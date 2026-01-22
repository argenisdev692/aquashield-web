<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactSupport;
use App\Models\CompanyData;

class ContactSupportNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $contactSupport;
    public $companyData;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\ContactSupport $contactSupport
     * @return void
     */
    public function __construct(ContactSupport $contactSupport)
    {
        $this->contactSupport = $contactSupport;
        $this->companyData = CompanyData::first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Contact Support Request - ' . config('app.name'))
                    ->view('emails.contact-support-notification');
    }
} 