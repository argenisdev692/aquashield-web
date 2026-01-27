<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\CompanyData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRejectionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $noContact;
    protected $noInsurance;
    protected $otherReason;
    protected $companyData;

    /**
     * Create a new notification instance.
     *
     * @param  Appointment  $appointment
     * @param  bool  $noContact
     * @param  bool  $noInsurance
     * @param  string|null  $otherReason
     * @param  CompanyData|null  $companyData
     * @return void
     */
    public function __construct(Appointment $appointment, bool $noContact = false, bool $noInsurance = false, ?string $otherReason = null, ?CompanyData $companyData = null)
    {
        $this->appointment = $appointment;
        $this->noContact = $noContact;
        $this->noInsurance = $noInsurance;
        $this->otherReason = $otherReason;
        $this->companyData = $companyData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Información Importante Sobre Su Solicitud de Inspección')
            ->view('emails.appointment-rejection', [
                'appointment' => $this->appointment,
                'noContact' => $this->noContact,
                'noInsurance' => $this->noInsurance,
                'otherReason' => $this->otherReason,
                'companyData' => $this->companyData
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'appointment_uuid' => $this->appointment->uuid,
            'customer_name' => $this->appointment->first_name . ' ' . $this->appointment->last_name,
            'customer_email' => $this->appointment->email,
            'no_contact' => $this->noContact,
            'no_insurance' => $this->noInsurance,
            'other_reason' => $this->otherReason,
        ];
    }
} 