<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\UserCredentialsMail;
use App\Mail\PasswordResetMail;

class SendUserCredentialsEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $password;
    protected $isPasswordReset;

    public function __construct(User $user, ?string $password = null, bool $isPasswordReset = false)
    {
        $this->user = $user;
        $this->password = $password;
        $this->isPasswordReset = $isPasswordReset;
        
        Log::info('SendUserCredentialsEmail job created', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'is_password_reset' => $isPasswordReset,
            'password_provided' => !empty($password)
        ]);
    }

    public function handle(): void
    {
        try {
            Log::info('SendUserCredentialsEmail job starting', [
                'user_id' => $this->user->id,
                'user_email' => $this->user->email,
                'is_password_reset' => $this->isPasswordReset,
                'password_length' => $this->password ? strlen($this->password) : 0
            ]);

            $mailable = $this->isPasswordReset 
                ? new PasswordResetMail($this->user, $this->password)
                : new UserCredentialsMail($this->user, $this->password);

            Mail::to($this->user->email)->send($mailable);
            
            Log::info('SendUserCredentialsEmail job completed successfully', [
                'user_id' => $this->user->id,
                'user_email' => $this->user->email,
                'is_password_reset' => $this->isPasswordReset
            ]);
        } catch (\Exception $e) {
            Log::error('SendUserCredentialsEmail job failed', [
                'user_id' => $this->user->id,
                'user_email' => $this->user->email,
                'is_password_reset' => $this->isPasswordReset,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}
