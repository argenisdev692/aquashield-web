<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Models\EmailData;
use App\Models\CompanyData;
use App\Notifications\AppointmentConfirmedNotification;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentRescheduledNotification;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ProcessAppointmentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;
    protected $emailType;
    protected $companyData;

    public function __construct(Appointment $appointment, string $emailType)
    {
        $this->appointment = $appointment;
        $this->emailType = $emailType;
        $this->companyData = CompanyData::first();
        
        if (!$this->companyData) {
            Log::error('CompanyData not found in database');
            throw new \RuntimeException('CompanyData not found in database');
        }
    }

    public function handle()
    {
        try {
            // Ensure we have company data
            if (!$this->companyData) {
                $this->companyData = CompanyData::first();
                if (!$this->companyData) {
                    Log::error('CompanyData not found in database');
                    throw new \RuntimeException('CompanyData not found in database');
                }
            }

            // Get the notification class
            $notificationClass = $this->getNotificationClass();

            // Send notification to the client
            if ($this->appointment->email) {
                $notification = new $notificationClass($this->appointment, false, $this->companyData);
                Notification::route('mail', $this->appointment->email)
                    ->notify($notification);
                Log::info("Appointment {$this->emailType} email sent to client: " . $this->appointment->email . ' for appointment UUID: ' . $this->appointment->uuid);
            } else {
                Log::warning('Appointment does not have an email address. Cannot send notification. UUID: ' . $this->appointment->uuid);
            }

            // Send internal notification to admin
            $appointmentEmailData = EmailData::where('type', 'Admin')->first();
            if ($appointmentEmailData && $appointmentEmailData->email) {
                $notification = new $notificationClass($this->appointment, true, $this->companyData);
                Notification::route('mail', $appointmentEmailData->email)
                    ->notify($notification);
                Log::info("Internal appointment {$this->emailType} notification sent to admin: {$appointmentEmailData->email} for appointment UUID: " . $this->appointment->uuid);
            } else {
                Log::warning('Could not find appointment email address in EmailData table for type Admin.');
            }
        } catch (\Exception $e) {
            Log::error("Error sending appointment {$this->emailType} email for appointment UUID: " . $this->appointment->uuid . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function getNotificationClass()
    {
        return match ($this->emailType) {
            'confirmed' => AppointmentConfirmedNotification::class,
            'cancelled', 'declined' => AppointmentCancelledNotification::class,
            'rescheduled' => AppointmentRescheduledNotification::class,
            'reminder' => AppointmentReminderNotification::class,
            default => throw new \InvalidArgumentException("Invalid email type: {$this->emailType}")
        };
    }
} 