<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Services\TransactionService;
use App\Jobs\ProcessNewLead;
use App\Jobs\ProcessRejectionNotifications;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentsExport;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use App\Traits\CacheTrait;
use App\Jobs\ProcessAppointmentEmail;

class AppointmentController extends BaseCrudController
{
    use CacheTrait;
    
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $showDeleted = false;
    protected $significantDataChange = false;
    
    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        $this->modelClass = Appointment::class;
        $this->entityName = 'APPOINTMENT';
        $this->viewPrefix = 'appointments';
        $this->routePrefix = 'appointments';
    }

    /**
     * Get validation rules for appointment
     */
    protected function getValidationRules($id = null)
    {
        $emailRule = 'required|email|max:255|unique:appointments,email';
        
        // If we have an ID (UUID in this case), exclude it from the unique check
        if ($id) {
            $emailRule .= ',' . $id . ',uuid';
        }
        
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\'-]+$/'],
            'email' => $emailRule,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'address_map_input' => 'nullable|string|max:255',
            'address_simple' => 'nullable|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zipcode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'insurance_property' => 'required|in:0,1',
            'message' => 'nullable|string',
            'sms_consent' => 'nullable|boolean',
            'registration_date' => 'nullable|date',
            'inspection_date' => 'nullable|date|after_or_equal:today|required_with:inspection_time',
            'inspection_time' => 'nullable|date_format:H:i|required_with:inspection_date',
            'notes' => 'nullable|string',
            'owner' => 'nullable|string|max:255',
            'damage_detail' => 'nullable|string',
            'intent_to_claim' => 'nullable|boolean',
            'lead_source' => 'required|in:Website,Facebook Ads,Reference,Retell AI',
            'additional_note' => 'nullable|string',
            'inspection_status' => 'nullable|in:Completed,Pending,Declined,Confirmed',
            'status_lead' => 'nullable|in:New,Called,Pending,Declined',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Get validation messages for appointment
     */
    protected function getValidationMessages()
    {
        return [
            'first_name.required' => __('first_name_required'),
            'first_name.max' => __('first_name_max'),
            'first_name.regex' => __('first_name_regex'),
            'last_name.required' => __('last_name_required'),
            'last_name.max' => __('last_name_max'),
            'last_name.regex' => __('last_name_regex'),
            'email.required' => __('email_required'),
            'email.email' => __('email_invalid'),
            'email.unique' => __('email_unique'),
            'email.max' => __('email_max'),
            'phone.required' => __('phone_required'),
            'phone.max' => __('phone_max'),
            'address.required' => __('address_required'),
            'address.max' => __('address_max'),
            'address_2.max' => __('address_2_max'),
            'city.required' => __('city_required'),
            'city.max' => __('city_max'),
            'state.required' => __('state_required'),
            'state.max' => __('state_max'),
            'zipcode.required' => __('zipcode_required'),
            'zipcode.max' => __('zipcode_max'),
            'country.required' => __('country_required'),
            'country.max' => __('country_max'),
            'insurance_property.required' => __('insurance_property_required'),
            'insurance_property.in' => __('insurance_property_invalid_value'),
            'lead_source.required' => __('lead_source_required'),
            'lead_source.in' => __('lead_source_in'),
            'sms_consent.boolean' => __('sms_consent_boolean'),
            'registration_date.date' => __('registration_date_date'),
            'inspection_date.date' => __('inspection_date_date'),
            'inspection_date.after_or_equal' => __('inspection_date_after_or_equal'),
            'inspection_date.required_with' => __('inspection_date_required_with'),
            'inspection_time.date_format' => __('inspection_time_date_format'),
            'inspection_time.required_with' => __('inspection_time_required_with'),
            'intent_to_claim.boolean' => __('intent_to_claim_boolean'),
            'inspection_status.in' => __('inspection_status_in'),
            'status_lead.in' => __('status_lead_in'),
            'latitude.between' => __('latitude_between'),
            'longitude.between' => __('longitude_between'),
        ];
    }

    /**
     * Prepare data for storing an appointment
     */
    protected function prepareStoreData(Request $request)
    {
        return [
            'uuid' => (string) Str::uuid(),
            'first_name' => ucfirst(strtolower($request->first_name)),
            'last_name' => ucfirst(strtolower($request->last_name)),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'address_2' => $request->address_2,
            'city' => $request->city,
            'state' => $request->state,
            'zipcode' => $request->zipcode,
            'country' => $request->country,
            'insurance_property' => $request->insurance_property ?? false,
            'message' => $request->message,
            'sms_consent' => $request->sms_consent ?? false,
            'registration_date' => $request->registration_date,
            'inspection_date' => $request->inspection_date,
            'inspection_time' => $request->inspection_time,
            'notes' => $request->notes,
            'owner' => $request->owner ? ucwords(strtolower($request->owner)) : null,
            'damage_detail' => $request->damage_detail,
            'intent_to_claim' => $request->intent_to_claim ?? false,
            'lead_source' => $request->lead_source,
            'additional_note' => $request->additional_note,
            'inspection_status' => $request->inspection_status ?? 'Pending',
            'status_lead' => $request->status_lead ?? 'New',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];
    }

    /**
     * Prepare data for updating an appointment
     */
    protected function prepareUpdateData(Request $request)
    {
        return array_filter([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'address_2' => $request->address_2,
            'city' => $request->city,
            'state' => $request->state,
            'zipcode' => $request->zipcode,
            'country' => $request->country,
            'insurance_property' => $request->insurance_property ?? false,
            'message' => $request->message,
            'sms_consent' => $request->sms_consent ?? false,
            'registration_date' => $request->registration_date,
            'inspection_date' => $request->inspection_date,
            'inspection_time' => $request->inspection_time,
            'notes' => $request->notes,
            'owner' => $request->owner ? ucwords(strtolower($request->owner)) : null,
            'damage_detail' => $request->damage_detail,
            'intent_to_claim' => $request->intent_to_claim ?? false,
            'lead_source' => $request->lead_source,
            'additional_note' => $request->additional_note,
            'inspection_status' => $request->inspection_status,
            'status_lead' => $request->status_lead,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ], fn ($value) => !is_null($value));
    }

    /**
     * Display a listing of the appointments
     */
    public function index(Request $request)
    {
        try {
            // Set up cache and search parameters
            $this->search = $request->input('search', '');
            $this->sortField = $request->input('sort_field', 'created_at');
            $this->sortDirection = $request->input('sort_direction', 'desc');
            $this->perPage = $request->input('per_page', 10);
            $this->showDeleted = $request->input('show_deleted', 'false') === 'true';
            
            // Add debug logging
            Log::info('AppointmentController::index - Request parameters:', [
                'all_params' => $request->all(),
                'search_param' => $this->search,
                'has_search' => $request->has('search'),
                'is_empty' => empty($this->search)
            ]);
            
            $page = $request->input('page', 1);
            $cacheKey = $this->generateCacheKey('appointments', $page);
            
            // Handle Excel export (skip cache for exports)
            if ($request->has('export') && $request->export === 'excel') {
                $query = $this->buildAppointmentsQuery($request);
                
                Log::info('Exporting appointments to Excel', [
                    'filter_count' => $query->count(),
                    'request_params' => $request->all()
                ]);
                
                $filename = 'appointments_export_' . date('Y-m-d_His') . '.xlsx';
                return Excel::download(new AppointmentsExport($query), $filename);
            }
            
            // Use cache for normal views
            $appointments = Cache::remember($cacheKey, 15, function() use ($request, $page) {
                $query = $this->buildAppointmentsQuery($request);
                
                // Pagination
                return $query->paginate($this->perPage, ['*'], 'page', $page);
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $appointments->items(),
                    'current_page' => $appointments->currentPage(),
                    'last_page' => $appointments->lastPage(),
                    'from' => $appointments->firstItem(),
                    'to' => $appointments->lastItem(),
                    'total' => $appointments->total(),
                ]);
            }

            return view("{$this->viewPrefix}.index", [
                'appointments' => $appointments,
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error listing {$this->entityName}s: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error listing {$this->entityName}s",
                ], 500);
            }

            return back()->with('error', "Error listing {$this->entityName}s");
        }
    }
    
    /**
     * Build appointments query based on request filters
     */
    private function buildAppointmentsQuery(Request $request)
    {
        $query = $this->modelClass::query();
        
        // Apply date range filters if provided
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('inspection_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('inspection_date', '<=', $request->end_date);
        }

        // Handle status_lead filter
        if ($request->has('status_lead_filter') && !empty($request->status_lead_filter)) {
            $query->where('status_lead', $request->status_lead_filter);
        }

        // Handle search
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('email', 'like', $searchTerm)
                  ->orWhere('first_name', 'like', $searchTerm)
                  ->orWhere('last_name', 'like', $searchTerm)
                  ->orWhere('status_lead', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm);
            });
        }

        // Handle soft deletes
        if ($this->showDeleted) {
            $query->withTrashed();
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        return $query;
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create()
    {
        try {
            if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create {$this->entityName}")) {
                return redirect()->route($this->routePrefix . '.index')->with('error', "Permission denied");
            }
            
            // Retorna la vista unificada de formulario sin un appointment
            return view("{$this->viewPrefix}.form", [
                'entityName' => $this->entityName,
                'appointment' => new Appointment(), // Appointment vacío para evitar errores con los condicionales
            ]);
        } catch (Throwable $e) {
            Log::error("Error showing create form for {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            
            return redirect()->route($this->routePrefix . '.index')->with('error', "Error loading create form");
        }
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validateRequest($request);

            $appointment = $this->transactionService->run(function () use ($data) {
                $preparedData = $this->prepareStoreData($data);
                return $this->modelClass::create($preparedData);
            }, function ($appointment) {
                Log::info("{$this->entityName} created successfully", ['id' => $appointment->id]);
                
                // Mark significant data change and clear cache
                $this->significantDataChange = true;
                $this->clearCache('appointments');
                
                $this->afterStore($appointment);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} created successfully",
                    'appointment' => $appointment,
                    'redirectUrl' => route("{$this->routePrefix}.index"),
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} created successfully");
        } catch (Throwable $e) {
            Log::error("Error creating {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error creating {$this->entityName}",
                    'errors' => $e instanceof ValidationException
                    ? $e->errors()
                    : [$e->getMessage()],
            ], $e instanceof ValidationException ? 422 : 500);
            }

            return back()->withErrors($e instanceof ValidationException
                ? $e->errors()
                : [$e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified appointment
     */
    public function edit($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $appointment = $this->modelClass::withTrashed()->where('uuid', $uuid)->first();

            if (!$appointment) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("{$this->entityName} not found");
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'appointment' => $appointment,
                ]);
            }

            // Usa la misma vista unificada pero con un appointment existente
            return view("{$this->viewPrefix}.form", [
                'appointment' => $appointment,
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error retrieving {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error retrieving {$this->entityName}",
                ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }

            return back()->with('error', "Error retrieving {$this->entityName}");
        }
    }

    /**
     * Update the specified appointment
     */
    public function update(Request $request, $uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $data = $this->validateRequest($request, $uuid);

            $appointment = $this->transactionService->run(function () use ($uuid, $data) {
                $appointment = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                $preparedData = $this->prepareUpdateData($data);
                $appointment->update($preparedData);
                return $appointment->fresh();
            }, function ($appointment) {
                Log::info("{$this->entityName} updated successfully", ['id' => $appointment->id]);
                
                // Mark significant data change and clear cache
                $this->significantDataChange = true;
                $this->clearCache('appointments');
                
                $this->afterUpdate($appointment);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully",
                    'appointment' => $appointment,
                    'redirectUrl' => route("{$this->routePrefix}.index"),
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} updated successfully");
        } catch (Throwable $e) {
            Log::error("Error updating {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error updating {$this->entityName}",
                    'errors' => $e instanceof ValidationException
                    ? $e->errors()
                    : [$e->getMessage()],
            ], $e instanceof ValidationException ? 422 : ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500));
            }

            return back()->withErrors($e instanceof ValidationException
                ? $e->errors()
                : [$e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified appointment (soft delete)
     */
    public function destroy($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $appointment = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                
                // Actualizar estado a Declined antes de eliminar
                $appointment->status_lead = 'Declined';
                $appointment->inspection_status = 'Declined';
                $appointment->save();
                
                // Realizar soft delete
                $appointment->delete();
                
                return $appointment;
            }, function ($appointment) {
                Log::info("{$this->entityName} deleted successfully", ['id' => $appointment->id]);
                
                // Mark significant data change and clear cache
                $this->significantDataChange = true;
                $this->clearCache('appointments');
                
                $this->afterDestroy($appointment);
            });

            return response()->json([
                'success' => true,
                'message' => "{$this->entityName} deleted successfully",
            ]);
        } catch (Throwable $e) {
            Log::error("Error deleting {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => "Error deleting {$this->entityName}",
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Restore a soft-deleted appointment
     */
    public function restore($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $appointment = $this->modelClass::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
                $appointment->restore();
                return $appointment;
            }, function ($appointment) {
                Log::info("{$this->entityName} restored successfully", ['id' => $appointment->id]);
                
                // Mark significant data change and clear cache
                $this->significantDataChange = true;
                $this->clearCache('appointments');
                
                $this->afterRestore($appointment);
            });

            return response()->json([
                'success' => true,
                'message' => "{$this->entityName} restored successfully",
            ]);
        } catch (Throwable $e) {
            Log::error("Error restoring {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => "Error restoring {$this->entityName}",
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Check if an email already exists for real-time validation
     */
    public function checkEmailExists(Request $request)
    {
        if (!$this->checkPermissionWithMessage("READ_APPOINTMENT", "You don't have permission to check appointment emails")) {
            return response()->json([
                'success' => false,
                'message' => 'Permission denied',
            ], 403);
        }

        try {
            $email = $request->input('email');
            $excludeUuid = $request->input('exclude_uuid');

            Log::info('Checking email existence', [
                'email' => $email,
                'exclude_uuid' => $excludeUuid
            ]);

            $query = Appointment::where('email', $email);

            if ($excludeUuid) {
                $query->where('uuid', '!=', $excludeUuid);
            }

            // Debug: Log the SQL query being executed
            Log::info('Email check SQL', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $exists = $query->exists();
            
            // Debug: Also get the actual records for debugging
            $records = $query->get(['uuid', 'email', 'deleted_at']);
            
            Log::info('Email check results', [
                'exists' => $exists,
                'records_found' => $records->count(),
                'records' => $records->toArray(),
                'also_checking_deleted' => $query->withTrashed()->exists()
            ]);

            return response()->json([
                'success' => true,
                'exists' => $exists,
            ]);
        } catch (Throwable $e) {
            Log::error("Error checking email existence: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error checking email existence',
            ], 500);
        }
    }

    /**
     * Check if a phone number already exists for real-time validation
     */
    public function checkPhoneExists(Request $request)
    {
        if (!$this->checkPermissionWithMessage("READ_APPOINTMENT", "You don't have permission to check appointment phones")) {
            return response()->json([
                'success' => false,
                'message' => 'Permission denied',
            ], 403);
        }

        try {
            $phone = $request->input('phone');
            $excludeUuid = $request->input('exclude_uuid');

            Log::info('Checking phone existence', [
                'phone' => $phone,
                'exclude_uuid' => $excludeUuid
            ]);

            $query = Appointment::where('phone', $phone);

            if ($excludeUuid) {
                $query->where('uuid', '!=', $excludeUuid);
            }

            // Debug: Log the SQL query being executed
            Log::info('Phone check SQL', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $exists = $query->exists();
            
            // Debug: Also get the actual records for debugging
            $records = $query->get(['uuid', 'phone', 'deleted_at']);
            
            Log::info('Phone check results', [
                'exists' => $exists,
                'records_found' => $records->count(),
                'records' => $records->toArray(),
                'also_checking_deleted' => $query->withTrashed()->exists()
            ]);

            return response()->json([
                'success' => true,
                'exists' => $exists,
            ]);
        } catch (Throwable $e) {
            Log::error("Error checking phone existence: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error checking phone existence',
            ], 500);
        }
    }

    /**
     * Validate the request data
     */
    protected function validateRequest(Request $request, $id = null)
    {
        $rules = $this->getValidationRules($id);
        $messages = $this->getValidationMessages();

        // Convertir insurance_property a booleano desde radio button values
        $insuranceValue = $request->input('insurance_property');
        $insuranceBoolean = false;
        
        if ($insuranceValue === '1' || $insuranceValue === 1 || $insuranceValue === true || $insuranceValue === 'true') {
            $insuranceBoolean = true;
        }
        
        $request->merge([
            'insurance_property' => $insuranceBoolean
        ]);

        // Handle inspection date and time relationship
        if ($request->has('inspection_date')) {
            if (empty($request->inspection_date)) {
                // If date is empty, time should be empty too
                $request->merge(['inspection_time' => null]);
            } else if ($request->has('inspection_time') && !empty($request->inspection_time)) {
                // Check for scheduling conflicts with other appointments
                $uuid = $id;
                $date = $request->inspection_date;
                $time = $request->inspection_time;
                
                $conflict = $this->checkScheduleConflict($date, $time, $uuid);
                
                if ($conflict) {
                    throw ValidationException::withMessages([
                        'schedule_conflict' => 'This time slot is already booked with another client. Please select a different date or time for your inspection.'
                    ]);
                }
                
                // If both inspection date and time are provided, set inspection_status to "Confirmed"
                $request->merge([
                    'inspection_status' => 'Confirmed'
                ]);
            }
        }

        // Handle inspection_status and status_lead relationship
        if ($request->has('inspection_status')) {
            $inspection_status = $request->inspection_status;
            
            if ($inspection_status === 'Declined') {
                // If inspection status is Declined, set status_lead to Declined
                $request->merge(['status_lead' => 'Declined']);
            } else if ($inspection_status === 'Confirmed' || $inspection_status === 'Completed') {
                // If inspection status is Confirmed or Completed, set status_lead to Called
                $request->merge(['status_lead' => 'Called']);
            } else if ($inspection_status === 'Pending') {
                // If inspection status is Pending and status_lead is not already Pending, set to New
                if ($request->status_lead !== 'Pending') {
                    $request->merge(['status_lead' => 'New']);
                }
            }
        } else {
            // Default inspection_status to Pending if not provided
            $request->merge(['inspection_status' => 'Pending', 'status_lead' => 'New']);
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $request;
    }
    
    /**
     * Check if there's a scheduling conflict with existing appointments
     * 
     * @param string $date Inspection date
     * @param string $time Inspection time
     * @param string|null $excludeUuid UUID of the appointment to exclude from check (when editing)
     * @return bool True if conflict exists, false otherwise
     */
    protected function checkScheduleConflict($date, $time, $excludeUuid = null)
    {
        // Query to find appointments on the same date and time
        $query = Appointment::where('inspection_date', $date)
            ->where('inspection_time', $time)
            ->whereNotIn('inspection_status', ['Declined', 'Cancelled']); // Only care about active appointments
        
        // Exclude the current appointment when editing
        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }
        
        // Check if any appointments exist at this date and time
        return $query->exists();
    }

    /**
     * Get search field for filtering
     */
    protected function getSearchField()
    {
        return 'email'; // Search by email for appointments
    }

    /**
     * Get name field for display
     */
    protected function getNameField()
    {
        return 'email'; // Use email as the primary identifier for validation
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity)
    {
        return $entity->first_name . ' ' . $entity->last_name; // Display full name
    }

    /**
     * Hook after storing an appointment
     */
    protected function afterStore($appointment)
    {
        // If appointment has inspection date and time, send confirmation email instead of new lead notification
        if ($appointment->inspection_status === 'Confirmed' && $appointment->inspection_date && $appointment->inspection_time) {
            ProcessAppointmentEmail::dispatch($appointment, 'confirmed');
            Log::info('Appointment confirmation email dispatched via creation', ['id' => $appointment->id]);
        } else {
            // Otherwise, send the standard new lead notification
            ProcessNewLead::dispatch($appointment);
        }
    }

    /**
     * Hook after updating an appointment
     */
    protected function afterUpdate($appointment)
    {
        // Add custom logic here, e.g., update related records
        // If this is a new lead (status changed to 'New'), notify admin and client
        if ($appointment->status_lead === 'New') {
            ProcessNewLead::dispatch($appointment);
        }
        // If the appointment has been confirmed (has date and time), send confirmation email
        else if ($appointment->inspection_status === 'Confirmed' && $appointment->inspection_date && $appointment->inspection_time) {
            ProcessAppointmentEmail::dispatch($appointment, 'confirmed');
            Log::info('Appointment confirmation email dispatched via update', ['id' => $appointment->id]);
        }
    }

    /**
     * Hook after deleting an appointment
     */
    protected function afterDestroy($appointment)
    {
        // Add custom logic here, e.g., log activity
    }

    /**
     * Hook after restoring an appointment
     */
    protected function afterRestore($appointment)
    {
        // Add custom logic here, e.g., notify user
    }

    /**
     * Send rejection notifications to multiple appointments
     */
    public function sendRejection(Request $request)
    {
        try {
            if (!$this->checkPermissionWithMessage("UPDATE_APPOINTMENT", "You don't have permission to update appointments")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied',
                ], 403);
            }

            // Registrar los datos recibidos para depuración
            Log::info('Rejection request data:', [
                'all_data' => $request->all(),
                'appointment_ids' => $request->input('appointment_ids'),
                'no_contact' => $request->input('no_contact'),
                'no_insurance' => $request->input('no_insurance'),
                'other_reason' => $request->input('other_reason'),
            ]);
            
            // Convertir strings "true"/"false" a valores booleanos antes de la validación
            $booleanFields = ['no_contact', 'no_insurance'];
            foreach ($booleanFields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    if ($value === 'true' || $value === '1') {
                        $request->merge([$field => true]);
                    } else if ($value === 'false' || $value === '0') {
                        $request->merge([$field => false]);
                    }
                }
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'appointment_ids' => 'required|array|min:1',
                'appointment_ids.*' => 'required|string',
                'no_contact' => 'sometimes|boolean',
                'no_insurance' => 'sometimes|boolean',
                'other_reason' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                // Registrar los errores de validación para depuración
                Log::error('Validation error in sendRejection:', [
                    'errors' => $validator->errors()->toArray(),
                    'data' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Get validated data
            $appointmentIds = $request->input('appointment_ids');
            $noContact = filter_var($request->input('no_contact', false), FILTER_VALIDATE_BOOLEAN);
            $noInsurance = filter_var($request->input('no_insurance', false), FILTER_VALIDATE_BOOLEAN);
            $otherReason = $request->input('other_reason');

            // Validate that at least one reason is provided
            if (!$noContact && !$noInsurance && empty($otherReason)) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one rejection reason must be provided',
                ], 422);
            }

            // Verificar que cada ID exista en la base de datos
            $existingAppointments = $this->modelClass::whereIn('uuid', $appointmentIds)->pluck('uuid')->toArray();
            $missingIds = array_diff($appointmentIds, $existingAppointments);
            
            if (!empty($missingIds)) {
                Log::warning('Some appointment IDs not found:', [
                    'missing_ids' => $missingIds
                ]);
                
                // Continuamos con las citas existentes
                $appointmentIds = $existingAppointments;
                
                if (empty($appointmentIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid appointment IDs provided',
                    ], 422);
                }
            }

            // Actualizar y mover a papelera todas las citas seleccionadas
            $processed = 0;
            $errors = 0;
            $processedAppointments = [];
            
            foreach ($appointmentIds as $appointmentId) {
                try {
                    $this->transactionService->run(function () use ($appointmentId) {
                        $appointment = $this->modelClass::where('uuid', $appointmentId)->first();
                        
                        if ($appointment) {
                            // Actualizar estado a Declined
                            $appointment->status_lead = 'Declined';
                            $appointment->inspection_status = 'Declined';
                            $appointment->save();
                            
                            // Realizar soft delete
                            $appointment->delete();
                        }
                        
                        return $appointment;
                    }, function($appointment) use (&$processedAppointments) {
                        if ($appointment) {
                            $processedAppointments[] = $appointment->uuid;
                        }
                    });
                    
                    $processed++;
                } catch (Throwable $e) {
                    $errors++;
                    Log::error("Error processing appointment during rejection: {$e->getMessage()}", [
                        'uuid' => $appointmentId,
                        'exception' => $e
                    ]);
                }
            }
            
            // Clear cache after batch processing
            $this->significantDataChange = true;
            $this->clearCache('appointments');
            
            // Dispatch job to process rejection notifications AFTER all appointments have been updated
            // and we have a list of successfully processed appointment UUIDs
            if (!empty($processedAppointments)) {
                ProcessRejectionNotifications::dispatch(
                    $processedAppointments,
                    $noContact,
                    $noInsurance,
                    $otherReason
                );
            }

            Log::info('Rejection notifications sent and appointments moved to trash', [
                'total' => count($appointmentIds),
                'processed' => $processed,
                'errors' => $errors,
                'reasons' => [
                    'no_contact' => $noContact,
                    'no_insurance' => $noInsurance,
                    'has_other_reason' => !empty($otherReason),
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => __('rejection_notifications_sent'),
                'title' => __('success_title'),
                'processed' => $processed,
                'errors' => $errors,
            ]);
        } catch (Throwable $e) {
            Log::error("Error sending rejection notifications: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending rejection notifications: ' . $e->getMessage(),
            ], 500);
        }
    }
}