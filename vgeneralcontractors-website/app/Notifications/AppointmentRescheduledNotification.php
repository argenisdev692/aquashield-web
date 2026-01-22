<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\CompanyData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AppointmentRescheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $isInternal;
    protected $companyData;

    public function __construct(Appointment $appointment, bool $isInternal = false, ?CompanyData $companyData = null)
    {
        $this->appointment = $appointment;
        $this->isInternal = $isInternal;
        $this->companyData = $companyData ?? CompanyData::first();
        
        if (!$this->companyData) {
            Log::error('CompanyData not found in database');
            throw new \RuntimeException('CompanyData not found in database');
        }
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Asegurarnos de que tenemos CompanyData
        if (!$this->companyData) {
            $this->companyData = CompanyData::first();
            if (!$this->companyData) {
                Log::error('CompanyData not found in database during email generation');
                throw new \RuntimeException('CompanyData not found in database during email generation');
            }
        }

        if ($this->isInternal) {
            return (new MailMessage)
                ->subject('🔄 Appointment Rescheduled Alert! 🔔')
                ->view('emails.appointment-rescheduled-internal', [
                    'appointment' => $this->appointment,
                    'companyData' => $this->companyData
                ]);
        }

        return (new MailMessage)
            ->subject('🔄 Cita Reprogramada - V General Contractors')
            ->view('emails.appointment-rescheduled', [
                'appointment' => $this->appointment,
                'companyData' => $this->companyData
            ]);
    }
} 