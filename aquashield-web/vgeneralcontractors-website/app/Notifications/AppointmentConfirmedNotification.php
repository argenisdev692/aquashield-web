<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\CompanyData;
use App\Traits\HandlesCompanyData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AppointmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable, HandlesCompanyData;

    protected $appointment;
    protected $isInternal;
    protected $companyData;

    public function __construct(Appointment $appointment, bool $isInternal = false, ?CompanyData $companyData = null)
    {
        $this->appointment = $appointment;
        $this->isInternal = $isInternal;
        $this->companyData = $companyData;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $companyData = $this->getCompanyData();

        if ($this->isInternal) {
            return (new MailMessage)
                ->subject('🎉 Appointment Confirmed Alert! 🔔')
                ->view('emails.appointment-confirmed-internal', [
                    'appointment' => $this->appointment,
                    'companyData' => $companyData
                ]);
        }

        return (new MailMessage)
            ->subject('✅ ¡Cita Confirmada! - V General Contractors')
            ->view('emails.appointment-confirmed', [
                'appointment' => $this->appointment,
                'companyData' => $companyData
            ]);
    }
} 