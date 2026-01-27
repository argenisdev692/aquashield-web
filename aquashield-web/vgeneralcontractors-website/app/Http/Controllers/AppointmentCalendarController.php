<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Jobs\ProcessAppointmentEmail;
use App\Jobs\ProcessNewLead;
use Illuminate\Support\Facades\Cache;
use App\Traits\CacheTrait;

class AppointmentCalendarController extends Controller
{
    use CacheTrait;
    
    protected TransactionService $transactionService;
    public $search = '';
    public $sortField = 'inspection_date';
    public $sortDirection = 'asc';
    public $perPage = 100;
    public $showDeleted = false;
    protected $significantDataChange = false;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display the calendar view.
     */
    public function index()
    {
        // Simply return the calendar view
        return view('appointments.calendar'); 
    }

    /**
     * Fetch appointments as events for FullCalendar.
     */
    public function events(Request $request)
    {
        // Get the date range requested by FullCalendar (parameters start, end)
        // Or load all relevant appointments if a range is not initially provided
        $start = $request->query('start') ? Carbon::parse($request->query('start'))->startOfDay() : now()->subMonth();
        $end = $request->query('end') ? Carbon::parse($request->query('end'))->endOfDay() : now()->addMonth();
        
        // Create a cache key based on the date range
        $cacheKey = 'calendar_events_' . $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
        
        // Register this cache key for future invalidation
        $this->trackCalendarCacheKey($cacheKey);
        
        // Cache the events for 60 minutes (adjust as needed)
        $events = Cache::remember($cacheKey, 60, function() use ($start, $end) {
            $appointments = Appointment::query()
                ->where(function($query) use ($start, $end) {
                    // Filter appointments with inspection date within the range
                    $query->whereBetween('inspection_date', [$start->toDateString(), $end->toDateString()]);
                })
                // Optionally filter by status if needed
                // ->whereNotIn('inspection_status', ['Declined'])
                ->get();
    
            return $appointments->map(function (Appointment $appointment) {
                // Color based on appointment status
                $color = '#3b82f6'; // Blue by default (pending)
                switch ($appointment->inspection_status) {
                    case 'Completed':
                        $color = '#059669'; // Dark Green
                        break;
                    case 'Confirmed':
                        $color = '#10b981'; // Green (changed from purple)
                        break;
                    case 'Declined':
                        $color = '#ef4444'; // Red
                        break;
                    case 'Pending':
                        $color = '#f59e0b'; // Orange
                        break;
                }
    
                // Convert inspection_date and inspection_time to Carbon objects
                $inspectionDate = $appointment->inspection_date instanceof \Carbon\Carbon 
                    ? $appointment->inspection_date 
                    : Carbon::parse($appointment->inspection_date);
                $inspectionTime = $appointment->inspection_time ? Carbon::parse($appointment->inspection_time) : null;
                
                // If we have a time, combine it with the date
                if ($inspectionTime) {
                    $startTime = ($appointment->inspection_date instanceof \Carbon\Carbon 
                        ? $appointment->inspection_date 
                        : Carbon::parse($appointment->inspection_date))
                        ->setHour($inspectionTime->hour)
                        ->setMinute($inspectionTime->minute)
                        ->setSecond(0);
                    
                    // Add 3 hours for the end by default
                    $endTime = $startTime->copy()->addHours(3);
                } else {
                    // If there's no time, use all day
                    $startTime = $inspectionDate;
                    $endTime = $inspectionDate->copy()->addDay();
                }
    
                // Format the appointment for FullCalendar
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->first_name . ' ' . $appointment->last_name,
                    'start' => $startTime->toIso8601String(),
                    'end' => $endTime->toIso8601String(),
                    'color' => $color,
                    'className' => 'fc-event-' . strtolower($appointment->inspection_status),
                    'allDay' => $inspectionTime ? false : true,
                    // Additional properties to display in the popup
                    'extendedProps' => [
                        'clientName' => $appointment->first_name . ' ' . $appointment->last_name,
                        'clientEmail' => $appointment->email,
                        'clientPhone' => $appointment->phone,
                        'status' => $appointment->inspection_status,
                        'leadStatus' => $appointment->status_lead,
                        'notes' => $appointment->notes,
                        'address' => $appointment->address . ', ' . $appointment->city . ', ' . $appointment->state . ' ' . $appointment->zipcode,
                        'message' => $appointment->message,
                        'damage' => $appointment->damage_detail,
                        'hasInsurance' => $appointment->insurance_property ? 'Yes' : 'No',
                        'latitude' => $appointment->latitude,
                        'longitude' => $appointment->longitude,
                    ]
                ];
            });
        });

        return response()->json($events);
    }

    /**
     * Update appointment time via drag-and-drop.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming start and end times
        $validator = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid date format.', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            $updatedAppointment = $this->transactionService->run(
                // 1. Database operations
                function () use ($id, $validatedData) {
                    $appointment = Appointment::findOrFail($id);
                    
                    // Parse new times
                    $newStartTime = Carbon::parse($validatedData['start']);
                    
                    // Update the inspection date and time
                    $appointment->inspection_date = $newStartTime->toDateString();
                    $appointment->inspection_time = $newStartTime->format('H:i:s');
                    
                    // Check for conflicts with other appointments
                    $existingAppointment = Appointment::where('id', '!=', $appointment->id)
                        ->where('inspection_date', $appointment->inspection_date)
                        ->where('inspection_time', $appointment->inspection_time)
                        ->whereNotIn('inspection_status', ['Declined'])
                        ->first();

                    if ($existingAppointment) {
                        throw new \RuntimeException('Schedule conflict: Another appointment is already scheduled for this time.', 409);
                    }

                    // Mark the inspection as confirmed
                    $appointment->inspection_status = 'Confirmed';
                    $appointment->status_lead = 'Called';
                    
                    $appointment->save();

                    Log::info('Appointment updated via drag and drop in calendar', ['id' => $appointment->id]);
                    return $appointment;
                },
                // 2. Action to execute after completing the transaction
                function ($updatedAppointment) {
                    // Send notification via job
                    ProcessAppointmentEmail::dispatch($updatedAppointment, 'rescheduled');                    
                    Log::info('Rescheduling email job dispatched', ['id' => $updatedAppointment->id]);
                    
                    // Mark significant change and clear relevant caches
                    $this->significantDataChange = true;
                    $this->clearCache('appointments');
                    
                    // Clear calendar event cache for all potential date ranges that could include this appointment
                    // We'll use a pattern to clear all calendar event caches
                    $cacheKeys = Cache::get('calendar_event_keys', []);
                    foreach ($cacheKeys as $key) {
                        Cache::forget($key);
                    }
                    Cache::forget('calendar_event_keys');
                }
            );

            // If the transaction is successful
            return response()->json(['message' => 'Appointment updated successfully. A notification email has been sent to the client.']);

        } catch (\RuntimeException $re) {
            // Capture specific errors thrown from the transaction
            Log::warning('Business logic error during calendar update: ' . $re->getMessage(), [
                'id' => $id,
                'code' => $re->getCode()
            ]);
            return response()->json(['message' => $re->getMessage()], $re->getCode() ?: 422);
        } catch (Throwable $e) {
            // Capture any other error during the transaction
            Log::error('Error during appointment update in calendar: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e
            ]);
            return response()->json(['message' => 'Error updating the appointment in the calendar.'], 500);
        }
    }

    /**
     * Change appointment status (confirm/decline)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $status = $request->input('status');
            // Only allow valid values based on the database schema
            if (!in_array($status, ['Confirmed', 'Completed', 'Pending', 'Declined'])) {
                return response()->json(['success' => false, 'message' => 'Invalid status'], 400);
            }

            $appointment = Appointment::findOrFail($id);
            $oldStatus = $appointment->inspection_status;
            $appointment->inspection_status = $status;
            
            // Update status_lead based on inspection_status
            if ($status === 'Confirmed' || $status === 'Completed') {
                $appointment->status_lead = 'Called';
            } else if ($status === 'Declined') {
                $appointment->status_lead = 'Declined';
                // Clear inspection date and time to free up the slot
                $appointment->inspection_date = null;
                $appointment->inspection_time = null;
            } else if ($status === 'Pending') {
                if ($appointment->status_lead !== 'Pending') {
                    $appointment->status_lead = 'New';
                }
            }
            
            // If status is not Confirmed or Completed, but date/time are set,
            // ensure inspection_status is set to Confirmed
            if (($status !== 'Confirmed' && $status !== 'Completed') && 
                $appointment->inspection_date && $appointment->inspection_time) {
                $appointment->inspection_status = 'Confirmed';
                $appointment->status_lead = 'Called';
            }
            
            $appointment->save();
            
            // Clear cache
            $this->significantDataChange = true;
            $this->clearCache('appointments');
            
            // Clear calendar event cache
            $cacheKeys = Cache::get('calendar_event_keys', []);
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            Cache::forget('calendar_event_keys');

            // Determine message and email type based on status
            $message = 'Appointment status updated successfully.';
            $emailType = '';
            
            if ($appointment->inspection_status === 'Confirmed') {
                $emailType = 'confirmed';
                $message = 'Appointment confirmed successfully. A confirmation email has been sent to the client.';
            } else if ($appointment->inspection_status === 'Declined') {
                $emailType = 'declined';
                $message = 'Appointment declined successfully. A notification email has been sent to the client.';
            } else if ($appointment->inspection_status === 'Completed') {
                $emailType = 'completed';
                $message = 'Appointment marked as completed successfully.';
            }
            
            // Send notification email if needed
            if (!empty($emailType)) {
                ProcessAppointmentEmail::dispatch($appointment, $emailType);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Create a new appointment from the calendar view
     */
    public function create(Request $request)
    {
        try {
            // Find the client first to check if we're updating an existing appointment
            $client = Appointment::where('uuid', $request->client_uuid)->first();
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            // If no date/time provided, use existing ones (for confirmation scenarios)
            $inspectionDate = $request->inspection_date ?? $client->inspection_date;
            $inspectionTime = $request->inspection_time ?? $client->inspection_time;
            $inspectionStatus = $request->inspection_status ?? 'Confirmed';

            // Validate the request with conditional rules
            $rules = [
                'client_uuid' => 'required|exists:appointments,uuid',
                'inspection_status' => 'required|in:Confirmed,Pending'
            ];

            // Only require date/time if they're being changed
            if ($request->has('inspection_date') || $request->has('inspection_time')) {
                $rules['inspection_date'] = 'required|date|after_or_equal:today';
                $rules['inspection_time'] = 'required|date_format:H:i';
            }

            $request->validate($rules);

            // Check for schedule conflicts only if date/time are being changed
            if ($request->has('inspection_date') || $request->has('inspection_time')) {
                $conflictCheck = Appointment::where('inspection_date', $inspectionDate)
                    ->where('inspection_time', $inspectionTime)
                    ->whereNotIn('inspection_status', ['Declined', 'Cancelled'])
                    ->where('uuid', '!=', $client->uuid)
                    ->exists();

                if ($conflictCheck) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This time slot is already booked with another client.',
                        'errors' => [
                            'schedule_conflict' => 'Please select a different date or time for your inspection.'
                        ]
                    ], 422);
                }
            }

            // Update the appointment with new inspection details
            $oldStatus = $client->inspection_status;
            $oldInspectionDate = $client->inspection_date;
            $oldInspectionTime = $client->inspection_time;
            
            // Use the determined values (either from request or existing)
            $client->inspection_date = $inspectionDate;
            $client->inspection_time = $inspectionTime;
            
            // Ensure consistent status handling
            // If both date and time are provided, use the determined status
            if (!empty($client->inspection_date) && !empty($client->inspection_time)) {
                $client->inspection_status = $inspectionStatus;
                if ($inspectionStatus === 'Confirmed') {
                    $client->status_lead = 'Called';
                }
            } else {
                $client->inspection_status = $inspectionStatus;
                
                // Set status_lead based on inspection_status
                if ($client->inspection_status === 'Confirmed') {
                    $client->status_lead = 'Called';
                } else if ($client->inspection_status === 'Declined') {
                    $client->status_lead = 'Declined';
                } else if ($client->inspection_status === 'Pending') {
                    if ($client->status_lead !== 'Pending') {
                        $client->status_lead = 'New';
                    }
                }
            }
            
            $client->save();
            
            // Clear cache after updating
            $this->significantDataChange = true;
            $this->clearCache('appointments');
            
            // Clear calendar event cache
            $cacheKeys = Cache::get('calendar_event_keys', []);
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            Cache::forget('calendar_event_keys');

            // Determine email type and message based on status
            $message = 'Appointment scheduled successfully.';
            $emailType = '';
            
            if ($client->inspection_status === 'Confirmed') {
                if ($oldStatus !== 'Confirmed') {
                    $emailType = 'confirmed';
                    $message = 'Appointment confirmed successfully. A confirmation email has been sent to the client.';
                } else {
                    // Check if the date or time actually changed (this would be a reschedule)
                    $dateChanged = ($client->inspection_date !== $oldInspectionDate);
                    $timeChanged = ($client->inspection_time !== $oldInspectionTime);
                    
                    if ($dateChanged || $timeChanged) {
                        $emailType = 'rescheduled';
                        $message = 'Appointment rescheduled successfully. A notification email has been sent to the client.';
                    } else {
                        $emailType = 'confirmed';
                        $message = 'Appointment confirmed successfully. A confirmation email has been sent to the client.';
                    }
                }
            }
            
            // Send notification email if needed
            if (!empty($emailType)) {
                ProcessAppointmentEmail::dispatch($client, $emailType);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'appointment' => $client
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('appointment_calendar_validation_error'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create appointment for existing client (alias for create method)
     */
    public function createAppointment(Request $request)
    {
        return $this->create($request);
    }

    /**
     * Store a new lead/appointment from the calendar modal
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\'-]+$/',
                'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\'-]+$/',
                'email' => 'required|email|max:255|unique:appointments,email',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'address_2' => 'nullable|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'zipcode' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'insurance_property' => 'required|boolean',
                'message' => 'nullable|string',
                'notes' => 'nullable|string',
                'damage_detail' => 'nullable|string',
                'intent_to_claim' => 'nullable|boolean',
                'lead_source' => 'required|in:Website,Facebook Ads,Reference,Retell AI',
                'sms_consent' => 'nullable|boolean',
                'inspection_date' => 'nullable|date|after_or_equal:today',
                'inspection_time' => 'nullable|date_format:H:i',
            ]);

            // Use transaction service for data consistency
            $appointment = $this->transactionService->run(function () use ($request) {
                // Create new appointment/lead
                $appointment = new Appointment();
                $appointment->uuid = \Str::uuid();
                $appointment->first_name = $request->first_name;
                $appointment->last_name = $request->last_name;
                $appointment->email = $request->email;
                $appointment->phone = $request->phone;
                $appointment->address = $request->address;
                $appointment->address_2 = $request->address_2;
                $appointment->city = $request->city;
                $appointment->state = $request->state;
                $appointment->zipcode = $request->zipcode;
                $appointment->country = $request->country ?? 'USA';
                $appointment->latitude = $request->latitude;
                $appointment->longitude = $request->longitude;
                $appointment->insurance_property = $request->insurance_property ?? false;
                $appointment->message = $request->message;
                $appointment->notes = $request->notes;
                $appointment->damage_detail = $request->damage_detail;
                $appointment->intent_to_claim = $request->intent_to_claim ?? false;
                $appointment->sms_consent = $request->sms_consent ?? false;
                $appointment->registration_date = now();
                
                // Set lead source from request
                $appointment->lead_source = $request->lead_source;
                
                // Set inspection details if provided
                if ($request->inspection_date && $request->inspection_time) {
                    $appointment->inspection_date = $request->inspection_date;
                    $appointment->inspection_time = $request->inspection_time;
                    $appointment->inspection_status = 'Confirmed';
                    $appointment->status_lead = 'Called';
                } else {
                    // New lead without inspection scheduled
                    $appointment->status_lead = 'New';
                }
                
            $appointment->save();

                return $appointment;
            });

            // Clear cache after creating new appointment
            $this->significantDataChange = true;
            $this->clearCache('appointments');
            
            // Clear calendar event cache
            $cacheKeys = Cache::get('calendar_event_keys', []);
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            Cache::forget('calendar_event_keys');

            // Send appropriate notification based on appointment type
            if ($appointment->inspection_date && $appointment->inspection_time) {
                // If appointment has date and time, send confirmation email
                ProcessAppointmentEmail::dispatch($appointment, 'confirmed');
            } else {
                // If no date/time, treat as new lead
                ProcessNewLead::dispatch($appointment);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully.',
                'appointment' => $appointment
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('appointment_calendar_validation_error'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch available clients for appointment scheduling
     */
    public function getClients()
    {
        try {
            // Create a cache key for clients list
            $cacheKey = 'calendar_available_clients';
            
            // Cache the clients list for 10 minutes
            $clients = Cache::remember($cacheKey, 10, function() {
                return Appointment::select('uuid', 'first_name', 'last_name', 'email', 'phone', 'status_lead', 'inspection_status')
                    ->whereNotNull('first_name')
                    ->whereNotNull('email')
                    ->orderBy('created_at', 'desc')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $clients
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching clients: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if email already exists for duplicate validation.
     */
    public function checkEmailExists(Request $request)
    {
        try {
        $email = $request->input('email');
            
            if (!$email) {
                return response()->json([
                    'exists' => false,
                    'valid' => true
                ]);
            }

            // Validate email format first
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'exists' => false,
                    'valid' => false,
                    'message' => __('invalid_email_format')
                ]);
            }

            // Check if email exists in appointments table
            $exists = Appointment::where('email', $email)->exists();
        
        return response()->json([
            'exists' => $exists,
                'valid' => true,
                'message' => $exists ? __('email_already_exists') : null
            ]);
        } catch (Throwable $e) {
            Log::error('Error checking email existence', ['error' => $e->getMessage()]);
            return response()->json([
                'exists' => false,
                'valid' => true,
                'message' => null
            ], 500);
        }
    }

    /**
     * Check if phone already exists for duplicate validation.
     */
    public function checkPhoneExists(Request $request)
    {
        try {
        $phone = $request->input('phone');
            
            if (!$phone) {
                return response()->json([
                    'exists' => false,
                    'valid' => true
                ]);
            }

            // Clean phone number (remove formatting)
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            
            // Validate phone format (must be 10 digits)
            if (strlen($cleanPhone) !== 10) {
                return response()->json([
                    'exists' => false,
                    'valid' => false,
                    'message' => __('invalid_phone_format')
                ]);
            }

            // Check if phone exists in appointments table (compare clean numbers)
            $exists = Appointment::whereRaw('REGEXP_REPLACE(phone, "[^0-9]", "") = ?', [$cleanPhone])->exists();
        
        return response()->json([
            'exists' => $exists,
                'valid' => true,
                'message' => $exists ? __('phone_already_exists') : null
            ]);
        } catch (Throwable $e) {
            Log::error('Error checking phone existence', ['error' => $e->getMessage()]);
            return response()->json([
                'exists' => false,
                'valid' => true,
                'message' => null
            ], 500);
        }
    }

    /**
     * Track calendar event cache keys for efficient invalidation
     */
    private function trackCalendarCacheKey($key)
    {
        $cacheKeys = Cache::get('calendar_event_keys', []);
        if (!in_array($key, $cacheKeys)) {
            $cacheKeys[] = $key;
            Cache::put('calendar_event_keys', $cacheKeys, 60 * 24); // Store for 24 hours
        }
    }
}