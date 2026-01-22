<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\CompanyData;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $companyData;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->companyData = CompanyData::first();
    }

    public function build()
    {
        return $this->subject('Password Reset Notification - ' . config('app.name'))
                    ->view('emails.password-reset');
    }
}
