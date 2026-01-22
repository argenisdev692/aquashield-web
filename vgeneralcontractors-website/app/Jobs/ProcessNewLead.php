<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Models\EmailData;
use App\Notifications\NewLeadNotification;
use App\Notifications\LeadConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ProcessNewLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function handle()
    {
        // 1. Send notification to the internal team
        try {
            $appointmentEmailData = EmailData::where('type', 'Admin')->first();
            if ($appointmentEmailData && $appointmentEmailData->email) {
                Notification::route('mail', $appointmentEmailData->email)
                    ->notify(new NewLeadNotification($this->appointment));
                 Log::info('Internal new lead notification sent for appointment UUID: ' . $this->appointment->uuid);
            } else {
                Log::warning('Could not find appointment email address in EmailData table.');
            }
        } catch (\Exception $e) {
            Log::error('Error sending internal new lead notification for appointment UUID: ' . $this->appointment->uuid . ' - Error: ' . $e->getMessage());
            // Optionally re-throw or handle the exception differently
        }

        // 2. Send confirmation notification to the lead
        try {
            if ($this->appointment->email) {
                // Send notification directly to the lead's email
                Notification::route('mail', $this->appointment->email)
                    ->notify(new LeadConfirmationNotification($this->appointment));
                 Log::info('Lead confirmation notification sent to: ' . $this->appointment->email . ' for appointment UUID: ' . $this->appointment->uuid);
            } else {
                Log::warning('Lead does not have an email address. Cannot send confirmation. UUID: ' . $this->appointment->uuid);
            }
        } catch (\Exception $e) {
            Log::error('Error sending lead confirmation notification for appointment UUID: ' . $this->appointment->uuid . ' - Error: ' . $e->getMessage());
            // Optionally re-throw or handle the exception differently
        }
    }
} 