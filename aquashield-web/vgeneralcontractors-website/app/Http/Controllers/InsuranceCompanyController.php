<?php

namespace App\Http\Controllers;

use App\Models\InsuranceCompany;
use App\Models\User;
use App\Http\Requests\InsuranceCompanyRequest;
use App\Http\DTOs\InsuranceCompanyDTO;
use App\Services\TransactionService;
use App\Services\InsuranceCompanyService;
use App\Enums\RequestMethod;
use App\Enums\CacheTime;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Traits\CacheTraitCrud;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class InsuranceCompanyController extends BaseController
{
    use CacheTraitCrud;
    
    protected string $modelClass = InsuranceCompany::class;
    protected string $entityName = 'INSURANCE_COMPANY';
    protected string $routePrefix = 'insurance-companies';
    protected string $viewPrefix = 'insurance-companies';
    
    // Cache time for insurance companies - 5 minutes (default is good for this data)
    protected $cacheTime = 300;
    
    protected InsuranceCompanyService $insuranceCompanyService;

    public function __construct(TransactionService $transactionService, InsuranceCompanyService $insuranceCompanyService)
    {
        parent::__construct($transactionService);
        $this->insuranceCompanyService = $insuranceCompanyService;
        
        // Initialize cache properties with defaults
        $this->initializeCacheProperties();
    }

    /**
     * Display a listing of insurance companies with users for modal.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        return match(true) {
            $request->ajax() || $request->wantsJson() => $this->handleAjaxIndexRequest($request),
            default => $this->handleViewIndexRequest()
        };
    }

    protected function handleAjaxIndexRequest(Request $request): JsonResponse
    {
        // Validate date range if provided
        $dateValidation = $this->validateDateRange($request);
        if (!$dateValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $dateValidation['message']
            ], 422);
        }

        // For AJAX requests, use the parent implementation but with user relationship
        $query = InsuranceCompany::with('user');
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('insurance_company_name', 'like', $searchTerm)
                  ->orWhere('address', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm)
                  ->orWhere('website', 'like', $searchTerm);
            });
        }

        // Apply date filters using base controller method
        $this->applyDateFilters($query, $request);
        
        // Apply sorting
        $sortField = $request->input('sort_field', 'insurance_company_name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);
        
        // Show deleted items if requested
        if ($request->has('show_deleted') && $request->show_deleted === 'true') {
            $query->withTrashed();
        }
        
        // Paginate results
        $perPage = (int) $request->input('per_page', 10);
        $entities = $query->paginate($perPage);
        
        // Transform the collection to include user_name using nullsafe operator
        $entities->getCollection()->transform(function ($entity) {
            $entity->user_name = $entity->user ? $entity->user->name . ' ' . $entity->user->last_name : null;
            return $entity;
        });
        
        return response()->json($entities);
    }

    protected function handleViewIndexRequest(): View
    {
        // For view requests, get users for the modal
        $users = User::orderBy('name')->get();
        
        return view($this->viewPrefix . '.index', [
            'users' => $users
        ]);
    }

    /**
     * Check if insurance company name already exists.
     */
    public function checkNameExists(Request $request): JsonResponse
    {
        $name = $request->input('insurance_company_name');
        $excludeUuid = $request->input('uuid') ?: $request->input('exclude_uuid');
        
        if (empty($name)) {
            return response()->json(['valid' => false, 'exists' => false, 'message' => 'Company name is required']);
        }
        
        $exists = $this->insuranceCompanyService->nameExists($name, $excludeUuid);
        
        return response()->json([
            'exists' => $exists,
            'valid' => !$exists,
            'message' => match($exists) {
                true => 'This company name is already registered',
                false => 'Company name is available'
            }
        ]);
    }

    /**
     * Check if email already exists.
     */
    public function checkEmailExists(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $excludeUuid = $request->input('uuid');
        
        $exists = $this->insuranceCompanyService->emailExists($email, $excludeUuid);
        
        return response()->json(['exists' => $exists]);
    }

    /**
     * Check if phone already exists.
     */
    public function checkPhoneExists(Request $request): JsonResponse
    {
        $phone = $request->input('phone');
        $excludeUuid = $request->input('uuid');
        
        // Format phone for checking
        $formattedPhone = $this->formatPhoneForStorage($phone);
        $exists = $this->insuranceCompanyService->phoneExists($formattedPhone, $excludeUuid);
        
        return response()->json(['exists' => $exists]);
    }

    /**
     * Get form data for dropdowns
     */
    public function getFormData(): JsonResponse
    {
        $users = User::orderBy('name')
                    ->select('id', 'name', 'email')
                    ->get()
                    ->map(function ($user) {
                        return [
                            'value' => $user->id,
                            'text' => $user->name . ' (' . $user->email . ')'
                        ];
                    });

        return response()->json([
            'users' => $users
        ]);
    }

    // Validation is now handled entirely by InsuranceCompanyRequest
    // These methods are required by BaseController but not used since we use FormRequest validation
    
    /**
     * Get validation rules - Not used, validation handled by InsuranceCompanyRequest
     */
    protected function getValidationRules(?int $id = null): array
    {
        // This method is required by BaseController but not used
        // Validation is handled by InsuranceCompanyRequest
        return [];
    }

    /**
     * Get validation messages - Not used, validation handled by InsuranceCompanyRequest
     */
    protected function getValidationMessages(): array
    {
        // This method is required by BaseController but not used
        // Validation is handled by InsuranceCompanyRequest
        return [];
    }

    /**
     * Get the search field for the entity
     */
    protected function getSearchField(): string
    {
        return 'insurance_company_name';
    }

    /**
     * Get the name field for the entity
     */
    protected function getNameField(): string
    {
        return 'insurance_company_name';
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity): string
    {
        return $entity->insurance_company_name . ' (' . $entity->email . ')';
    }

    /**
     * Prepare data for storing
     */
    protected function prepareStoreData(Request $request): array
    {
        $formattedPhone = $this->formatPhoneForStorage($request->phone);
        $formattedWebsite = $this->formatWebsite($request->website);
        
        // Ensure user_id is always set
        $userId = $request->user_id ?? auth()->id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }
        
        Log::info('InsuranceCompanyController::prepareStoreData', [
            'original_phone' => $request->phone,
            'formatted_phone' => $formattedPhone,
            'original_website' => $request->website,
            'formatted_website' => $formattedWebsite,
            'user_id' => $userId
        ]);

        return [
            'uuid' => (string) Str::uuid(),
            'insurance_company_name' => trim($request->insurance_company_name),
            'address' => trim($request->address),
            'phone' => $formattedPhone,
            'email' => strtolower(trim($request->email)),
            'website' => $formattedWebsite,
            'user_id' => $userId,
        ];
    }

    /**
     * Prepare data for updating
     */
    protected function prepareUpdateData(Request $request): array
    {
        $formattedPhone = $this->formatPhoneForStorage($request->phone);
        $formattedWebsite = $this->formatWebsite($request->website);
        
        // Ensure user_id is always set
        $userId = $request->user_id ?? auth()->id();
        
        if (!$userId) {
            throw new \Exception('User not authenticated');
        }
        
        Log::info('InsuranceCompanyController::prepareUpdateData', [
            'original_phone' => $request->phone,
            'formatted_phone' => $formattedPhone,
            'original_website' => $request->website,
            'formatted_website' => $formattedWebsite,
            'user_id' => $userId
        ]);

        return array_filter([
            'insurance_company_name' => trim($request->insurance_company_name),
            'address' => trim($request->address),
            'phone' => $formattedPhone,
            'email' => strtolower(trim($request->email)),
            'website' => $formattedWebsite,
            'user_id' => $userId,
        ], fn ($value) => !is_null($value));
    }



    /**
     * Format phone number for storage
     */
    private function formatPhoneForStorage(string $phone): string
    {
        // If phone is already in (xxx) xxx-xxxx format, keep it as is
        if (preg_match('/^\(\d{3}\)\s\d{3}-\d{4}$/', $phone)) {
            return $phone;
        }
        
        // Remove all non-digits
        $cleaned = preg_replace('/\D/', '', $phone);
        
        // If it's 10 digits, format to (xxx) xxx-xxxx
        if (strlen($cleaned) === 10) {
            return '(' . substr($cleaned, 0, 3) . ') ' . substr($cleaned, 3, 3) . '-' . substr($cleaned, 6, 4);
        }
        
        // If it's 11 digits and starts with 1, remove the 1 and format
        if (strlen($cleaned) === 11 && str_starts_with($cleaned, '1')) {
            $cleaned = substr($cleaned, 1);
            return '(' . substr($cleaned, 0, 3) . ') ' . substr($cleaned, 3, 3) . '-' . substr($cleaned, 6, 4);
        }
        
        return $cleaned;
    }

    /**
     * Format website URL
     */
    private function formatWebsite(?string $website): ?string
    {
        if (empty($website)) {
            return null;
        }
        
        $website = trim($website);
        
        // Add https:// if no protocol specified
        if (!preg_match('/^https?:\/\//', $website)) {
            $website = 'https://' . $website;
        }
        
        return $website;
    }

    /**
     * Validate request data using InsuranceCompanyRequest rules
     */
    private function validateInsuranceCompanyRequest(Request $request, ?string $uuid = null): array
    {
        // Create an instance of InsuranceCompanyRequest to get validation rules
        $formRequest = new InsuranceCompanyRequest();
        
        // Simulate the route parameter for unique validation
        if ($uuid) {
            $request->route()->setParameter('insurance_company', $uuid);
        }
        
        // Get validation rules
        $rules = $formRequest->rules();
        $messages = $formRequest->messages();
        $attributes = $formRequest->attributes();
        
        // Add user_id if not present
        $requestData = $request->all();
        if (!isset($requestData['user_id'])) {
            $requestData['user_id'] = auth()->id();
        }
        
        // Perform validation
        $validator = Validator::make($requestData, $rules, $messages, $attributes);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Store a new insurance company - BaseController compatible method
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate using InsuranceCompanyRequest manually
            $validatedData = $this->validateInsuranceCompanyRequest($request);
            
            // Create DTO from validated data
            $dto = InsuranceCompanyDTO::from($validatedData);
            
            // Create insurance company using service with DTO data
            $insuranceCompany = $this->insuranceCompanyService->create($dto->toDatabase());
            
            return response()->json([
                'success' => true,
                'message' => __('insurance_companies.messages.created_successfully'),
                'data' => $insuranceCompany,
            ]);
            
        } catch (ValidationException $e) {
            Log::error('InsuranceCompanyController::store validation failed', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('insurance_companies.messages.validation_failed'),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('InsuranceCompanyController::store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('insurance_companies.messages.creation_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing insurance company - BaseController compatible method
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        try {
            // Find the existing insurance company
            $insuranceCompany = InsuranceCompany::where('uuid', $uuid)->firstOrFail();
            
            // Validate using InsuranceCompanyRequest manually for updates
            $validatedData = $this->validateInsuranceCompanyRequest($request, $uuid);
            
            // Add UUID to validated data for DTO
            $validatedData['uuid'] = $uuid;
            
            // Create DTO from validated data
            $dto = InsuranceCompanyDTO::from($validatedData);
            
            // Update using the service with DTO data
            $updatedInsuranceCompany = $this->insuranceCompanyService->update($insuranceCompany, $dto->toDatabase());
            
            return response()->json([
                'success' => true,
                'message' => __('insurance_companies.messages.updated_successfully'),
                'data' => $updatedInsuranceCompany,
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('InsuranceCompanyController::update - Company not found', [
                'uuid' => $uuid,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('insurance_companies.messages.not_found')
            ], 404);
            
        } catch (ValidationException $e) {
            Log::error('InsuranceCompanyController::update validation failed', [
                'uuid' => $uuid,
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('insurance_companies.messages.validation_failed'),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('InsuranceCompanyController::update error', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('insurance_companies.messages.update_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update using FormRequest injection (alternative method)
     */
    public function updateWithFormRequest(InsuranceCompanyRequest $request, string $uuid): JsonResponse
    {
        try {
            // Find the existing insurance company
            $insuranceCompany = InsuranceCompany::where('uuid', $uuid)->firstOrFail();
            
            // The request is automatically validated by Laravel
            // Convert validated request to DTO
            $dto = $request->toDTO();
            
            // Update using the service with DTO data
            $updatedInsuranceCompany = $this->insuranceCompanyService->update($insuranceCompany, $dto->toDatabase());
            
            return response()->json([
                'success' => true,
                'message' => __('insurance_companies.messages.updated_successfully'),
                'data' => $updatedInsuranceCompany,
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('InsuranceCompanyController::updateWithFormRequest - Company not found', [
                'uuid' => $uuid,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('insurance_companies.messages.not_found')
            ], 404);
            
        } catch (ValidationException $e) {
            Log::error('InsuranceCompanyController::updateWithFormRequest validation failed', [
                'uuid' => $uuid,
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('insurance_companies.messages.validation_failed'),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('InsuranceCompanyController::updateWithFormRequest error', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('insurance_companies.messages.update_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }



    public function show(string $uuid): JsonResponse
    {
        $insuranceCompany = $this->insuranceCompanyService->findByUuid($uuid);
        if (!$insuranceCompany) {
            return response()->json(['success' => false, 'message' => 'Insurance company not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $insuranceCompany]);
    }

    public function edit(string $uuid): JsonResponse
    {
        $insuranceCompany = $this->insuranceCompanyService->findByUuid($uuid);
        if (!$insuranceCompany) {
            return response()->json(['success' => false, 'message' => 'Insurance company not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $insuranceCompany]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $deleted = $this->insuranceCompanyService->deleteByUuid($uuid);
        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Unable to delete insurance company'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Insurance company deleted successfully']);
    }

    public function restore(string $uuid): JsonResponse
    {
        $restored = $this->insuranceCompanyService->restoreByUuid($uuid);
        if (!$restored) {
            return response()->json(['success' => false, 'message' => 'Unable to restore insurance company'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Insurance company restored successfully']);
    }

    /**
     * Check if email is unique for AJAX validation
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $uuid = $request->input('uuid') ?: $request->input('exclude_uuid'); // For update operations
        
        // If email is empty, it's valid since it's optional
        if (empty($email)) {
            return response()->json(['valid' => true, 'exists' => false, 'message' => 'Email is optional']);
        }
        
        $query = InsuranceCompany::where('email', $email);
        
        // If updating, exclude current record
        if ($uuid) {
            $query->where('uuid', '!=', $uuid);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'valid' => !$exists,
            'message' => match($exists) {
                true => 'This email is already registered',
                false => 'Email is available'
            }
        ]);
    }

    /**
     * Export insurance companies to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $searchFilters = [
                'search' => $request->input('search'),
                'sort_field' => $request->input('sort_field', 'insurance_company_name'),
                'sort_direction' => $request->input('sort_direction', 'asc'),
                'show_deleted' => $request->input('show_deleted', false),
            ];

            $dateFilters = [
                'start_date' => $request->input('date_start'),
                'end_date' => $request->input('date_end'),
            ];

            $export = new \App\Exports\Excel\InsuranceCompanyExport(null, $searchFilters, $dateFilters);
            
            $filename = 'insurance_companies_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download($export, $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting insurance companies to Excel: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generating Excel export: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error generating Excel export: ' . $e->getMessage());
        }
    }

    /**
     * Export insurance companies to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $searchFilters = [
                'search' => $request->input('search'),
                'sort_field' => $request->input('sort_field', 'insurance_company_name'),
                'sort_direction' => $request->input('sort_direction', 'asc'),
                'show_deleted' => $request->input('show_deleted', false),
            ];

            $dateFilters = [
                'start_date' => $request->input('date_start'),
                'end_date' => $request->input('date_end'),
            ];

            $export = new \App\Exports\PDF\InsuranceCompanyExportPDF(null, $searchFilters, $dateFilters);
            
            $filename = 'insurance_companies_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return $export->download($filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting insurance companies to PDF: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generating PDF export: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error generating PDF export: ' . $e->getMessage());
        }
    }

    /**
     * Bulk export with multiple formats and filters
     */
    public function bulkExport(Request $request)
    {
        try {
            $request->validate([
                'format' => 'required|in:excel,pdf',
                'filters' => 'nullable|array',
                'date_filters' => 'nullable|array',
                'export_options' => 'nullable|array'
            ]);

            $format = $request->input('format');
            $searchFilters = $request->input('filters', []);
            $dateFilters = $request->input('date_filters', []);
            $exportOptions = $request->input('export_options', []);

            if ($format === 'excel') {
                $export = new \App\Exports\Excel\InsuranceCompanyExport(null, $searchFilters, $dateFilters);
                $filename = 'insurance_companies_bulk_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                return Excel::download($export, $filename);
            } else {
                $export = new \App\Exports\PDF\InsuranceCompanyExportPDF(null, $searchFilters, $dateFilters, $exportOptions);
                $filename = 'insurance_companies_bulk_' . now()->format('Y-m-d_H-i-s') . '.pdf';
                return $export->download($filename);
            }

        } catch (\Exception $e) {
            Log::error('Error in bulk export: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating bulk export: ' . $e->getMessage()
            ], 500);
        }
    }


}
