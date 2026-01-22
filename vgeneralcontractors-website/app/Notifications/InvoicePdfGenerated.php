<?php

namespace App\Notifications;

use App\Models\InvoiceDemo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class InvoicePdfGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The invoice instance.
     *
     * @var \App\Models\InvoiceDemo
     */
    protected $invoice;

    /**
     * The PDF URL.
     *
     * @var string
     */
    protected $pdfUrl;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\InvoiceDemo $invoice
     * @param string $pdfUrl
     * @return void
     */
    public function __construct(InvoiceDemo $invoice, string $pdfUrl)
    {
        $this->invoice = $invoice;
        $this->pdfUrl = $pdfUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $invoiceNumber = $this->invoice->invoice_number;
        $billToName = $this->invoice->bill_to_name;
        $amount = '$' . number_format($this->invoice->balance_due, 2);
        $date = $this->invoice->invoice_date->format('m/d/Y');

        return (new MailMessage)
            ->subject("Invoice {$invoiceNumber} PDF Generated")
            ->greeting("Hello {$notifiable->name},")
            ->line("The PDF for invoice {$invoiceNumber} has been generated successfully.")
            ->line(new HtmlString("<strong>Invoice Details:</strong>"))
            ->line(new HtmlString("<strong>Invoice Number:</strong> {$invoiceNumber}"))
            ->line(new HtmlString("<strong>Client:</strong> {$billToName}"))
            ->line(new HtmlString("<strong>Amount:</strong> {$amount}"))
            ->line(new HtmlString("<strong>Date:</strong> {$date}"))
            ->action('View Invoice PDF', $this->pdfUrl)
            ->line('You can download or print this PDF for your records.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'bill_to_name' => $this->invoice->bill_to_name,
            'balance_due' => $this->invoice->balance_due,
            'invoice_date' => $this->invoice->invoice_date->format('Y-m-d'),
            'pdf_url' => $this->pdfUrl,
        ];
    }
}