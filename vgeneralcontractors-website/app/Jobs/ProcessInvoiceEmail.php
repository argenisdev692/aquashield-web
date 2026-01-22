<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\InvoiceDemo;
use App\Models\EmailData;
use App\Models\CompanyData;
use App\Notifications\NewInvoiceNotification;
use App\Notifications\UpdatedInvoiceNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ProcessInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;
    protected $emailType;
    protected $companyData;

    /**
     * Create a new job instance.
     *
     * @param InvoiceDemo $invoice
     * @param string $emailType
     */
    public function __construct(InvoiceDemo $invoice, string $emailType = 'new')
    {
        $this->invoice = $invoice;
        $this->emailType = $emailType;
        $this->companyData = CompanyData::first();
        
        if (!$this->companyData) {
            Log::error('CompanyData not found in database');
            throw new \RuntimeException('CompanyData not found in database');
        }
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Ensure we have company data
            if (!$this->companyData) {
                $this->companyData = CompanyData::first();
                if (!$this->companyData) {
                    Log::error('CompanyData not found in database');
                    throw new \RuntimeException('CompanyData not found in database');
                }
            }

            // Get the notification class
            $notificationClass = $this->getNotificationClass();

            // Send notification to the client if we have an email
            if ($this->invoice->bill_to_email) {
                $notification = new $notificationClass($this->invoice, false, $this->companyData);
                Notification::route('mail', $this->invoice->bill_to_email)
                    ->notify($notification);
                Log::info("Invoice {$this->emailType} email sent to client: " . $this->invoice->bill_to_email . ' for invoice UUID: ' . $this->invoice->uuid);
            } else {
                Log::warning('Invoice does not have an email address. Cannot send notification. UUID: ' . $this->invoice->uuid);
            }

            // Send internal notification to admin - COMMENTED OUT
            /*
            $adminEmailData = EmailData::where('type', 'Admin')->first();
            if ($adminEmailData && $adminEmailData->email) {
                $notification = new $notificationClass($this->invoice, true, $this->companyData);
                Notification::route('mail', $adminEmailData->email)
                    ->notify($notification);
                Log::info("Internal invoice {$this->emailType} notification sent to admin: {$adminEmailData->email} for invoice UUID: " . $this->invoice->uuid);
            } else {
                Log::warning('Could not find admin email address in EmailData table for type Admin.');
            }
            */
            
            // Send internal notification to Info email
            $infoEmailData = EmailData::where('type', 'Info')->first();
            if ($infoEmailData && $infoEmailData->email) {
                $notification = new $notificationClass($this->invoice, true, $this->companyData);
                Notification::route('mail', $infoEmailData->email)
                    ->notify($notification);
                Log::info("Internal invoice {$this->emailType} notification sent to info: {$infoEmailData->email} for invoice UUID: " . $this->invoice->uuid);
            } else {
                Log::warning('Could not find info email address in EmailData table for type Info.');
            }
        } catch (\Exception $e) {
            Log::error("Error sending invoice {$this->emailType} email for invoice UUID: " . $this->invoice->uuid . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the notification class based on email type.
     *
     * @return string
     */
    protected function getNotificationClass()
    {
        return match ($this->emailType) {
            'new' => NewInvoiceNotification::class,
            'updated' => UpdatedInvoiceNotification::class,
            default => throw new \InvalidArgumentException("Invalid email type: {$this->emailType}")
        };
    }
}