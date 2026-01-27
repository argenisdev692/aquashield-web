<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\CompanyData;

class UserCredentialsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $companyData;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->companyData = CompanyData::first(); // O como obtengas los datos de tu empresa
    }

    public function build()
    {
        return $this->view('emails.user-credentials')
                    ->subject('Welcome to ' . $this->companyData->company_name . ' - Your Account Credentials');
    }
} 