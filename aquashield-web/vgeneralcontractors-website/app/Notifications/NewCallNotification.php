<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewCallNotification extends Notification
{
    use Queueable;

    protected $callData;

    public function __construct(array $callData)
    {
        $this->callData = $callData;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔔 Nueva Llamada Registrada - V General Contractors')
            ->view('emails.new-call', [
                'callData' => $this->callData,
                'companyData' => \App\Models\CompanyData::first()
            ]);
    }
}