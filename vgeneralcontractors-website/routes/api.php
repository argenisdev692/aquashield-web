<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookLeadFormController;
use App\Http\Controllers\RetellAIController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Facebook Lead API Routes
Route::prefix('facebook-leads')->group(function () {
    // Store a new lead (microservice endpoint)
    Route::post('/', [FacebookLeadFormController::class, 'storeApi']);
    
    // Get all leads with optional pagination and filtering
    Route::get('/', [FacebookLeadFormController::class, 'getAllLeads']);
    
    // Check calendar availability
    Route::get('/availability', [FacebookLeadFormController::class, 'getAllLeads'])->defaults('availability', true);
    
    // Reschedule existing appointment
    Route::post('/reschedule', [FacebookLeadFormController::class, 'getAllLeads'])->defaults('reschedule', true);
    
    // Get client's appointments by name, phone or email
    Route::get('/client', [FacebookLeadFormController::class, 'getAllLeads'])->defaults('client_lookup', true);
    
    // Get appointments by UUID
    Route::get('/{uuid}', [FacebookLeadFormController::class, 'getAppointmentByUuid']);
    
    // Update appointment status
    Route::patch('/{uuid}/status', [FacebookLeadFormController::class, 'updateAppointmentStatus']);
    
    // Delete appointment (soft delete)
    Route::delete('/{uuid}', [FacebookLeadFormController::class, 'deleteAppointment']);
});

// Retell AI API Routes - Public endpoints without auth middleware
Route::prefix('retell')->group(function () {
    // Lead management
    Route::post('/leads', [RetellAIController::class, 'storeLead']);
    
    // Appointment availability - Changed from GET to POST
    Route::post('/appointments/availability', [RetellAIController::class, 'getAvailability']);
    
    // Client appointments lookup - Changed from GET to POST
    Route::post('/appointments/client', [RetellAIController::class, 'getClientAppointments']);
    
    // Appointment management - UUID sent in body instead of URL
    Route::post('/appointments/get', [RetellAIController::class, 'getAppointment']);
    Route::post('/appointments/update', [RetellAIController::class, 'updateAppointment']);
    Route::post('/appointments/update', [RetellAIController::class, 'updateAppointment']);
    Route::post('/appointments/delete', [RetellAIController::class, 'deleteAppointment']);
    
    // Reschedule appointment - UUID sent in body
    Route::post('/appointments/reschedule', [RetellAIController::class, 'rescheduleAppointment']);
    
    // Update appointment status - UUID sent in body
    Route::post('/appointments/status', [RetellAIController::class, 'updateAppointmentStatus']);
    Route::post('/appointments/status', [RetellAIController::class, 'updateAppointmentStatus']);
});
