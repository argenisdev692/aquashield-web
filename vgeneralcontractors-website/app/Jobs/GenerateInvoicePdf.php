<?php

namespace App\Jobs;

use App\Models\InvoiceDemo;
use App\Services\InvoicePdfService;
use App\Notifications\InvoicePdfGenerated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class GenerateInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * The invoice instance.
     *
     * @var \App\Models\InvoiceDemo
     */
    protected $invoice;

    /**
     * Whether to force regeneration of the PDF.
     *
     * @var bool
     */
    protected $forceRegenerate;

    /**
     * Whether to notify the user when the PDF is generated.
     *
     * @var bool
     */
    protected $shouldNotify;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\InvoiceDemo $invoice
     * @param bool $forceRegenerate
     * @param bool $shouldNotify
     * @return void
     */
    public function __construct(InvoiceDemo $invoice, bool $forceRegenerate = false, bool $shouldNotify = false)
    {
        $this->invoice = $invoice;
        $this->forceRegenerate = $forceRegenerate;
        $this->shouldNotify = $shouldNotify;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\InvoicePdfService $pdfService
     * @return void
     */
    public function handle(InvoicePdfService $pdfService)
    {
        try {
            Log::info('Starting PDF generation job', [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'force_regenerate' => $this->forceRegenerate
            ]);

            // ✅ IMPROVED: Use direct PDF generation instead of getPdfUrl
            $pdfUrl = $pdfService->generateAndStorePdf($this->invoice);

            if (!$pdfUrl) {
                throw new Exception('Failed to generate PDF for invoice ' . $this->invoice->invoice_number);
            }

            Log::info('PDF generated successfully', [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'pdf_url' => $pdfUrl
            ]);

            // ✅ IMPROVED: Refresh invoice model to get updated pdf_url
            // (The pdf_url is already updated in generateAndStorePdf method)
            $this->invoice->refresh();

            // Send notification if requested
            if ($this->shouldNotify && $this->invoice->user) {
                $this->invoice->user->notify(new InvoicePdfGenerated($this->invoice, $pdfUrl));
            }
        } catch (Throwable $e) {
            Log::error('Error in PDF generation job', [
                'invoice_id' => $this->invoice->id ?? null,
                'invoice_number' => $this->invoice->invoice_number ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Rethrow the exception to trigger job retry
            throw $e;
        }
    }

    /**
     * The job failed to process.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        Log::error('PDF generation job failed', [
            'invoice_id' => $this->invoice->id ?? null,
            'invoice_number' => $this->invoice->invoice_number ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}