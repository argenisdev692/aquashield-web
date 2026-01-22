<?php

namespace App\Console\Commands;

use App\Models\InvoiceDemo;
use App\Services\InvoicePdfService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DiagnoseInvoicePdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:diagnose-pdfs {--fix : Fix missing PDF URLs by generating them} {--limit=50 : Limit the number of invoices to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose and optionally fix missing PDF URLs for invoice demos';

    protected InvoicePdfService $pdfService;

    public function __construct(InvoicePdfService $pdfService)
    {
        parent::__construct();
        $this->pdfService = $pdfService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Diagnosing Invoice PDFs...');
        
        $limit = (int) $this->option('limit');
        $shouldFix = $this->option('fix');

        // Get invoices without PDF URLs
        $invoicesWithoutPdf = InvoiceDemo::whereNull('pdf_url')
            ->orWhere('pdf_url', '')
            ->limit($limit)
            ->get();

        $totalInvoices = InvoiceDemo::count();
        $withoutPdfCount = $invoicesWithoutPdf->count();
        $withPdfCount = $totalInvoices - InvoiceDemo::whereNull('pdf_url')->orWhere('pdf_url', '')->count();

        $this->info("📊 Statistics:");
        $this->info("   Total Invoices: {$totalInvoices}");
        $this->info("   With PDF URL: {$withPdfCount}");
        $this->info("   Without PDF URL: {$withoutPdfCount}");
        $this->newLine();

        if ($invoicesWithoutPdf->isEmpty()) {
            $this->info('✅ All invoices have PDF URLs!');
            return;
        }

        $this->info("🚨 Found {$invoicesWithoutPdf->count()} invoices without PDF URLs:");
        
        $headers = ['ID', 'Invoice Number', 'Date', 'Status', 'PDF URL'];
        $rows = [];

        foreach ($invoicesWithoutPdf as $invoice) {
            $rows[] = [
                $invoice->id,
                $invoice->invoice_number,
                $invoice->invoice_date->format('Y-m-d'),
                $invoice->status,
                $invoice->pdf_url ?: 'MISSING'
            ];
        }

        $this->table($headers, $rows);

        if ($shouldFix) {
            $this->newLine();
            $this->info('🔧 Fixing missing PDF URLs...');
            
            $fixed = 0;
            $failed = 0;

            foreach ($invoicesWithoutPdf as $invoice) {
                $this->info("Processing Invoice {$invoice->invoice_number}...");
                
                try {
                    $pdfUrl = $this->pdfService->generateAndStorePdf($invoice);
                    
                    if ($pdfUrl) {
                        $this->info("  ✅ PDF generated: {$pdfUrl}");
                        $fixed++;
                    } else {
                        $this->error("  ❌ Failed to generate PDF");
                        $failed++;
                    }
                } catch (\Exception $e) {
                    $this->error("  ❌ Error: {$e->getMessage()}");
                    $failed++;
                }
            }

            $this->newLine();
            $this->info("📊 Fix Results:");
            $this->info("   Fixed: {$fixed}");
            $this->info("   Failed: {$failed}");
            
            if ($fixed > 0) {
                $this->info('✅ PDF URLs have been generated and saved!');
            }
            
            if ($failed > 0) {
                $this->warn('⚠️  Some PDFs could not be generated. Check logs for details.');
            }
        } else {
            $this->newLine();
            $this->info('💡 To fix missing PDF URLs, run:');
            $this->info('   php artisan invoice:diagnose-pdfs --fix');
        }
    }
} 