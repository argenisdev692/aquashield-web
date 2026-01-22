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

class AppointmentCancelledNotification extends Notification implements ShouldQueue
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
                ->subject('❌ Appointment Cancelled Alert! 🔔')
                ->view('emails.appointment-cancelled-internal', [
                    'appointment' => $this->appointment,
                    'companyData' => $companyData
                ]);
        }

        return (new MailMessage)
            ->subject('❌ Cita Cancelada - V General Contractors')
            ->view('emails.appointment-cancelled', [
                'appointment' => $this->appointment,
                'companyData' => $companyData
            ]);
    }
} 