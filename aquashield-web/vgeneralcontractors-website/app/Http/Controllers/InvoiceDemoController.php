<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Resources\InvoiceDemoResource;
use App\Http\Requests\InvoiceDemoRequest;
use App\Jobs\GenerateInvoicePdf;
use App\Jobs\ProcessInvoiceEmail;
use App\Models\InvoiceDemo;
use App\Services\InvoiceDemoService;
use App\Services\InvoicePdfService;
use App\Services\TransactionService;
use App\Traits\CacheTraitCrud;
use App\Exports\InvoiceDemoExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class InvoiceDemoController extends BaseController
{
    use CacheTraitCrud;
        
    protected int $cacheTime = 3000; // 5 mt
    protected InvoiceDemoService $invoiceService;
    protected InvoicePdfService $pdfService;

    public function __construct(
        InvoiceDemoService $invoiceService, 
        TransactionService $transactionService,
        InvoicePdfService $pdfService
    ) {
        parent::__construct($transactionService);
        $this->invoiceService = $invoiceService;
        $this->pdfService = $pdfService;
        
        // Set properties for parent compatibility
        $this->modelClass = InvoiceDemo::class;
        $this->entityName = 'INVOICE_DEMO';
        $this->viewPrefix = 'invoice-demos';
        $this->routePrefix = 'invoices';
    }

    /**
     * Override index method to use service and resource
     * Enhanced with modern date range handling for Laravel 12 & PHP 8.4
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            // Check permissions using the correct method from BaseController
            if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view invoice demos")) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have permission to view invoice demos',
                    ], 403);
                }
                
                return redirect()->route('dashboard')->with('error', 'You do not have permission to view invoice demos');
            }

            if ($request->ajax() || $request->wantsJson()) {
                // Enhanced parameter validation and sanitization
                $page = max(1, (int) $request->get('page', 1));
                $perPage = min(100, max(5, (int) $request->get('per_page', 10)));
                $search = trim($request->get('search', ''));
                $status = (string) ($request->get('status') ?? '');
                $sortBy = $request->get('sort_by', 'created_at');
                $sortOrder = in_array($request->get('sort_order'), ['asc', 'desc']) ? $request->get('sort_order') : 'desc';
                $includeDeleted = $request->boolean('include_deleted');
                
                // Log raw request parameters for debugging
                Log::debug('InvoiceDemoController - Raw request parameters', [
                    'all_params' => $request->all(),
                    'raw_start_date' => $request->get('start_date'),
                    'raw_end_date' => $request->get('end_date'),
                    'raw_date_range' => $request->get('date_range'),
                    'content_type' => $request->header('Content-Type'),
                    'accept' => $request->header('Accept'),
                    'user_agent' => $request->header('User-Agent')
                ]);
                
                // Enhanced date range handling with validation
                $rawStartDate = $request->get('start_date', '');
                $rawEndDate = $request->get('end_date', '');
                
                // Additional logging for debugging
                Log::debug('InvoiceDemoController - Raw date parameters before validation', [
                    'raw_start_date_value' => $rawStartDate,
                    'raw_end_date_value' => $rawEndDate,
                    'start_date_type' => gettype($rawStartDate),
                    'end_date_type' => gettype($rawEndDate),
                    'start_date_empty' => empty($rawStartDate),
                    'end_date_empty' => empty($rawEndDate)
                ]);
                
                $startDate = $this->validateAndFormatDate($rawStartDate);
                $endDate = $this->validateAndFormatDate($rawEndDate);
                
                // Log validated dates
                Log::debug('InvoiceDemoController - Validated dates', [
                    'validated_start_date' => $startDate,
                    'validated_end_date' => $endDate
                ]);
                
                // Handle predefined date ranges
                $dateRange = $request->get('date_range', '');
                if ($dateRange && !$startDate && !$endDate) {
                    Log::debug('InvoiceDemoController - Using predefined date range', ['date_range' => $dateRange]);
                    [$startDate, $endDate] = $this->getPredefinedDateRange($dateRange);
                    Log::debug('InvoiceDemoController - Predefined date range result', [
                        'calculated_start_date' => $startDate,
                        'calculated_end_date' => $endDate
                    ]);
                }

                $invoices = $this->invoiceService->getPaginatedInvoices(
                    page: $page,
                    perPage: $perPage,
                    search: $search,
                    status: $status,
                    sortBy: $sortBy,
                    sortOrder: $sortOrder,
                    includeDeleted: $includeDeleted,
                    startDate: $startDate,
                    endDate: $endDate
                );

                return response()->json([
                    'success' => true,
                    'data' => [
                        'data' => InvoiceDemoResource::collection($invoices->items()),
                        'current_page' => $invoices->currentPage(),
                        'last_page' => $invoices->lastPage(),
                        'per_page' => $invoices->perPage(),
                        'total' => $invoices->total(),
                        'from' => $invoices->firstItem(),
                        'to' => $invoices->lastItem()
                    ],
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'search' => $search,
                        'status' => $status,
                        'date_range' => $dateRange
                    ]
                ]);
            }

            return view($this->viewPrefix . '.index');
        } catch (Throwable $e) {
            Log::error('Failed to load invoice demos', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load invoice demos'
                ], 500);
            }

            return back()->with('error', 'Failed to load invoice demos');
        }
    }

    /**
     * Validate and format date string
     * @param mixed $date The date input to validate and format
     * @return string Formatted date or empty string if invalid
     */
    private function validateAndFormatDate($date): string
    {
        // Log the raw date input with detailed type information
        Log::debug('validateAndFormatDate - Raw date input', [
            'date' => $date, 
            'type' => gettype($date),
            'is_null' => is_null($date),
            'is_empty_string' => $date === '',
            'is_false' => $date === false,
            'is_zero' => $date === 0,
            'empty_check' => empty($date)
        ]);
        
        // Explicitly handle null and empty strings
        if ($date === null || $date === '') {
            Log::debug('validateAndFormatDate - Empty date, returning empty string');
            return '';
        }

        try {
            // Convert to string if not already
            if (!is_string($date)) {
                $date = (string) $date;
                Log::debug('validateAndFormatDate - Converted non-string to string', ['converted_value' => $date]);
            }
            
            $parsedDate = Carbon::parse($date);
            $formattedDate = $parsedDate->format('Y-m-d');
            
            // Log the parsed and formatted date
            Log::debug('validateAndFormatDate - Date successfully parsed', [
                'raw_date' => $date,
                'parsed_date' => $parsedDate->toDateTimeString(),
                'formatted_date' => $formattedDate
            ]);
            
            return $formattedDate;
        } catch (Throwable $e) {
            Log::warning('Invalid date format provided', [
                'date' => $date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return '';
        }
    }

    /**
     * Get predefined date ranges
     */
    private function getPredefinedDateRange(string $range): array
    {
        $today = Carbon::today();
        
        return match ($range) {
            'today' => [$today->format('Y-m-d'), $today->format('Y-m-d')],
            'yesterday' => [$today->subDay()->format('Y-m-d'), $today->format('Y-m-d')],
            'last_7_days' => [$today->subDays(6)->format('Y-m-d'), Carbon::today()->format('Y-m-d')],
            'last_30_days' => [$today->subDays(29)->format('Y-m-d'), Carbon::today()->format('Y-m-d')],
            'this_month' => [$today->startOfMonth()->format('Y-m-d'), Carbon::today()->endOfMonth()->format('Y-m-d')],
            'last_month' => [$today->subMonth()->startOfMonth()->format('Y-m-d'), $today->endOfMonth()->format('Y-m-d')],
            'this_year' => [$today->startOfYear()->format('Y-m-d'), Carbon::today()->endOfYear()->format('Y-m-d')],
            'last_year' => [$today->subYear()->startOfYear()->format('Y-m-d'), $today->endOfYear()->format('Y-m-d')],
            default => ['', '']
        };
    }

    /**
     * Store a new invoice demo
     */
    public function store(Request $request): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to create invoice demos"
            ], 403);
        }

        // Log the request data for debugging - Enhanced logging
        Log::info('Invoice Demo store request data:', [
            'all_data' => $request->all(),
            'bill_to_phone' => $request->input('bill_to_phone'),
            'invoice_number' => $request->input('invoice_number'),
            'items' => $request->input('items'),
            'headers' => $request->header(),
            'content_type' => $request->header('Content-Type'),
            'request_format' => $request->format(),
            'is_json' => $request->isJson(),
            'is_ajax' => $request->ajax()
        ]);

        try {
            $this->validateRequest($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Invoice Demo validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        // ✅ ADDITIONAL VALIDATION: Prevent duplicate invoice numbers
        $invoiceNumber = $request->input('invoice_number');
        if ($invoiceNumber && $this->invoiceService->checkInvoiceNumberExists($invoiceNumber)) {
            Log::warning('Attempted to create invoice with duplicate number', [
                'invoice_number' => $invoiceNumber,
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => [
                    'invoice_number' => ['This invoice number already exists.']
                ]
            ], 422);
        }

        try {
            $invoice = $this->invoiceService->createInvoice(
                $request->all(),
                auth()->id()
            );
            
            // ✅ IMPROVED: Clear cache immediately after creation
            $this->markSignificantDataChange();
            $this->clearCrudCache('invoice_demos');
            
            // Dispatch jobs in afterStore for better timing
            $this->afterStore($invoice);

            return response()->json([
                'success' => true,
                'message' => 'Invoice demo created successfully',
                'data' => new InvoiceDemoResource($invoice),
                'timestamp' => now()->timestamp // ✅ Add timestamp for cache busting
            ], 201);
        } catch (Throwable $e) {
            Log::error('Failed to create invoice demo', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice demo'
            ], 500);
        }
    }

    /**
     * Update an existing invoice demo
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("UPDATE_{$this->entityName}", "You don't have permission to update invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to update invoice demos"
            ], 403);
        }

        // Log the request data for debugging - Enhanced logging
        Log::info('Invoice Demo update request data:', [
            'uuid' => $uuid,
            'all_data' => $request->all(),
            'bill_to_phone' => $request->input('bill_to_phone'),
            'invoice_number' => $request->input('invoice_number'),
            'items' => $request->input('items'),
            'headers' => $request->header(),
            'content_type' => $request->header('Content-Type'),
            'request_format' => $request->format(),
            'is_json' => $request->isJson(),
            'is_ajax' => $request->ajax()
        ]);

        try {
            $invoice = InvoiceDemo::where('uuid', $uuid)->firstOrFail();
            
            try {
                $this->validateRequest($request, $invoice->id);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Invoice Demo update validation failed:', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all(),
                    'invoice_id' => $invoice->id
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            // ✅ ADDITIONAL VALIDATION: Prevent duplicate invoice numbers on update
            $invoiceNumber = $request->input('invoice_number');
            if ($invoiceNumber && $this->invoiceService->checkInvoiceNumberExists($invoiceNumber, $invoice->uuid)) {
                Log::warning('Attempted to update invoice with duplicate number', [
                    'invoice_number' => $invoiceNumber,
                    'invoice_uuid' => $invoice->uuid,
                    'user_id' => auth()->id(),
                    'request_data' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'invoice_number' => ['This invoice number already exists.']
                    ]
                ], 422);
            }
            
            $updatedInvoice = $this->invoiceService->updateInvoice(
                $invoice,
                $request->all(),
                auth()->id()
            );
            
            // ✅ IMPROVED: Clear cache immediately after update
            $this->markSignificantDataChange();
            $this->clearCrudCache('invoice_demos');
            
            // Dispatch jobs in afterUpdate for better timing
            $this->afterUpdate($updatedInvoice);

            return response()->json([
                'success' => true,
                'message' => 'Invoice demo updated successfully',
                'data' => new InvoiceDemoResource($updatedInvoice),
                'timestamp' => now()->timestamp // ✅ Add timestamp for cache busting
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to update invoice demo', [
                'error' => $e->getMessage(),
                'invoice_uuid' => $uuid,
                'data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice demo'
            ], 500);
        }
    }

    /**
     * Delete invoice demo (soft delete)
     */
    public function destroy(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("DELETE_{$this->entityName}", "You don't have permission to delete invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to delete invoice demos"
            ], 403);
        }

        try {
            $invoice = InvoiceDemo::where('uuid', $uuid)->firstOrFail();
            
            $this->invoiceService->deleteInvoice($invoice, auth()->id());
            
            // ✅ IMPROVED: Clear cache immediately after delete
            $this->markSignificantDataChange();
            $this->clearCrudCache('invoice_demos');
            
            // ✅ IMPROVED: Call afterDelete hook
            $this->afterDelete($invoice);

            return response()->json([
                'success' => true,
                'message' => 'Invoice demo deleted successfully',
                'timestamp' => now()->timestamp // ✅ Add timestamp for cache busting
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to delete invoice demo', [
                'error' => $e->getMessage(),
                'invoice_uuid' => $uuid,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice demo'
            ], 500);
        }
    }

    /**
     * Restore deleted invoice demo
     */
    public function restore(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("RESTORE_{$this->entityName}", "You don't have permission to restore invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to restore invoice demos"
            ], 403);
        }

        try {
            $invoice = InvoiceDemo::withTrashed()->where('uuid', $uuid)->firstOrFail();
            
            $restoredInvoice = $this->invoiceService->restoreInvoice($invoice, auth()->id());
            
            // ✅ IMPROVED: Clear cache immediately after restore
            $this->markSignificantDataChange();
            $this->clearCrudCache('invoice_demos');
            
            // ✅ IMPROVED: Call afterRestore hook
            $this->afterRestore($restoredInvoice);

            return response()->json([
                'success' => true,
                'message' => 'Invoice demo restored successfully',
                'data' => new InvoiceDemoResource($restoredInvoice),
                'timestamp' => now()->timestamp // ✅ Add timestamp for cache busting
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to restore invoice demo', [
                'error' => $e->getMessage(),
                'invoice_uuid' => $uuid,
                'user_id' => auth()->id()
            ]);
             
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore invoice demo'
            ], 500);
        }
    }

    /**
     * Get dropdown data for form selects
     */
    public function getFormData(): JsonResponse
    {
        try {
            $formData = $this->invoiceService->getFormData();
            
            return response()->json([
                'success' => true,
                'data' => $formData
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to get form data for invoice demo', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load form data'
            ], 500);
        }
    }

    /**
     * Check if invoice number exists (for real-time validation)
     */
    public function checkInvoiceNumberExists(Request $request): JsonResponse
    {
        try {
            $invoiceNumber = $request->input('invoice_number');
            $excludeId = $request->input('exclude_id'); // For edit mode
            
            if (!$invoiceNumber) {
                return response()->json(['exists' => false]);
            }
            
            $exists = $this->invoiceService->checkInvoiceNumberExists($invoiceNumber, $excludeId);
            
            return response()->json([
                'exists' => $exists
            ]);
        } catch (Throwable $e) {
            Log::error('Error checking invoice number existence', [
                'error' => $e->getMessage(),
                'invoice_number' => $request->input('invoice_number')
            ]);
            
            return response()->json([
                'exists' => false,
                'error' => 'Unable to check invoice number'
            ], 500);
        }
    }

    /**
     * Generate next invoice number
     */
    public function generateInvoiceNumber(): JsonResponse
    {
        try {
            $invoiceNumber = $this->invoiceService->generateInvoiceNumber();
            
            return response()->json([
                'success' => true,
                'invoice_number' => $invoiceNumber
            ]);
        } catch (Throwable $e) {
            Log::error('Error generating invoice number', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to generate invoice number'
            ], 500);
        }
    }

    // Required abstract methods from BaseController
    protected function getValidationRules(?int $id = null): array
    {
        $rules = (new InvoiceDemoRequest())->rules();

        if ($id) {
            // Overwrite the 'invoice_number' rule to ignore the current record by its primary key.
            // This is necessary because we are not using Form Request Injection,
            // so the InvoiceDemoRequest doesn't have the route context to get the UUID.
            $rules['invoice_number'] = [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('invoice_demos', 'invoice_number')->ignore($id)
            ];
        }

        return $rules;
    }

    protected function getValidationMessages(): array
    {
        $invoiceDemoRequest = new InvoiceDemoRequest();
        return $invoiceDemoRequest->messages();
    }

    // Override methods from BaseController for invoice-specific behavior
    protected function getSearchField(): string
    {
        return 'invoice_number';
    }
    
    protected function getNameField(): string
    {
        return 'invoice_number';
    }

    protected function getEntityDisplayName($entity): string
    {
        return $entity->invoice_number;
    }

    protected function prepareStoreData(Request $request): array
    {
        $data = $request->all();
        $data['user_id'] = auth()->id();
        return $data;
    }
    
    protected function prepareUpdateData(Request $request): array
    {
        return $request->all();
    }
    
    /**
     * Generate and download PDF for an invoice
     */
    public function downloadPdf(string $uuid): Response|JsonResponse
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to view invoice demos"
            ], 403);
        }

        try {
            $invoice = InvoiceDemo::where('uuid', $uuid)->firstOrFail();
            
            // Generate PDF
            $pdf = $this->pdfService->generatePdf($invoice);
            
            if (!$pdf) {
                throw new \Exception('Failed to generate PDF');
            }
            
            // Set filename
            $dateFormatted = $invoice->invoice_date instanceof Carbon 
                ? $invoice->invoice_date->format('Y-m-d') 
                : date('Y-m-d');
            $filename = Str::slug("invoice-{$invoice->invoice_number}-{$dateFormatted}") . '.pdf';
            
            // Return PDF for download
            return $pdf->download($filename);
        } catch (Throwable $e) {
            Log::error('Failed to download invoice PDF', [
                'error' => $e->getMessage(),
                'invoice_uuid' => $uuid,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to download invoice PDF'
            ], 500);
        }
    }
    
    /**
     * View PDF in browser
     */
    public function viewPdf(string $uuid): Response|JsonResponse
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to view invoice demos"
            ], 403);
        }

        try {
            $invoice = InvoiceDemo::where('uuid', $uuid)->firstOrFail();
            
            // Generate PDF
            $pdf = $this->pdfService->generatePdf($invoice);
            
            if (!$pdf) {
                throw new \Exception('Failed to generate PDF');
            }
            
            // Stream PDF to browser
            return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
        } catch (Throwable $e) {
            Log::error('Failed to view invoice PDF', [
                'error' => $e->getMessage(),
                'invoice_uuid' => $uuid,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to view invoice PDF'
            ], 500);
        }
    }
    
    /**
     * Generate PDF and return S3 URL
     */
    public function generatePdf(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to view invoice demos"
            ], 403);
        }

        try {
            $invoice = InvoiceDemo::where('uuid', $uuid)->firstOrFail();
            
            // Queue PDF generation with notification
            GenerateInvoicePdf::dispatch($invoice, true, true);
            
            return response()->json([
                'success' => true,
                'message' => 'PDF generation has been queued. You will be notified when it is ready.'
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to queue invoice PDF generation', [
                'error' => $e->getMessage(),
                'invoice_uuid' => $uuid,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice PDF'
            ], 500);
        }
    }
    
    /**
     * Get PDF URL for an invoice
     */
    public function getPdfUrl(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to view invoice demos"
            ], 403);
        }

        try {
            $invoice = InvoiceDemo::where('uuid', $uuid)->firstOrFail();
            
            if (!$invoice->pdf_url) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF not yet generated for this invoice',
                    'pdf_url' => null
                ]);
            }
            
            return response()->json([
                'success' => true,
                'pdf_url' => $invoice->pdf_url
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to get invoice PDF URL', [
                'error' => $e->getMessage(),
                'invoice_uuid' => $uuid,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get invoice PDF URL'
            ], 500);
        }
    }

    /**
     * ✅ IMPROVED: After store hook with SYNCHRONOUS PDF generation
     */
    protected function afterStore($entity): void
    {
        try {
            $invoice = $entity; // $entity is already an InvoiceDemo instance
            
            // ✅ GENERATE PDF SYNCHRONOUSLY (like images in other controllers)
            $pdfUrl = $this->pdfService->generateAndStorePdf($invoice);
            
            if ($pdfUrl) {
                Log::info('PDF generated synchronously after invoice creation', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'pdf_url' => $pdfUrl
                ]);
                
                // Send email notification AFTER PDF is ready
                ProcessInvoiceEmail::dispatch($invoice, 'new');
            } else {
                Log::warning('PDF generation failed for invoice', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number
                ]);
                
                // Fallback: Queue PDF generation as backup
                GenerateInvoicePdf::dispatch($invoice);
            }
        } catch (Throwable $e) {
            Log::error('Error in synchronous PDF generation after invoice creation', [
                'error' => $e->getMessage(),
                'invoice_id' => $entity->id ?? 'unknown'
            ]);
            
            // Fallback: Queue PDF generation as backup
            GenerateInvoicePdf::dispatch($entity);
        }
    }

    /**
     * ✅ IMPROVED: After update hook with SYNCHRONOUS PDF regeneration
     */
    protected function afterUpdate($entity): void
    {
        try {
            $invoice = $entity; // $entity is already an InvoiceDemo instance
            
            // ✅ REGENERATE PDF SYNCHRONOUSLY (like images in other controllers)
            $pdfUrl = $this->pdfService->generateAndStorePdf($invoice);
            
            if ($pdfUrl) {
                Log::info('PDF regenerated synchronously after invoice update', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'pdf_url' => $pdfUrl
                ]);
                
                // Send email notification AFTER PDF is ready
                ProcessInvoiceEmail::dispatch($invoice, 'updated');
            } else {
                Log::warning('PDF regeneration failed for invoice', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number
                ]);
                
                // Fallback: Queue PDF generation as backup
                GenerateInvoicePdf::dispatch($invoice, true);
            }
        } catch (Throwable $e) {
            Log::error('Error in synchronous PDF regeneration after invoice update', [
                'error' => $e->getMessage(),
                'invoice_id' => $entity->id ?? 'unknown'
            ]);
            
            // Fallback: Queue PDF generation as backup
            GenerateInvoicePdf::dispatch($entity, true);
        }
    }

    /**
     * ✅ NEW: After restore hook
     */
    protected function afterRestore($entity): void
    {
        try {
            $invoice = $entity; // $entity is already an InvoiceDemo instance
            
            Log::info('Invoice restored successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => auth()->id()
            ]);
            
            // Send email notification for restored invoice
            ProcessInvoiceEmail::dispatch($invoice, 'restored');
            
        } catch (Throwable $e) {
            Log::error('Error in afterRestore hook', [
                'error' => $e->getMessage(),
                'invoice_id' => $entity->id ?? 'unknown'
            ]);
        }
    }

    /**
     * ✅ NEW: After delete hook
     */
    protected function afterDelete($entity): void
    {
        try {
            $invoice = $entity; // $entity is already an InvoiceDemo instance
            
            Log::info('Invoice deleted successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => auth()->id()
            ]);
            
            // Send email notification for deleted invoice
            ProcessInvoiceEmail::dispatch($invoice, 'deleted');
            
        } catch (Throwable $e) {
            Log::error('Error in afterDelete hook', [
                'error' => $e->getMessage(),
                'invoice_id' => $entity->id ?? 'unknown'
            ]);
        }
    }

    /**
     * ✅ NEW: Verify PDF status and regenerate if needed
     */
    public function verifyPdfStatus(string $uuid, Request $request): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view invoice demos")) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to view invoice demos"
            ], 403);
        }

        try {
            $invoice = InvoiceDemo::where('uuid', $uuid)->firstOrFail();
            $forceRegenerate = $request->boolean('force_regenerate', false);
            
            $status = [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'has_pdf_url' => !empty($invoice->pdf_url),
                'pdf_url' => $invoice->pdf_url,
                'needs_regeneration' => empty($invoice->pdf_url)
            ];
            
            // If PDF URL is missing or force regeneration is requested
            if (empty($invoice->pdf_url) || $forceRegenerate) {
                Log::info('Regenerating PDF due to missing URL or force flag', [
                    'invoice_id' => $invoice->id,
                    'force_regenerate' => $forceRegenerate,
                    'current_pdf_url' => $invoice->pdf_url
                ]);
                
                $pdfUrl = $this->pdfService->generateAndStorePdf($invoice);
                
                if ($pdfUrl) {
                    $status['pdf_url'] = $pdfUrl;
                    $status['has_pdf_url'] = true;
                    $status['needs_regeneration'] = false;
                    $status['regenerated'] = true;
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'PDF regenerated successfully',
                        'status' => $status
                    ]);
                } else {
                    $status['regeneration_failed'] = true;
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'PDF regeneration failed',
                        'status' => $status
                    ], 500);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'PDF status verified',
                'status' => $status
            ]);
            
        } catch (Throwable $e) {
            Log::error('Failed to verify PDF status', [
                'error' => $e->getMessage(),
                'invoice_uuid' => $uuid,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify PDF status'
            ], 500);
        }
    }

    /**
     * Export invoices to Excel with applied filters
     */
    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to export invoice demos")) {
            abort(403, "You don't have permission to export invoice demos");
        }

        try {
            // Get the same filters used in the index method
            $search = trim($request->get('search', ''));
            $status = (string) ($request->get('status') ?? '');
            $includeDeleted = $request->boolean('include_deleted');
            
            // Enhanced date range handling
            $rawStartDate = $request->get('start_date', '');
            $rawEndDate = $request->get('end_date', '');
            
            $startDate = $this->validateAndFormatDate($rawStartDate);
            $endDate = $this->validateAndFormatDate($rawEndDate);
            
            // Handle predefined date ranges
            $dateRange = $request->get('date_range', '');
            if ($dateRange && !$startDate && !$endDate) {
                [$startDate, $endDate] = $this->getPredefinedDateRange($dateRange);
            }

            $filters = [
                'search' => $search,
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'include_deleted' => $includeDeleted
            ];

            // Get invoices to determine range for filename
            $invoices = $this->invoiceService->getPaginatedInvoices(
                page: 1,
                perPage: 10000, // Large number to get all results
                search: $search,
                status: $status,
                sortBy: 'created_at',
                sortOrder: 'desc',
                includeDeleted: $includeDeleted,
                startDate: $startDate,
                endDate: $endDate
            );

            // Generate descriptive filename with date and invoice range
            $currentDate = now()->format('Y-m-d');
            $filename = "facturas_{$currentDate}";
            
            if ($invoices->count() > 0) {
                $firstInvoice = $invoices->items()[0];
                $lastInvoice = $invoices->items()[$invoices->count() - 1];
                $filename .= "_desde_{$lastInvoice->invoice_number}_hasta_{$firstInvoice->invoice_number}";
            }
            
            if ($search) {
                $filename .= '_busqueda-' . Str::slug($search);
            }
            if ($status) {
                $filename .= '_estado-' . $status;
            }
            if ($startDate || $endDate) {
                $filename .= '_fechas-' . ($startDate ?: 'todas') . '_a_' . ($endDate ?: 'todas');
            }
            
            $filename .= '.xlsx';

            Log::info('Exporting invoices to Excel', [
                'filters' => $filters,
                'filename' => $filename,
                'user_id' => auth()->id()
            ]);

            return Excel::download(new InvoiceDemoExport($filters), $filename);

        } catch (Throwable $e) {
            Log::error('Failed to export invoices to Excel', [
                'error' => $e->getMessage(),
                'filters' => $request->all(),
                'user_id' => auth()->id()
            ]);

            abort(500, 'Failed to export invoices to Excel');
        }
    }

    /**
     * Export invoices to PDF (bulk export)
     */
    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to export invoice demos")) {
            abort(403, "You don't have permission to export invoice demos");
        }

        try {
            // Get the same filters used in the index method
            $search = trim($request->get('search', ''));
            $status = (string) ($request->get('status') ?? '');
            $includeDeleted = $request->boolean('include_deleted');
            
            // Enhanced date range handling
            $rawStartDate = $request->get('start_date', '');
            $rawEndDate = $request->get('end_date', '');
            
            $startDate = $this->validateAndFormatDate($rawStartDate);
            $endDate = $this->validateAndFormatDate($rawEndDate);
            
            // Handle predefined date ranges
            $dateRange = $request->get('date_range', '');
            if ($dateRange && !$startDate && !$endDate) {
                [$startDate, $endDate] = $this->getPredefinedDateRange($dateRange);
            }

            // Get invoices with applied filters
            $invoices = $this->invoiceService->getPaginatedInvoices(
                page: 1,
                perPage: 1000, // Large number to get all results
                search: $search,
                status: $status,
                sortBy: 'created_at',
                sortOrder: 'desc',
                includeDeleted: $includeDeleted,
                startDate: $startDate,
                endDate: $endDate
            );

            // Generate bulk PDF using the PDF service
            $pdfPath = $this->pdfService->generateBulkInvoicesPdf($invoices->items());
            
            if (!$pdfPath || !file_exists($pdfPath)) {
                throw new \Exception('Failed to generate bulk PDF');
            }

            // Generate filename
            $filename = 'invoices_bulk_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            Log::info('Exporting invoices to PDF (bulk)', [
                'count' => $invoices->count(),
                'filename' => $filename,
                'user_id' => auth()->id()
            ]);

            return response()->download($pdfPath, $filename)->deleteFileAfterSend();

        } catch (Throwable $e) {
            Log::error('Failed to export invoices to PDF', [
                'error' => $e->getMessage(),
                'filters' => $request->all(),
                'user_id' => auth()->id()
            ]);

            abort(500, 'Failed to export invoices to PDF');
        }
    }
}