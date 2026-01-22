<?php

namespace App\Services;

use App\Models\InvoiceDemo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class InvoicePdfService
{
    /**
     * Generate and store PDF for an invoice
     *
     * @param InvoiceDemo $invoice
     * @return string|null URL of the stored PDF
     */
    public function generateAndStorePdf(InvoiceDemo $invoice): ?string
    {
        try {
            Log::info('Starting PDF generation and storage', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number
            ]);
            
            // Generate PDF content
            $pdf = $this->generatePdf($invoice);
            
            if (!$pdf) {
                Log::error('PDF generation failed', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number
                ]);
                return null;
            }
            
            // Store PDF in S3
            $pdfUrl = $this->storePdf($pdf, $invoice);
            
            if (!$pdfUrl) {
                Log::error('PDF storage in S3 failed', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number
                ]);
                return null;
            }
            
            // ✅ UPDATE: Save PDF URL to database with verification
            $updateResult = $invoice->update(['pdf_url' => $pdfUrl]);
                
            if (!$updateResult) {
                Log::error('Failed to update PDF URL in database', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'pdf_url' => $pdfUrl
                ]);
                return null;
            }
            
            // ✅ VERIFY: Confirm PDF URL was saved
            $verificationResult = $this->verifyPdfUrlSaved($invoice);
            
            if (!$verificationResult) {
                Log::error('PDF URL verification failed after update', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'pdf_url' => $pdfUrl
                ]);
                return null;
            }
            
            // ✅ CLEAR CACHE: Invalidate cache after successful update
            $this->invalidatePdfCache($invoice);
            
            Log::info('PDF generation and storage completed successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'pdf_url' => $pdfUrl,
                'database_updated' => $updateResult,
                'verification_passed' => $verificationResult
            ]);
            
            return $pdfUrl;
            
        } catch (Throwable $e) {
            Log::error('Error generating and storing invoice PDF', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Generate PDF for an invoice
     *
     * @param InvoiceDemo $invoice
     * @return \Barryvdh\DomPDF\PDF|null
     */
    public function generatePdf(InvoiceDemo $invoice)
    {
        try {
            // Load invoice with its items
            $invoice->load(['items']);
            
            // Get company data
            $company = \App\Models\CompanyData::first();
            
            // Get collections email
            $collectionsEmail = \App\Models\EmailData::where('type', 'Collections')->first();
            
            // Generate PDF using Laravel DomPDF
            $pdf = PDF::loadView('invoice-demos.pdf', [
                'invoice' => $invoice,
                'company' => $company,
                'collectionsEmail' => $collectionsEmail
            ]);
            
            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');
            
            return $pdf;
        } catch (Throwable $e) {
            Log::error('Error generating invoice PDF', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Store PDF in S3
     *
     * @param \Barryvdh\DomPDF\PDF $pdf
     * @param InvoiceDemo $invoice
     * @return string|null URL of the stored PDF
     */
    public function storePdf($pdf, InvoiceDemo $invoice): ?string
    {
        try {
            // Create directory structure based on date: invoices/2023/05/25/
            $now = Carbon::now();
            $storagePath = 'invoices/' . $now->year . '/' . $now->format('m') . '/' . $now->format('d');
            
            // Generate unique filename with invoice number and claim number
            $uniqueId = $this->generateUniqueFilename($invoice);
            $filename = $uniqueId . '.pdf';
            
            // Full path in S3
            $pdfS3Path = $storagePath . '/' . $filename;
            
            // Get PDF content
            $pdfContent = $pdf->output();
            
            // Upload to S3
            Storage::disk('s3')->put($pdfS3Path, $pdfContent);
            
            // Return the PDF URL
            return Storage::disk('s3')->url($pdfS3Path);
        } catch (Throwable $e) {
            Log::error('Error storing invoice PDF', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Generate a unique filename for the invoice PDF
     * Format: {invoice_number}-{client_name}-{datetime}
     *
     * @param InvoiceDemo $invoice
     * @return string
     */
    private function generateUniqueFilename(InvoiceDemo $invoice): string
    {
        // Clean invoice number (remove VG- prefix if present, keep only the number)
        $invoiceNumber = str_replace('VG-', '', $invoice->invoice_number);
        
        // Clean client name (remove special characters, spaces to dashes, limit length)
        $clientName = $invoice->bill_to_name ?? 'client';
        $clientName = Str::slug($clientName); // Converts to URL-friendly format
        $clientName = Str::limit($clientName, 30, ''); // Limit to 30 characters max
        
        // Format datetime - more readable format (YYYYMMDD-HHMMSS)
        $datetime = $invoice->invoice_date instanceof Carbon 
            ? $invoice->invoice_date->format('Ymd-His')
            : Carbon::now()->format('Ymd-His');
        
        // ✅ NEW FORMAT: {invoice_number}-{client_name}-{datetime}
        $baseFilename = $invoiceNumber . '-' . $clientName . '-' . $datetime;
        
        // Clean filename for security (removes any remaining special characters)
        $cleanFilename = Str::slug($baseFilename);
        
        return $cleanFilename;
    }
    
    /**
     * Get PDF URL for an invoice if it exists, or generate a new one
     *
     * @param InvoiceDemo $invoice
     * @param bool $forceRegenerate Whether to force regeneration of the PDF
     * @return string|null URL of the PDF
     */
    public function getPdfUrl(InvoiceDemo $invoice, bool $forceRegenerate = false): ?string
    {
        try {
            // Check if we need to regenerate the PDF
            if ($forceRegenerate) {
                return $this->generateAndStorePdf($invoice);
            }
            
            // Try to find existing PDF
            $existingPdfUrl = $this->findExistingPdf($invoice);
            
            // If found, return it
            if ($existingPdfUrl) {
                return $existingPdfUrl;
            }
            
            // Otherwise generate a new one
            return $this->generateAndStorePdf($invoice);
        } catch (Throwable $e) {
            Log::error('Error getting PDF URL', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Find existing PDF for an invoice
     *
     * @param InvoiceDemo $invoice
     * @return string|null URL of the existing PDF
     */
    public function findExistingPdf(InvoiceDemo $invoice): ?string
    {
        try {
            // Generate the filename pattern
            $filenamePattern = $this->generateUniqueFilename($invoice);
            
            // Search in S3 for matching files
            $files = Storage::disk('s3')->files('invoices');
            
            foreach ($files as $file) {
                if (Str::contains($file, $filenamePattern) && Str::endsWith($file, '.pdf')) {
                    return Storage::disk('s3')->url($file);
                }
            }
            
            return null;
        } catch (Throwable $e) {
            Log::error('Error finding existing PDF', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Delete PDF for an invoice
     *
     * @param InvoiceDemo $invoice
     * @return bool
     */
    public function deletePdf(InvoiceDemo $invoice): bool
    {
        try {
            // Find existing PDF
            $existingPdfUrl = $this->findExistingPdf($invoice);
            
            if (!$existingPdfUrl) {
                return false;
            }
            
            // Extract the path from URL
            $path = $this->getRelativePathFromUrl($existingPdfUrl);
            
            // Delete from S3
            $deleted = Storage::disk('s3')->exists($path) ? Storage::disk('s3')->delete($path) : false;
            
            Log::info('Deleted invoice PDF', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'path' => $path,
                'deleted' => $deleted
            ]);
            
            return $deleted;
        } catch (Throwable $e) {
            Log::error('Error deleting invoice PDF', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Extract relative path from S3 URL
     *
     * @param string $url
     * @return string
     */
    private function getRelativePathFromUrl(string $url): string
    {
        // Remove any query parameters
        $url = strtok($url, '?');
        
        // Parse the URL
        $parsedUrl = parse_url($url);
        
        // Get the path component
        $path = $parsedUrl['path'] ?? '';
        
        // Remove leading slash and bucket name if present
        $path = ltrim($path, '/');
        $bucketName = env('AWS_BUCKET');
        $path = preg_replace("/^{$bucketName}\//", '', $path);
        
        return $path;
    }
    
    /**
     * ✅ NEW: Invalidate cache specifically for PDF operations
     *
     * @param InvoiceDemo $invoice
     * @return void
     */
    protected function invalidatePdfCache(InvoiceDemo $invoice): void
    {
        try {
            // Clear invoice-specific caches
            $cacheKeys = [
                "invoice_demo_invoices_*",
                "invoice_demos_*",
                "crud_cache_invoice_demos_*",
                "invoice_demo_form_data",
                "invoice_demo_statistics",
                "invoice_pdf_{$invoice->id}",
                "invoice_pdf_{$invoice->uuid}"
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            
            // Also trigger general cache invalidation flag
            Cache::forget('significant_data_change');
            Cache::put('significant_data_change', now(), 60); // 1 minute flag
            
            Log::info('PDF cache invalidated successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number
            ]);
        } catch (Throwable $e) {
            Log::error('Error invalidating PDF cache', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * ✅ NEW: Verify that PDF URL was saved correctly
     *
     * @param InvoiceDemo $invoice
     * @return bool
     */
    public function verifyPdfUrlSaved(InvoiceDemo $invoice): bool
    {
        try {
            // Refresh the model to get latest data
            $invoice->refresh();
            
            $hasPdfUrl = !empty($invoice->pdf_url);
            
            Log::info('PDF URL verification', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'has_pdf_url' => $hasPdfUrl,
                'pdf_url' => $invoice->pdf_url
            ]);
            
            return $hasPdfUrl;
            
        } catch (Throwable $e) {
            Log::error('Error verifying PDF URL', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate bulk PDF for multiple invoices
     *
     * @param \Illuminate\Support\Collection|array $invoices
     * @return string|null Path to the generated PDF file
     */
    public function generateBulkInvoicesPdf($invoices): ?string
    {
        try {
            Log::info('Starting bulk PDF generation', [
                'invoice_count' => is_countable($invoices) ? count($invoices) : 0
            ]);

            // Convert to collection if it's an array
            if (is_array($invoices)) {
                $invoices = collect($invoices);
            }

            if ($invoices->isEmpty()) {
                Log::warning('No invoices provided for bulk PDF generation');
                return null;
            }

            // Load invoice items for all invoices
            $invoices->each(function ($invoice) {
                $invoice->load(['items']);
            });

            // Get company data
            $company = \App\Models\CompanyData::first();
            
            // Get collections email
            $collectionsEmail = \App\Models\EmailData::where('type', 'Collections')->first();

            // Generate PDF using Laravel DomPDF with bulk template
            $pdf = PDF::loadView('invoice-demos.bulk-pdf', [
                'invoices' => $invoices,
                'company' => $company,
                'collectionsEmail' => $collectionsEmail
            ]);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');

            // Generate temporary file path
            $tempPath = storage_path('app/temp/bulk_invoices_' . uniqid() . '.pdf');
            
            // Ensure temp directory exists
            $tempDir = dirname($tempPath);
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Save PDF to temporary file
            file_put_contents($tempPath, $pdf->output());

            Log::info('Bulk PDF generation completed', [
                'invoice_count' => $invoices->count(),
                'file_path' => $tempPath,
                'file_size' => file_exists($tempPath) ? filesize($tempPath) : 0
            ]);

            return $tempPath;

        } catch (Throwable $e) {
            Log::error('Error generating bulk invoices PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}