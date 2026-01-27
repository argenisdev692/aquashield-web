<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class NewLeadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Obtener datos de la empresa
        $companyData = \App\Models\CompanyData::first();
        
        // Combine first and last name for the view if needed, or pass separately
        $full_name = $this->appointment->first_name . ' ' . $this->appointment->last_name;

        return (new MailMessage)
            ->subject('🚨 New Lead Alert! 🔔')
            ->view('emails.new-lead', [
                // Pass the whole appointment object for flexibility
                'appointment' => $this->appointment,
                // Pass company data
                'companyData' => $companyData,
                // Pass full name
                'full_name' => $full_name
            ]);
    }
} 