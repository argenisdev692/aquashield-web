<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\FacebookLeadFormRequest;
use App\Models\Appointment;
use App\Jobs\ProcessNewLead;
use App\Services\FacebookConversionApi;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Throwable;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AppointmentCollection;
use App\Traits\CacheTrait;

class FacebookLeadFormController extends Controller
{
    use CacheTrait;
    
    protected TransactionService $transactionService;
    public $search = '';
    public $sortField = 'registration_date';
    public $sortDirection = 'desc';
    public $perPage = 15;
    public $showDeleted = false;
    protected $significantDataChange = false;

    // Inject TransactionService via constructor
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display the standalone Facebook lead form.
     */
    public function showForm()
    {
        $googleMapsApiKey = Cache::remember('google_maps_api_key', 3600, function() {
            return config('services.google.maps_api_key');
        });
        
        $recaptchaSiteKey = Cache::remember('recaptcha_site_key', 3600, function() {
            return config('captcha.sitekey');
        });

        return view('facebook-lead-form', [
            'googleMapsApiKey' => $googleMapsApiKey,
            'recaptchaSiteKey' => $recaptchaSiteKey
        ]);
    }

    /**
     * Display the appointment confirmation page.
     */
    public function showConfirmation()
    {
        return view('appointment-confirmation')->with('appointment_success', 'Your appointment request has been received! We will contact you shortly to schedule your free inspection.');
    }

    /**
     * Store the submitted lead data via AJAX.
     */
    public function store(FacebookLeadFormRequest $request)
    {
        $validatedData = $request->validated();
        
        // Verify reCAPTCHA token with stricter score (0.7 instead of default 0.5)
        $recaptchaToken = $request->input('g-recaptcha-response');
        if (empty($recaptchaToken)) {
            Log::warning('reCAPTCHA token is missing in the request', [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'CAPTCHA verification is required, but no token was provided.',
                'errors' => ['g-recaptcha-response' => ['Please verify you are not a robot.']]
            ], 422);
        }
        
        // Log the incoming token for debugging
        Log::info('Processing reCAPTCHA token', [
            'token_length' => strlen($recaptchaToken),
            'ip' => $request->ip()
        ]);
        
        $recaptchaVerification = $this->verifyRecaptchaToken($recaptchaToken);
        if (!$recaptchaVerification['success']) {
            return response()->json([
                'success' => false,
                'message' => 'reCAPTCHA verification failed: ' . ($recaptchaVerification['message'] ?? 'Please try again.'),
                'errors' => ['g-recaptcha-response' => [$recaptchaVerification['message'] ?? 'CAPTCHA verification failed.']]
            ], 422);
        }

        // Check for spam patterns by IP address
        if ($this->isLikelySpam($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Your submission has been flagged. Please contact us directly by phone.',
                'errors' => ['general' => ['Your submission has been flagged as potentially automated. Please contact us directly.']]
            ], 422);
        }

        try {
            // Check if email already exists in appointments
            $existingAppointment = Appointment::where('email', $validatedData['email'])->first();
            
            if ($existingAppointment) {
                return response()->json([
                    'success' => false,
                    'duplicate_email' => true,
                    'message' => 'This email is already registered in our system. Please contact our support team or call us to schedule your appointment.',
                    'errors' => ['email' => ['This email is already registered. Please contact support to schedule your appointment.']]
                ], 422);
            }

            $appointment = $this->transactionService->run(
                // Database operations
                function () use ($validatedData) {
                    Log::info('Creating appointment with data:', [
                        'first_name' => $validatedData['first_name'],
                        'status_lead' => 'New'
                    ]);
                
                    $newAppointment = Appointment::create([
                        'uuid' => Str::uuid(),
                        'first_name' => $validatedData['first_name'],
                        'last_name' => $validatedData['last_name'],
                        'phone' => $validatedData['phone'],
                        'email' => $validatedData['email'],
                        'address' => $validatedData['address'],
                        'address_2' => $validatedData['address_2'] ?? null,
                        'city' => $validatedData['city'],
                        'state' => $validatedData['state'],
                        'zipcode' => $validatedData['zipcode'],
                        'country' => $validatedData['country'],
                        'insurance_property' => $validatedData['insurance_property'] === 'yes',
                        'message' => $validatedData['message'] ?? null,
                        'sms_consent' => filter_var($validatedData['sms_consent'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'registration_date' => Carbon::now(),
                        'inspection_status' => 'Pending',
                        'status_lead' => 'New',
                        'latitude' => $validatedData['latitude'] ?? null,
                        'longitude' => $validatedData['longitude'] ?? null,
                        'lead_source' => $validatedData['lead_source'] ?? 'Facebook Ads'
                    ]);

                    Log::info('Appointment created:', [
                        'id' => $newAppointment->id,
                        'status_lead' => $newAppointment->status_lead,
                        'fresh_status' => $newAppointment->fresh()->status_lead
                    ]);

                    $this->significantDataChange = true;
                    $this->clearCache('appointments');

                    Log::info('Lead successfully created via Facebook Form.', ['email' => $validatedData['email']]);

                    return $newAppointment;
                },
                // Post-Commit actions
                function ($createdAppointment) {
                    Log::info('Transaction committed for Facebook Form Appointment.', ['appointment_uuid' => $createdAppointment->uuid]);
                    ProcessNewLead::dispatch($createdAppointment);

                    try {
                        $fbApi = app(FacebookConversionApi::class);
                        $fbApi->lead([
                            'email' => $createdAppointment->email,
                            'phone' => $createdAppointment->phone,
                            'first_name' => $createdAppointment->first_name,
                            'last_name' => $createdAppointment->last_name,
                            'address' => $createdAppointment->address,
                            'address_2' => $createdAppointment->address_2,
                            'city' => $createdAppointment->city,
                            'state' => $createdAppointment->state,
                            'zip_code' => $createdAppointment->zipcode,
                            'country' => $createdAppointment->country,
                        ], [
                            'content_name' => 'Appointment Request (Facebook Form)',
                            'content_type' => 'Roof Inspection Service',
                            'content_id' => 'appointment_request_fb',
                            'event_id' => 'appointment_fb_' . time() . '_' . substr(md5($createdAppointment->email), 0, 8),
                        ]);
                        Log::info('Facebook Lead event tracked successfully.', ['appointment_uuid' => $createdAppointment->uuid]);
                    } catch (Throwable $fbError) {
                        Log::error('Failed to track Facebook Lead event.', [
                            'appointment_uuid' => $createdAppointment->uuid,
                            'error_message' => $fbError->getMessage()
                        ]);
                    }
                },
                // Error actions (before rollback)
                function (Throwable $e) use ($validatedData) {
                    Log::error('Error occurred during Facebook Form transaction.', [
                        'error_message' => $e->getMessage(),
                        'email' => $validatedData['email'] ?? 'N/A'
                    ]);
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Your request has been submitted successfully!',
                'redirectUrl' => route('facebook.confirmation')
            ]);

        } catch (Throwable $e) {
            Log::error('Failed to process Facebook lead form submission.', [
                'error_message' => $e->getMessage(),
                'email' => $validatedData['email'] ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    /**
     * Validate a single field via AJAX.
     */
    public function validateField(Request $request)
    {
        $fieldName = $request->input('fieldName');
        $fieldValue = $request->input('fieldValue');

        if (!$fieldName) {
            return response()->json(['error' => 'Field name not provided.'], 400);
        }

        $formRequest = new FacebookLeadFormRequest();
        $rules = $formRequest->rules();
        $messages = $formRequest->messages();

        if (!isset($rules[$fieldName])) {
            return response()->json(['error' => 'Invalid field name.'], 400);
        }

        // Filter messages to only include those for the current field
        $fieldMessages = array_filter($messages, function($key) use ($fieldName) {
            return strpos($key, $fieldName . '.') === 0;
        }, ARRAY_FILTER_USE_KEY);

        $validator = Validator::make([$fieldName => $fieldValue], [$fieldName => $rules[$fieldName]], $fieldMessages);

        if ($validator->fails()) {
            return response()->json(['valid' => false, 'errors' => $validator->errors()->get($fieldName)], 422);
        } else {
            // Verificar específicamente si es el campo email y ya existe en la base de datos
            if ($fieldName === 'email' && !empty($fieldValue)) {
                $existingEmail = Appointment::where('email', $fieldValue)->exists();
                if ($existingEmail) {
                    return response()->json([
                        'valid' => false, 
                        'errors' => [__('email_already_registered')],
                        'duplicate_email' => true
                    ], 422);
                }
            }
            
            return response()->json(['valid' => true]);
        }
    }

    /**
     * API endpoint to store lead data from external services.
     */
    public function storeApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'min:2', 'regex:/^[A-Za-z\'-]+$/'],
            'last_name' => ['required', 'min:2', 'regex:/^[A-Za-z\'-]+$/'],
            'phone' => 'required|regex:/^\(\d{3}\)\s\d{3}-\d{4}$/',
            'email' => 'required|email',
            'address' => 'required|min:5',
            'address_2' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required|digits:5',
            'country' => 'required',
            'insurance_property' => 'required|string',
            'intent_to_claim' => 'nullable|string',
            'message' => 'nullable|min:5',
            'notes' => 'nullable|string',
            'damage_detail' => 'nullable|string',
            'sms_consent' => 'nullable|boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'lead_source' => 'nullable|string',
            'status_lead' => 'nullable|string|in:New,Called,Pending,Declined',
            'inspection_date' => 'nullable|date|required_with:inspection_time',
            'inspection_time' => 'nullable|date_format:H:i|required_with:inspection_date',
            'api_key' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        
        // Usar directamente la variable de entorno en lugar de cache y config
        $apiKey = env('API_KEY_STORE_API_REST');
        
        if ($validatedData['api_key'] !== $apiKey) {
            return response()->json([
                'success' => false,
                'message' => __('invalid_api_key')
            ], 401);
        }
        
        unset($validatedData['api_key']);

        try {
            // Check if email already exists in appointments
            $existingAppointment = Appointment::where('email', $validatedData['email'])->first();
            
            if ($existingAppointment) {
                return response()->json([
                    'success' => false,
                    'duplicate_email' => true,
                    'message' => __('email_already_registered'),
                    'data' => $existingAppointment ? new AppointmentResource($existingAppointment) : null
                ], 422);
            }
            
            $appointment = $this->transactionService->run(
                // Database operations
                function () use ($validatedData) {
                    Log::info('Creating API appointment with data:', [
                        'first_name' => $validatedData['first_name'],
                        'status_lead_input' => $validatedData['status_lead'] ?? 'Not provided', 
                        'status_lead_default' => 'New'
                    ]);
                
                    // Convertir insurance_property a boolean correctamente aceptando diferentes variaciones
                    $insuranceProperty = false;
                    if (is_string($validatedData['insurance_property'])) {
                        $value = strtolower($validatedData['insurance_property']);
                        $insuranceProperty = in_array($value, ['yes', 'y', 'true', '1', 'si', 'sí']);
                    } elseif (is_bool($validatedData['insurance_property'])) {
                        $insuranceProperty = $validatedData['insurance_property'];
                    } elseif (is_numeric($validatedData['insurance_property'])) {
                        $insuranceProperty = (bool)$validatedData['insurance_property'];
                    }
                    
                    // Convertir intent_to_claim a boolean con la misma lógica
                    $intentToClaim = false;
                    if (isset($validatedData['intent_to_claim'])) {
                        if (is_string($validatedData['intent_to_claim'])) {
                            $value = strtolower($validatedData['intent_to_claim']);
                            $intentToClaim = in_array($value, ['yes', 'y', 'true', '1', 'si', 'sí']);
                        } elseif (is_bool($validatedData['intent_to_claim'])) {
                            $intentToClaim = $validatedData['intent_to_claim'];
                        } elseif (is_numeric($validatedData['intent_to_claim'])) {
                            $intentToClaim = (bool)$validatedData['intent_to_claim'];
                        }
                    }

                    $newAppointment = Appointment::create([
                        'uuid' => Str::uuid(),
                        'first_name' => $validatedData['first_name'],
                        'last_name' => $validatedData['last_name'],
                        'phone' => $validatedData['phone'],
                        'email' => $validatedData['email'],
                        'address' => $validatedData['address'],
                        'address_2' => $validatedData['address_2'] ?? null,
                        'city' => $validatedData['city'],
                        'state' => $validatedData['state'],
                        'zipcode' => $validatedData['zipcode'],
                        'country' => $validatedData['country'],
                        'insurance_property' => $insuranceProperty,
                        'intent_to_claim' => $intentToClaim,
                        'message' => $validatedData['message'] ?? null,
                        'notes' => $validatedData['notes'] ?? null,
                        'damage_detail' => $validatedData['damage_detail'] ?? null,
                        'sms_consent' => filter_var($validatedData['sms_consent'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'registration_date' => Carbon::now(),
                        'inspection_status' => 'Pending',
                        'status_lead' => 'New',
                        'latitude' => $validatedData['latitude'] ?? null,
                        'longitude' => $validatedData['longitude'] ?? null,
                        'lead_source' => $validatedData['lead_source'] ?? 'Retell AI'
                    ]);

                    Log::info('API Appointment created:', [
                        'id' => $newAppointment->id,
                        'status_lead' => $newAppointment->status_lead,
                        'fresh_status' => $newAppointment->fresh()->status_lead
                    ]);

                    $this->significantDataChange = true;
                    $this->clearCache('appointments');

                    Log::info('API Lead successfully created.', ['email' => $validatedData['email']]);

                    return $newAppointment;
                },
                // Post-Commit actions
                function ($createdAppointment) {
                    Log::info('API Transaction committed for Appointment.', ['appointment_uuid' => $createdAppointment->uuid]);
                    ProcessNewLead::dispatch($createdAppointment);
                },
                // Error actions (before rollback)
                function (Throwable $e) use ($validatedData) {
                    Log::error('Error occurred during API transaction.', [
                        'error_message' => $e->getMessage(),
                        'email' => $validatedData['email'] ?? 'N/A'
                    ]);
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Lead successfully created',
                'data' => new AppointmentResource($appointment)
            ], 200);

        } catch (Throwable $e) {
            Log::error('Failed to process API lead submission.', [
                'error_message' => $e->getMessage(),
                'email' => $validatedData['email'] ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
    }

    /**
     * API endpoint to get all leads or check availability.
     */
    public function getAllLeads(Request $request)
    {
        // Validación de API key
        $apiKey = env('API_KEY_STORE_API_REST');
        
        if ($request->header('X-API-KEY') !== $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        try {
            // Si se está reprogramando una cita
            if ($request->has('reschedule')) {
                return $this->rescheduleAppointment($request);
            }
            
            // Si se solicita disponibilidad
            if ($request->has('availability')) {
                // Si es para un cliente específico
                if ($request->has('email') || $request->has('phone')) {
                    return $this->getClientAppointments($request);
                }
                
                // Fechas a consultar (convertir de MM-DD-YYYY a YYYY-MM-DD para procesamiento interno)
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                
                // Convertir formato de fecha si es necesario
                if ($startDate && preg_match('/^\d{2}-\d{2}-\d{4}$/', $startDate)) {
                    $startDate = $this->convertDateFormat($startDate);
                }
                
                if ($endDate && preg_match('/^\d{2}-\d{2}-\d{4}$/', $endDate)) {
                    $endDate = $this->convertDateFormat($endDate);
                }
                
                // Si no hay fechas, usar fecha actual
                if (!$startDate) {
                    $startDate = Carbon::now()->format('Y-m-d');
                }
                
                // Si solo hay fecha de inicio, determinar el rango automáticamente
                if (!$endDate) {
                    // Si la fecha de inicio parece ser un año-mes (YYYY-MM)
                    if (preg_match('/^\d{4}-\d{2}$/', $startDate)) {
                        // Si es mes completo: usar todo el mes
                        list($year, $month) = explode('-', $startDate);
                        $startDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
                        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
                    } else {
                        // Si es fecha específica: usar solo ese día
                        $endDate = Carbon::parse($startDate)->format('Y-m-d');
                    }
                }
                
                // Obtener todos los slots disponibles para el rango de fechas
                $availableSlots = $this->getCalendarAvailability($startDate, $endDate);
                
                return response()->json([
                    'success' => true,
                    'data' => $availableSlots,
                    'period' => [
                        'start_date' => $this->formatDateForDisplay($startDate),
                        'end_date' => $this->formatDateForDisplay($endDate)
                    ]
                ]);
            }
            
            // Consulta normal de appointments
            $this->perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $this->search = $request->input('search', '');
            
            $filters = [];
            if ($request->has('status')) {
                $filters['inspection_status'] = $request->input('status');
            }
            
            $cacheKey = $this->generateCacheKey('appointments', $page);
            
            $leads = Cache::remember($cacheKey, 300, function() use ($filters, $page, $request) {
                $query = Appointment::query();
                
                if (!empty($this->search)) {
                    $searchTerm = '%' . $this->search . '%';
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('first_name', 'like', $searchTerm)
                          ->orWhere('last_name', 'like', $searchTerm)
                          ->orWhere('email', 'like', $searchTerm)
                          ->orWhere('phone', 'like', $searchTerm)
                          ->orWhere('address', 'like', $searchTerm);
                    });
                }
                
                // Filtrado por fecha si se especifica
                if ($request->has('from_date')) {
                    $fromDate = $request->input('from_date');
                    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $fromDate)) {
                        $fromDate = $this->convertDateFormat($fromDate);
                    }
                    $query->whereDate('inspection_date', '>=', $fromDate);
                }
                
                if ($request->has('to_date')) {
                    $toDate = $request->input('to_date');
                    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $toDate)) {
                        $toDate = $this->convertDateFormat($toDate);
                    }
                    $query->whereDate('inspection_date', '<=', $toDate);
                }
                
                foreach ($filters as $column => $value) {
                    $query->where($column, $value);
                }
                
                return $query->orderBy($this->sortField, $this->sortDirection)
                           ->paginate($this->perPage, ['*'], 'page', $page);
            });
            
            return response()->json([
                'success' => true,
                'data' => new AppointmentCollection($leads),
            ]);
            
        } catch (Throwable $e) {
            Log::error('Failed to fetch leads or availability via API.', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
    }
    
    /**
     * Reprogramar una cita con nombre, apellido y teléfono
     */
    private function rescheduleAppointment(Request $request)
    {
        // Validar los campos requeridos
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'new_date' => 'required|string',
            'new_time' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('missing_required_fields'),
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Buscar la cita por nombre, apellido y teléfono
        $appointment = Appointment::where('first_name', $request->input('first_name'))
            ->where('last_name', $request->input('last_name'))
            ->where('phone', $request->input('phone'))
            ->whereIn('inspection_status', ['Pending', 'Confirmed'])
            ->first();
        
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => __('no_appointment_found')
            ], 404);
        }
        
        // Convertir la fecha si viene en formato MM-DD-YYYY
        $newDate = $request->input('new_date');
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $newDate)) {
            $newDate = $this->convertDateFormat($newDate);
        }
        
        // Verificar si el nuevo slot está disponible
        $isAvailable = $this->isTimeSlotAvailable($newDate, $request->input('new_time'));
        
        if (!$isAvailable) {
            return response()->json([
                'success' => false,
                'message' => __('time_slot_not_available')
            ], 409);
        }
        
        try {
            // Actualizar la cita
            $appointment->inspection_date = $newDate;
            $appointment->inspection_time = $request->input('new_time');
            $appointment->save();
            
            // Limpiar caché
            $this->clearCache('appointments');
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment rescheduled successfully',
                'data' => new AppointmentResource($appointment)
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to reschedule appointment', [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reschedule appointment'
            ], 500);
        }
    }
    
    /**
     * Verifica si un slot de tiempo específico está disponible
     */
    private function isTimeSlotAvailable($date, $time)
    {
        // Verificar si la fecha es domingo
        if (Carbon::parse($date)->dayOfWeek === CarbonInterface::SUNDAY) {
            return false; // Domingos no disponibles
        }
        
        // Horario de trabajo: 8 AM - 6 PM
        $workingHours = [
            '08:00', '09:00', '10:00', '11:00', '12:00', 
            '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];
        
        // Si la hora no está en el horario de trabajo
        if (!in_array($time, $workingHours)) {
            return false;
        }
        
        // Buscar citas existentes para esa fecha y hora
        $existingAppointment = Appointment::whereDate('inspection_date', $date)
            ->whereTime('inspection_time', $time)
            ->whereIn('inspection_status', ['Confirmed', 'Pending'])
            ->first();
        
        // Si no hay cita existente, el slot está disponible
        return $existingAppointment === null;
    }
    
    /**
     * Obtiene las citas existentes de un cliente
     */
    private function getClientAppointments(Request $request)
    {
        $query = Appointment::query();
        
        if ($request->has('email')) {
            $query->where('email', $request->input('email'));
        }
        
        if ($request->has('phone')) {
            $query->where('phone', $request->input('phone'));
        }
        
        if ($request->has('first_name') && $request->has('last_name')) {
            $query->where('first_name', $request->input('first_name'))
                  ->where('last_name', $request->input('last_name'));
        }
        
        $appointments = $query->orderBy('created_at', 'desc')->get();
        
        // Formatear fechas para la respuesta
        $formattedAppointments = $appointments->map(function($appointment) {
            if ($appointment->inspection_date) {
                $appointment->formatted_inspection_date = $this->formatDateForDisplay($appointment->inspection_date);
            }
            return $appointment;
        });
        
        return response()->json([
            'success' => true,
            'data' => new AppointmentCollection($formattedAppointments),
            'message' => 'Client appointments retrieved successfully'
        ]);
    }
    
    /**
     * Convierte fecha de MM-DD-YYYY a YYYY-MM-DD
     */
    private function convertDateFormat($date)
    {
        if (empty($date)) return null;
        
        // Si ya está en formato YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        
        // Convertir de MM-DD-YYYY a YYYY-MM-DD
        $parts = explode('-', $date);
        if (count($parts) === 3) {
            return $parts[2] . '-' . $parts[0] . '-' . $parts[1];
        }
        
        return $date;
    }
    
    /**
     * Formatea fecha de YYYY-MM-DD a MM-DD-YYYY para mostrar
     */
    private function formatDateForDisplay($date)
    {
        if (empty($date)) return null;
        
        if ($date instanceof Carbon) {
            return $date->format('m-d-Y');
        }
        
        // Si es string en formato YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $parts = explode('-', $date);
            return $parts[1] . '-' . $parts[2] . '-' . $parts[0];
        }
        
        return $date;
    }
    
    /**
     * Obtiene la disponibilidad del calendario para un rango de fechas
     * 
     * @param string $startDate Fecha de inicio (YYYY-MM-DD)
     * @param string $endDate Fecha de fin (YYYY-MM-DD)
     * @return array Arreglo con días y horas disponibles
     */
    private function getCalendarAvailability($startDate, $endDate)
    {
        // Determinar horario de trabajo
        $workingHours = [
            '08:00', '09:00', '10:00', '11:00', '12:00', 
            '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];
        
        // Obtener las citas existentes
        $existingAppointments = Appointment::whereNotNull('inspection_date')
            ->whereNotNull('inspection_time')
            ->whereDate('inspection_date', '>=', $startDate)
            ->whereDate('inspection_date', '<=', $endDate)
            ->whereIn('inspection_status', ['Confirmed', 'Pending'])
            ->get(['inspection_date', 'inspection_time'])
            ->groupBy(function($appointment) {
                return $appointment->inspection_date->format('Y-m-d');
            });
        
        // Inicializar el resultado
        $calendar = [
            'days' => []
        ];
        
        // Recorrer cada día del rango
        $currentDate = Carbon::parse($startDate);
        $lastDate = Carbon::parse($endDate);
        
        while ($currentDate->lte($lastDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->dayOfWeek;
            
            // Omitir domingos
            if ($dayOfWeek !== CarbonInterface::SUNDAY) {
                $daySlots = [];
                
                // Para cada hora de trabajo
                foreach ($workingHours as $time) {
                    $isReserved = false;
                    
                    // Verificar si ya hay cita
                    if (isset($existingAppointments[$dateStr])) {
                        foreach ($existingAppointments[$dateStr] as $appointment) {
                            if ($appointment->inspection_time->format('H:i') === $time) {
                                $isReserved = true;
                                break;
                            }
                        }
                    }
                    
                    // Añadir el slot al día
                    $daySlots[] = [
                        'time' => $time,
                        'formatted_time' => Carbon::parse($time)->format('h:i A'),
                        'available' => !$isReserved
                    ];
                }
                
                // Añadir el día al calendario con formato MM-DD-YYYY
                $calendar['days'][] = [
                    'date' => $dateStr,
                    'formatted_date' => $this->formatDateForDisplay($dateStr),
                    'day_of_week' => $currentDate->format('l'),
                    'month_day' => $currentDate->format('F j'),
                    'slots' => $daySlots
                ];
            }
            
            $currentDate->addDay();
        }
        
        return $calendar;
    }

    /**
     * Clear caches related to appointments - Legacy method
     */
    private function clearAppointmentCache()
    {
        $this->clearCache('appointments');
        
        $specificCacheKeys = [
            'appointments_count', 
            'appointments_pending',
            'appointments_recent'
        ];

        foreach ($specificCacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Verify reCAPTCHA token with a higher score threshold.
     * @return array Success status and message
     */
    private function verifyRecaptchaToken($token)
    {
        if (empty($token)) {
            Log::warning('reCAPTCHA token is empty');
            return [
                'success' => false, 
                'message' => 'Missing reCAPTCHA token'
            ];
        }

        $client = new \GuzzleHttp\Client();
        $secret = config('captcha.secret');
        
        // Log token length and partial token for debugging
        Log::info('reCAPTCHA verification attempt', [
            'token_length' => strlen($token),
            'token_prefix' => substr($token, 0, 10) . '...',
            'secret_key_length' => strlen($secret),
            'secret_key_prefix' => substr($secret, 0, 5) . '...',
            'ip' => request()->ip()
        ]);
        
        try {
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => request()->ip()
                ]
            ]);

            $body = json_decode((string) $response->getBody(), true);
            
            // Log the full response for debugging
            Log::info('reCAPTCHA response', [
                'success' => $body['success'] ?? false,
                'score' => $body['score'] ?? 'N/A',
                'action' => $body['action'] ?? 'N/A',
                'hostname' => $body['hostname'] ?? 'N/A',
                'error_codes' => $body['error-codes'] ?? []
            ]);
            
            // Use 0.5 as the threshold (default recommended by Google)
            $isValid = isset($body['success']) && $body['success'] && (!isset($body['score']) || $body['score'] >= 0.5);
            
            // Construct detailed error message if available
            $message = 'CAPTCHA verification failed';
            if (!$isValid && isset($body['error-codes']) && !empty($body['error-codes'])) {
                $errorCodes = is_array($body['error-codes']) ? implode(', ', $body['error-codes']) : $body['error-codes'];
                $message .= ': ' . $errorCodes;
            } elseif (!$isValid && isset($body['score']) && $body['score'] < 0.5) {
                $message .= ': Low confidence score';
            }
            
            return [
                'success' => $isValid,
                'message' => $isValid ? 'Verification successful' : $message,
                'details' => $body
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'reCAPTCHA verification server error: ' . $e->getMessage(),
                'exception' => get_class($e)
            ];
        }
    }

    /**
     * Check if submission is likely spam based on various heuristics.
     */
    private function isLikelySpam(Request $request)
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $data = $request->all();
        
        // Rate limiting: Check submissions count from this IP in the last hour
        $cacheKey = 'form_submissions_' . md5($ip);
        $submissions = Cache::get($cacheKey, 0);
        
        if ($submissions >= 3) {
            // More than 3 submissions in an hour from same IP
            Log::warning('Excessive form submissions detected', [
                'ip' => $ip,
                'count' => $submissions
            ]);
            return true;
        }
        
        // Increment submission count
        Cache::put($cacheKey, $submissions + 1, 60 * 60); // Store for 1 hour
        
        // Check for timing - submissions completed too quickly are suspicious
        $timeStarted = $request->session()->get('form_start_time');
        if ($timeStarted && (time() - $timeStarted < 10)) {
            // Form completed in less than 10 seconds - likely a bot
            Log::warning('Form submitted too quickly', [
                'ip' => $ip,
                'time_taken' => time() - $timeStarted
            ]);
            return true;
        }
        
        // Check for suspicious user agents
        $suspiciousUserAgents = ['curl', 'wget', 'python-requests', 'go-http-client', 'scrapy'];
        foreach ($suspiciousUserAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                Log::warning('Suspicious user agent detected', [
                    'ip' => $ip,
                    'user_agent' => $userAgent
                ]);
                return true;
            }
        }
        
        // Look for identical submissions (all fields exactly the same)
        $submissionHash = md5(json_encode($data));
        $recentSubmissions = Cache::get('recent_submission_hashes', []);
        
        if (in_array($submissionHash, $recentSubmissions)) {
            Log::warning('Duplicate form submission detected', [
                'ip' => $ip
            ]);
            return true;
        }
        
        // Store this submission hash
        $recentSubmissions[] = $submissionHash;
        Cache::put('recent_submission_hashes', array_slice($recentSubmissions, -10), 60 * 60 * 24);
        
        return false;
    }
}
