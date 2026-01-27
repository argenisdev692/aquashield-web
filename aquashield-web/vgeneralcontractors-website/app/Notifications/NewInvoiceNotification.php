<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InvoiceDemo;
use App\Models\CompanyData;
use Illuminate\Support\HtmlString;

class NewInvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoice;
    protected $isInternal;
    protected $companyData;

    /**
     * Create a new notification instance.
     *
     * @param InvoiceDemo $invoice
     * @param bool $isInternal
     * @param CompanyData $companyData
     */
    public function __construct(InvoiceDemo $invoice, bool $isInternal, CompanyData $companyData)
    {
        $this->invoice = $invoice;
        $this->isInternal = $isInternal;
        $this->companyData = $companyData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->getSubject())
            ->view(
                'emails.invoice-new', 
                [
                    'invoice' => $this->invoice,
                    'isInternal' => $this->isInternal,
                    'companyData' => $this->companyData
                ]
            );

        // If we have a PDF URL, attach it to the email
        if ($this->invoice->pdf_url) {
            $mailMessage->line(new HtmlString('<div style="margin-top: 15px; margin-bottom: 15px;">'))
                ->line('You can also view or download the invoice PDF:')
                ->action('View Invoice PDF', $this->invoice->pdf_url)
                ->line(new HtmlString('</div>'));
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_uuid' => $this->invoice->uuid,
            'invoice_number' => $this->invoice->invoice_number,
            'bill_to_name' => $this->invoice->bill_to_name,
            'total_amount' => $this->invoice->total_amount,
        ];
    }

    /**
     * Get the subject for the email.
     *
     * @return string
     */
    protected function getSubject(): string
    {
        if ($this->isInternal) {
            return "New Invoice Created - {$this->invoice->invoice_number} - {$this->invoice->bill_to_name}";
        }

        return "New Invoice #{$this->invoice->invoice_number} from {$this->companyData->company_name}";
    }
}