<?php

namespace App\Services;

use App\Models\InvoiceDemo;
use App\Models\InvoiceDemoItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class InvoiceDemoService
{
    /**
     * Cache duration in seconds (reduced to 5 seconds for immediate updates)
     */
    protected int $cacheTime = 5;

    /**
     * Get paginated invoices with filters and search
     */
    public function getPaginatedInvoices(
        int $page = 1,
        int $perPage = 10,
        string $search = '',
        string $status = '',
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        bool $includeDeleted = false,
        string $startDate = '',
        string $endDate = ''
    ): LengthAwarePaginator {
        // ✅ FIXED: Check for recent data changes to bypass cache
        $hasRecentChanges = Cache::get('significant_data_change', false);
        
        $cacheKey = $this->generateCacheKey('invoices', [
            'page' => $page,
            'per_page' => $perPage,
            'search' => $search,
            'status' => $status,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'include_deleted' => $includeDeleted,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        // ✅ If there are recent changes, skip cache and fetch fresh data
        if ($hasRecentChanges) {
            Log::info('Bypassing cache due to recent data changes');
            Cache::forget($cacheKey);
            $result = $this->fetchInvoicesFromDatabase(
                $page, $perPage, $search, $status, $sortBy, $sortOrder, $includeDeleted, $startDate, $endDate
            );
            // Clear the change flag after fetching fresh data
            Cache::forget('significant_data_change');
            return $result;
        }

        return Cache::remember($cacheKey, $this->cacheTime, function () use (
            $page, $perPage, $search, $status, $sortBy, $sortOrder, $includeDeleted, $startDate, $endDate
        ) {
            return $this->fetchInvoicesFromDatabase(
                $page, $perPage, $search, $status, $sortBy, $sortOrder, $includeDeleted, $startDate, $endDate
            );
        });
    }

    /**
     * ✅ NEW: Extract database fetching logic to separate method
     */
    private function fetchInvoicesFromDatabase(
        int $page,
        int $perPage,
        string $search,
        string $status,
        string $sortBy,
        string $sortOrder,
        bool $includeDeleted,
        string $startDate,
        string $endDate
    ): LengthAwarePaginator {
        $query = InvoiceDemo::with(['user', 'items']);

        // Include soft deleted records if requested
        if ($includeDeleted) {
            $query->withTrashed();
        }

        // Apply search filters
        if (!empty($search)) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('bill_to_name', 'LIKE', "%{$search}%")
                  ->orWhere('bill_to_address', 'LIKE', "%{$search}%")
                  ->orWhere('bill_to_phone', 'LIKE', "%{$search}%")
                  ->orWhere('claim_number', 'LIKE', "%{$search}%")
                  ->orWhere('policy_number', 'LIKE', "%{$search}%")
                  ->orWhere('insurance_company', 'LIKE', "%{$search}%")
                  ->orWhere('type_of_loss', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // Process date filters with enhanced logging
        Log::debug('InvoiceDemoService - Processing date filters', [
            'raw_startDate' => $startDate,
            'raw_endDate' => $endDate,
            'startDate_type' => gettype($startDate),
            'endDate_type' => gettype($endDate),
            'startDate_empty' => empty($startDate),
            'endDate_empty' => empty($endDate),
            'startDate_is_null' => is_null($startDate),
            'endDate_is_null' => is_null($endDate),
            'startDate_is_empty_string' => $startDate === '',
            'endDate_is_empty_string' => $endDate === ''
        ]);
        
        // Check if we have valid date strings
        $hasStartDate = !empty($startDate) && is_string($startDate) && strlen(trim($startDate)) > 0;
        $hasEndDate = !empty($endDate) && is_string($endDate) && strlen(trim($endDate)) > 0;
        
        Log::debug('InvoiceDemoService - Date validation results', [
            'hasStartDate' => $hasStartDate,
            'hasEndDate' => $hasEndDate,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
        
        if ($hasStartDate && $hasEndDate) {
            // Parse dates for validation and logging
            $parsedStartDate = Carbon::parse($startDate);
            $parsedEndDate = Carbon::parse($endDate);
            
            Log::debug('InvoiceDemoService - Parsed dates for comparison', [
                'parsedStartDate' => $parsedStartDate->toDateTimeString(),
                'parsedEndDate' => $parsedEndDate->toDateTimeString(),
                'startDate_gt_endDate' => $parsedStartDate->gt($parsedEndDate)
            ]);
            
            // Ensure startDate is not after endDate
            if ($parsedStartDate->gt($parsedEndDate)) {
                Log::error('Invalid date range: startDate is after endDate', [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);
            } else {
                Log::debug('InvoiceDemoService - Applying whereBetween filter', [
                    'column' => 'invoice_date',
                    'range' => [$startDate, $endDate]
                ]);
                $query->whereBetween('invoice_date', [$startDate, $endDate]);
            }
        } elseif ($hasStartDate) {
            Log::debug('InvoiceDemoService - Applying start date filter only', [
                'column' => 'invoice_date',
                'operator' => '>=',
                'value' => $startDate
            ]);
            $query->whereDate('invoice_date', '>=', $startDate);
        } elseif ($hasEndDate) {
            Log::debug('InvoiceDemoService - Applying end date filter only', [
                'column' => 'invoice_date',
                'operator' => '<=',
                'value' => $endDate
            ]);
            $query->whereDate('invoice_date', '<=', $endDate);
        } else {
            Log::debug('InvoiceDemoService - No date filters applied');
        }

        // Apply sorting
        $allowedSortFields = [
            'invoice_number', 'bill_to_name', 'balance_due', 
            'status', 'invoice_date', 'date_of_loss', 
            'created_at', 'updated_at'
        ];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Create a new invoice demo
     */
    public function createInvoice(array $data, int $userId): InvoiceDemo
    {
        try {
            DB::beginTransaction();

            // Prepare invoice data
            $invoiceData = $this->prepareInvoiceData($data, $userId);
            
            // Separate items data if present
            $itemsData = $data['items'] ?? [];
            unset($invoiceData['items']);

            // Create invoice
            $invoice = InvoiceDemo::create($invoiceData);

            // Create items if provided
            if (!empty($itemsData)) {
                $this->createInvoiceItems($invoice, $itemsData);
            }

            // Calculate totals
            $invoice->calculateTotals();

            // Load relationships for response
            $invoice->load(['user', 'items']);

            DB::commit();

            // Clear related caches
            $this->clearInvoiceCaches();

            // Log activity
            Log::info('Invoice demo created', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => $userId
            ]);

            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create invoice demo', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => $userId
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing invoice demo
     */
    public function updateInvoice(InvoiceDemo $invoice, array $data, int $userId): InvoiceDemo
    {
        try {
            DB::beginTransaction();

            // Store original data for comparison
            $originalData = $invoice->toArray();

            // Prepare invoice data
            $invoiceData = $this->prepareInvoiceData($data, $userId, $invoice->id);
            
            // Separate items data if present
            $itemsData = $data['items'] ?? null;
            unset($invoiceData['items']);

            // Update invoice
            $invoice->update($invoiceData);

            // Update items if provided
            if ($itemsData !== null) {
                $this->updateInvoiceItems($invoice, $itemsData);
            }

            // Calculate totals
            $invoice->calculateTotals();

            // Load relationships for response
            $invoice->load(['user', 'items']);

            DB::commit();

            // Clear related caches
            $this->clearInvoiceCaches();

            // Log activity
            $this->logInvoiceChanges($invoice, $originalData, $userId);

            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update invoice demo', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
                'data' => $data,
                'user_id' => $userId
            ]);
            throw $e;
        }
    }

    /**
     * Delete an invoice demo (soft delete)
     */
    public function deleteInvoice(InvoiceDemo $invoice, int $userId): bool
    {
        try {
            DB::beginTransaction();

            $result = $invoice->delete();

            DB::commit();

            // Clear related caches
            $this->clearInvoiceCaches();

            // Log activity
            Log::info('Invoice demo deleted', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => $userId
            ]);

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete invoice demo', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
                'user_id' => $userId
            ]);
            throw $e;
        }
    }

    /**
     * Restore a deleted invoice demo
     */
    public function restoreInvoice(InvoiceDemo $invoice, int $userId): InvoiceDemo
    {
        try {
            DB::beginTransaction();

            $invoice->restore();
            
            // Refresh the model to get the latest data
            $invoice->refresh();
            
            // Load relationships for response
            $invoice->load(['user', 'items']);

            DB::commit();

            // Clear related caches
            $this->clearInvoiceCaches();

            // Log activity
            Log::info('Invoice demo restored', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => $userId
            ]);

            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore invoice demo', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
                'user_id' => $userId
            ]);
            throw $e;
        }
    }

    /**
     * Get form data for dropdowns
     */
    public function getFormData(): array
    {
        $cacheKey = 'invoice_demo_form_data';

        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return [
                'statuses' => $this->getStatusOptions(),
                'type_of_loss_options' => $this->getTypeOfLossOptions(),
                'common_insurance_companies' => $this->getCommonInsuranceCompanies()
            ];
        });
    }

    /**
     * Check if invoice number exists
     */
    public function invoiceNumberExists(string $invoiceNumber, ?string $excludeId = null): bool
    {
        $query = InvoiceDemo::where('invoice_number', $invoiceNumber);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Generate next available invoice number
     */
    public function generateInvoiceNumber(): string
    {
        return InvoiceDemo::generateInvoiceNumber();
    }

    /**
     * Get invoice statistics
     */
    public function getInvoiceStatistics(): array
    {
        $cacheKey = 'invoice_demo_statistics';

        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $total = InvoiceDemo::count();
            $byStatus = InvoiceDemo::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            $totalAmount = InvoiceDemo::sum('balance_due');
            $averageAmount = InvoiceDemo::avg('balance_due');
            
            $overdue = InvoiceDemo::overdue()->count();
            
            return [
                'total' => $total,
                'by_status' => $byStatus,
                'total_amount' => $totalAmount,
                'average_amount' => $averageAmount,
                'overdue' => $overdue,
                'completion_rate' => $total > 0 ? (($byStatus['paid'] ?? 0) / $total) * 100 : 0
            ];
        });
    }

    /**
     * Create invoice items
     */
    protected function createInvoiceItems(InvoiceDemo $invoice, array $itemsData): void
    {
        foreach ($itemsData as $index => $itemData) {
            $itemData['invoice_demo_id'] = $invoice->id;
            $itemData['sort_order'] = $itemData['sort_order'] ?? $index;
            
            InvoiceDemoItem::create($itemData);
        }
    }

    /**
     * Update invoice items
     */
    protected function updateInvoiceItems(InvoiceDemo $invoice, array $itemsData): void
    {
        // Delete existing items
        $invoice->items()->delete();
        
        // Create new items
        $this->createInvoiceItems($invoice, $itemsData);
    }

    /**
     * Prepare invoice data for storage
     */
    protected function prepareInvoiceData(array $data, int $userId, ?string $excludeId = null): array
    {
        // Set user ID
        $data['user_id'] = $userId;
        
        // Format phone number
        if (isset($data['bill_to_phone'])) {
            $data['bill_to_phone'] = $this->formatPhoneNumber($data['bill_to_phone']);
        }
        
        // Ensure financial amounts are properly formatted
        $financialFields = ['subtotal', 'tax_amount', 'balance_due'];
        foreach ($financialFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (float) $data[$field];
            }
        }
        
        // Format dates
        $dateFields = ['invoice_date', 'date_of_loss', 'date_received', 'date_inspected', 'date_entered'];
        foreach ($dateFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = Carbon::parse($data[$field])->format('Y-m-d');
            }
        }
        
        // Handle datetime fields
        $datetimeFields = ['date_received', 'date_inspected', 'date_entered'];
        foreach ($datetimeFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = Carbon::parse($data[$field])->format('Y-m-d H:i:s');
            }
        }
        
        return $data;
    }

    /**
     * Format phone number
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^\d]/', '', $phone);
        
        // Add country code if missing for US numbers
        if (strlen($cleaned) === 10) {
            $cleaned = '1' . $cleaned;
        }
        
        return $cleaned;
    }

    /**
     * Get status options
     */
    protected function getStatusOptions(): array
    {
        return [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'sent', 'label' => 'Sent'],
            ['value' => 'paid', 'label' => 'Paid'],
            ['value' => 'cancelled', 'label' => 'Cancelled'],
            ['value' => 'print_pdf', 'label' => 'Print PDF']
        ];
    }

    /**
     * Get type of loss options
     */
    protected function getTypeOfLossOptions(): array
    {
        return [
            'Hail Damage',
            'Wind Damage', 
            'Fire Damage',
            'Water Damage',
            'Storm Damage',
            'Vandalism',
            'Theft',
            'Other'
        ];
    }

    /**
     * Get common insurance companies
     */
    protected function getCommonInsuranceCompanies(): array
    {
        return [
            'State Farm',
            'Allstate',
            'GEICO',
            'Progressive',
            'USAA',
            'Liberty Mutual',
            'Farmers',
            'Nationwide',
            'American Family',
            'Travelers'
        ];
    }

    /**
     * ✅ FIXED: Check if invoice number exists (UUID-based validation)
     */
    public function checkInvoiceNumberExists(string $invoiceNumber, ?string $excludeId = null): bool
    {
        $query = InvoiceDemo::where('invoice_number', $invoiceNumber);
        
        if ($excludeId) {
            // ✅ ALWAYS use UUID for exclusion (modern approach)
            $query->where('uuid', '!=', $excludeId);
            
            // ✅ LOG for debugging
            Log::info('Invoice number validation check', [
                'invoice_number' => $invoiceNumber,
                'exclude_uuid' => $excludeId,
                'query' => $query->toSql()
            ]);
        }
        
        $exists = $query->exists();
        
        // ✅ LOG result for debugging
        Log::info('Invoice number validation result', [
            'invoice_number' => $invoiceNumber,
            'exclude_uuid' => $excludeId,
            'exists' => $exists
        ]);
        
        return $exists;
    }

    /**
     * Generate cache key
     */
    protected function generateCacheKey(string $prefix, array $params = []): string
    {
        $key = 'invoice_demo_' . $prefix;
        
        if (!empty($params)) {
            $key .= '_' . md5(serialize($params));
        }
        
        return $key;
    }

    /**
     * ✅ FIXED: Clear invoice-related caches more effectively
     */
    protected function clearInvoiceCaches(): void
    {
        try {
            // ✅ AGGRESSIVE CACHE CLEARING: Use cache flush for immediate effect
            Cache::flush();
            
            // ✅ Also clear specific known cache keys
            $specificKeys = [
                'invoice_demo_form_data',
                'invoice_demo_statistics',
                'invoice_demo_cache_keys',
                'significant_data_change'
            ];
            
            foreach ($specificKeys as $key) {
                Cache::forget($key);
            }
            
            // ✅ Clear any stored cache key registry
            $cacheKeys = Cache::get('invoice_demo_cache_keys', []);
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            
            Log::info('Invoice demo caches cleared aggressively with Cache::flush()');
            
        } catch (\Exception $e) {
            Log::error('Failed to clear invoice caches', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Log invoice changes
     */
    protected function logInvoiceChanges(InvoiceDemo $invoice, array $originalData, int $userId): void
    {
        $changes = [];
        $currentData = $invoice->toArray();
        
        foreach ($currentData as $key => $value) {
            if (isset($originalData[$key]) && $originalData[$key] !== $value) {
                $changes[$key] = [
                    'from' => $originalData[$key],
                    'to' => $value
                ];
            }
        }
        
        if (!empty($changes)) {
            Log::info('Invoice demo updated', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'changes' => $changes,
                'user_id' => $userId
            ]);
        }
    }
}