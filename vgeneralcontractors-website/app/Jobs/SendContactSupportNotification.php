<?php // MUST be the very first thing in the file. No spaces or lines before this.

namespace App\Jobs;

use App\Mail\ContactSupportNotification as ContactSupportNotificationMail; // Alias for clarity
use App\Models\CompanyData;
use App\Models\ContactSupport;
use App\Models\EmailData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable; // Make sure this is imported

class SendContactSupportNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The contact support instance.
     *
     * @var \App\Models\ContactSupport
     */
    public ContactSupport $contactSupport; // Added type hint (optional, requires PHP 7.4+)

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3; // Example: Allow 3 attempts

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60; // Example: Wait 60 seconds before retry

    /**
     * Create a new job instance.
     *
     * @param \App\Models\ContactSupport $contactSupport
     * @return void
     */
    public function __construct(ContactSupport $contactSupport)
    {
        // Use `withoutRelations()` if you don't need related models in the job
        // to potentially reduce payload size, but only if you're sure you won't access them later.
        $this->contactSupport = $contactSupport->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void // Added return type hint (optional, requires PHP 7.1+)
    {
        try {
            $recipientEmails = [];
            
            // 1. Get company data email (original recipient)
            $companyData = CompanyData::query()->first();
            if ($companyData && $companyData->email && filter_var($companyData->email, FILTER_VALIDATE_EMAIL)) {
                $recipientEmails[] = [
                    'email' => $companyData->email,
                    'type' => 'Company'
                ];
                Log::info('Added Company email to contact support notification recipients.', [
                    'contact_support_id' => $this->contactSupport->id,
                    'company_email' => $companyData->email
                ]);
            } else {
                Log::warning('Company email not found or invalid in CompanyData.', [
                    'contact_support_id' => $this->contactSupport->id,
                    'job_id' => $this->job?->getJobId()
                ]);
            }

            // 2. Get Admin email from EmailData (following ProcessNewLead pattern)
            $adminEmailData = EmailData::where('type', 'Admin')->first();
            if ($adminEmailData && $adminEmailData->email && filter_var($adminEmailData->email, FILTER_VALIDATE_EMAIL)) {
                $recipientEmails[] = [
                    'email' => $adminEmailData->email,
                    'type' => 'Admin'
                ];
                Log::info('Added Admin email to contact support notification recipients.', [
                    'contact_support_id' => $this->contactSupport->id,
                    'admin_email' => $adminEmailData->email
                ]);
            } else {
                Log::warning('Admin email not found or invalid in EmailData.', [
                    'contact_support_id' => $this->contactSupport->id,
                    'job_id' => $this->job?->getJobId()
                ]);
            }

            // 3. Check if we have at least one valid recipient
            if (empty($recipientEmails)) {
                Log::error('No valid recipient emails found for contact support notification.', [
                    'contact_support_id' => $this->contactSupport->id,
                    'job_id' => $this->job?->getJobId()
                ]);
                $this->fail('No valid recipient emails configured.');
                return;
            }

            // 4. Send emails to all recipients
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($recipientEmails as $recipient) {
                try {
                    // Create the Mailable instance for each recipient
                    $email = new ContactSupportNotificationMail($this->contactSupport);
                    
                    // Send the email
                    Mail::to($recipient['email'])->send($email);
                    
                    $successCount++;
                    Log::info('Contact support notification email sent successfully.', [
                        'contact_support_id' => $this->contactSupport->id,
                        'recipient_email' => $recipient['email'],
                        'recipient_type' => $recipient['type'],
                        'job_id' => $this->job?->getJobId()
                    ]);
                    
                } catch (Throwable $emailError) {
                    $failureCount++;
                    Log::error('Failed to send contact support notification to specific recipient.', [
                        'contact_support_id' => $this->contactSupport->id,
                        'recipient_email' => $recipient['email'],
                        'recipient_type' => $recipient['type'],
                        'job_id' => $this->job?->getJobId(),
                        'error_message' => $emailError->getMessage()
                    ]);
                }
            }

            // 5. Log final results
            Log::info('Contact support notification job completed.', [
                'contact_support_id' => $this->contactSupport->id,
                'total_recipients' => count($recipientEmails),
                'successful_sends' => $successCount,
                'failed_sends' => $failureCount,
                'job_id' => $this->job?->getJobId()
            ]);

            // If all emails failed, consider it a job failure
            if ($successCount === 0) {
                $this->fail('All email notifications failed to send.');
            }

        } catch (Throwable $e) {
            Log::error('Failed to process contact support notification job.', [
                'contact_support_id' => $this->contactSupport->id,
                'job_id' => $this->job?->getJobId(),
                'error_message' => $e->getMessage(),
            ]);

            // Check if the job has attempts remaining before releasing back to the queue
            if ($this->attempts() < $this->tries) {
                // Release the job back onto the queue with a delay (backoff)
                $this->release($this->backoff);
            } else {
                // Max attempts reached, fail the job permanently
                $this->fail($e);
            }
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        // This method is called when the job fails permanently (after all retries)
        Log::critical('SendContactSupportNotification Job FAILED permanently.', [
            'contact_support_id' => $this->contactSupport->id,
            'job_id' => $this->job?->getJobId(),
            'error_message' => $exception->getMessage(),
        ]);
        // Send notification to admin channel (e.g., Slack, email)
        // AdminNotification::send('Contact Support Email Job Failed for ID: ' . $this->contactSupport->id);
    }
}