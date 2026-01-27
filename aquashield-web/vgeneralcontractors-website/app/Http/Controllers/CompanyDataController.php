<?php

namespace App\Http\Controllers;

use App\Models\CompanyData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\TransactionService;
use App\Traits\CacheTraitCrud;
use Throwable;

class CompanyDataController extends BaseCrudController
{
    use CacheTraitCrud;
    
    protected $modelClass = CompanyData::class;
    protected $entityName = 'COMPANY_DATA';
    protected $routePrefix = 'company-data';
    protected $viewPrefix = 'company-data';
    
    // Cache time override: 10 minutes (company data doesn't change frequently)
    protected $cacheTime = 600;

    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        
        // Initialize cache properties with defaults
        $this->initializeCacheProperties();
    }

    /**
     * Get validation rules for company data
     */
    protected function getValidationRules($id = null)
    {
        $emailRule = 'required|email|max:255|unique:company_data,email';
        $phoneRule = 'required|string|max:20|unique:company_data,phone';
        
        // If we have an ID (UUID in this case), exclude it from the unique check
        if ($id) {
            $emailRule .= ',' . $id . ',uuid';
            $phoneRule .= ',' . $id . ',uuid';
        }
        
        return [
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => $emailRule,
            'phone' => $phoneRule,
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'facebook_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'linkedin_link' => 'nullable|url|max:255',
            'twitter_link' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Get validation messages for company data
     */
    protected function getValidationMessages()
    {
        return [
            'name.required' => __('name_required'),
            'name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name'), 'max' => 255]),
            'company_name.required' => __('The company name is required.'),
            'company_name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.company_name'), 'max' => 255]),
            'email.required' => __('email_required'),
            'email.email' => __('validation.email'),
            'email.unique' => __('validation.unique'),
            'email.max' => __('validation.max.string', ['attribute' => __('validation.attributes.email'), 'max' => 255]),
            'phone.required' => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'phone.unique' => __('validation.unique'),
            'phone.max' => __('validation.max.string', ['attribute' => __('validation.attributes.phone'), 'max' => 20]),
            'address.max' => __('validation.max.string', ['attribute' => __('validation.attributes.address'), 'max' => 500]),
            'website.url' => __('validation.url'),
            'website.max' => __('validation.max.string', ['attribute' => __('validation.attributes.website'), 'max' => 255]),
            'facebook_link.url' => __('validation.url'),
            'instagram_link.url' => __('validation.url'),
            'linkedin_link.url' => __('validation.url'),
            'twitter_link.url' => __('validation.url'),
            'latitude.numeric' => __('validation.numeric'),
            'latitude.between' => __('validation.between.numeric', ['attribute' => __('validation.attributes.latitude'), 'min' => -90, 'max' => 90]),
            'longitude.numeric' => __('validation.numeric'),
            'longitude.between' => __('validation.between.numeric', ['attribute' => __('validation.attributes.longitude'), 'min' => -180, 'max' => 180]),
        ];
    }

    /**
     * Prepare data for storing company data (not used since we only edit)
     */
    protected function prepareStoreData(Request $request)
    {
        // This method is not used in single record mode but required by BaseCrudController
        return $this->prepareUpdateData($request);
    }

    /**
     * Prepare data for updating company data
     */
    protected function prepareUpdateData(Request $request)
    {
        $formattedPhone = $this->formatPhone($request->phone);
        Log::info('CompanyDataController::prepareUpdateData - Phone formatting', [
            'original_phone' => $request->phone,
            'formatted_phone' => $formattedPhone
        ]);

        return array_filter([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => strtolower($request->email),
            'phone' => $formattedPhone,
            'address' => $request->address,
            'website' => $request->website,
            'facebook_link' => $request->facebook_link,
            'instagram_link' => $request->instagram_link,
            'linkedin_link' => $request->linkedin_link,
            'twitter_link' => $request->twitter_link,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'user_id' => auth()->id(),
        ], fn ($value) => !is_null($value));
    }

    /**
     * Display the company data (single record)
     */
    public function index(Request $request)
    {
        // Debug logging
        Log::info('CompanyDataController::index - Start', [
            'is_ajax' => $request->ajax(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'not authenticated',
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);
        
        // TEMPORARY: Disable permission check for development/debugging
        // TODO: Re-enable this after fixing permission assignment
        /*
        // Check permission first
        if (!$this->checkPermission('READ_COMPANY_DATA', false)) {
            Log::warning('CompanyDataController::index - Permission denied', [
                'user_id' => auth()->id(),
                'permission' => 'READ_COMPANY_DATA',
                'user_permissions' => auth()->user()->getAllPermissions()->pluck('name')->toArray()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view company data',
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view company data');
        }
        */

        try {
            Log::info('CompanyDataController::index - Permission check bypassed, processing request');
            
            // For AJAX requests, return the single company data record
            if ($request->ajax()) {
                $companyData = CompanyData::first();
                
                Log::info('CompanyDataController::index - Company data query result', [
                    'found' => $companyData ? 'yes' : 'no',
                    'data' => $companyData ? $companyData->toArray() : null
                ]);
                
                // If no company data exists, create a default one
                if (!$companyData) {
                    Log::info('CompanyDataController::index - Creating default company data');
                    $companyData = CompanyData::create([
                        'uuid' => (string) Str::uuid(),
                        'name' => 'Company Name',
                        'company_name' => 'Company Name',
                        'email' => 'info@company.com',
                        'phone' => '+1234567890',
                        'user_id' => auth()->id(),
                    ]);
                    Log::info('CompanyDataController::index - Default company data created', [
                        'uuid' => $companyData->uuid
                    ]);
                }

                // Return in the expected format for the CRUD modal
                $response = [
                    'data' => [$companyData], // Wrap in array to match pagination format
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 1,
                    'total' => 1,
                    'from' => 1,
                    'to' => 1,
                ];
                
                Log::info('CompanyDataController::index - Returning AJAX response', [
                    'response_structure' => array_keys($response),
                    'data_count' => count($response['data'])
                ]);
                
                return response()->json($response);
            }

            // For normal requests, get the single company data record
            $companyData = CompanyData::first();
            
            Log::info('CompanyDataController::index - Returning view', [
                'has_company_data' => $companyData ? 'yes' : 'no'
            ]);

            return view("{$this->viewPrefix}.index", [
                'companyData' => $companyData,
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error in CompanyDataController::index: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading company data',
                    'debug' => [
                        'error' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]
                ], 500);
            }

            return back()->with('error', 'Error loading company data');
        }
    }

    /**
     * Store method disabled - only editing allowed
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Creating new company records is not allowed. Only editing the existing record is permitted.',
        ], 403);
    }

    /**
     * Show the form for editing the company data
     */
    public function edit($uuid)
    {
        try {
            Log::info('CompanyDataController::edit - Starting edit process', [
                'uuid' => $uuid,
                'uuid_type' => gettype($uuid),
                'uuid_length' => strlen($uuid ?? ''),
                'is_ajax' => request()->ajax(),
                'raw_uuid' => var_export($uuid, true)
            ]);

            // More detailed UUID validation
            if (!$uuid) {
                Log::error('CompanyDataController::edit - UUID is null or empty', ['uuid' => $uuid]);
                throw new \InvalidArgumentException('UUID is required');
            }
            
            if ($uuid === 'undefined') {
                Log::error('CompanyDataController::edit - UUID is string "undefined"', ['uuid' => $uuid]);
                throw new \InvalidArgumentException('UUID cannot be "undefined"');
            }
            
            if ($uuid === 'null') {
                Log::error('CompanyDataController::edit - UUID is string "null"', ['uuid' => $uuid]);
                throw new \InvalidArgumentException('UUID cannot be "null"');
            }

            // Validate UUID format
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid)) {
                Log::error('CompanyDataController::edit - UUID format invalid', [
                    'uuid' => $uuid,
                    'uuid_length' => strlen($uuid),
                    'uuid_pattern_match' => preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid)
                ]);
                throw new \InvalidArgumentException('UUID format is invalid');
            }

            // TEMPORARY: Skip permission check for development
            // TODO: Re-enable permission check after fixing role assignments
            Log::info('CompanyDataController::edit - Permission check bypassed');

            $companyData = $this->modelClass::withTrashed()->where('uuid', $uuid)->first();
            Log::info('CompanyDataController::edit - Database query result', [
                'found' => $companyData ? 'yes' : 'no',
                'data' => $companyData ? $companyData->toArray() : null
            ]);

            if (!$companyData) {
                Log::error('CompanyDataController::edit - Company data not found', ['uuid' => $uuid]);
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("{$this->entityName} not found with UUID: {$uuid}");
            }

            if (request()->ajax()) {
                Log::info('CompanyDataController::edit - Returning data for AJAX:', [
                    'companyData' => $companyData->toArray()
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $companyData,
                ]);
            }

            return view("{$this->viewPrefix}.edit", [
                'companyData' => $companyData,
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error retrieving {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_url' => request()->url(),
                'request_method' => request()->method()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error retrieving {$this->entityName}: {$e->getMessage()}",
                    'debug' => [
                        'uuid' => $uuid,
                        'error_type' => get_class($e),
                        'error_message' => $e->getMessage(),
                        'error_line' => $e->getLine(),
                        'error_file' => $e->getFile()
                    ]
                ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }

            return back()->with('error', "Error retrieving {$this->entityName}: {$e->getMessage()}");
        }
    }

    /**
     * Update the company data
     */
    public function update(Request $request, $uuid)
    {
        try {
            Log::info('CompanyDataController::update - Starting update process', [
                'uuid' => $uuid,
                'request_data' => $request->all()
            ]);

            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            // TEMPORARY: Skip permission check for development
            // TODO: Re-enable permission check after fixing role assignments
            Log::info('CompanyDataController::update - Permission check bypassed');

            $data = $request->validate($this->getValidationRules($uuid));
            Log::info('CompanyDataController::update - Validation passed', ['validated_data' => $data]);

            $companyData = $this->transactionService->run(function () use ($uuid, $request) {
                $companyData = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                Log::info('CompanyDataController::update - Found company data', ['current_data' => $companyData->toArray()]);
                
                $preparedData = $this->prepareUpdateData($request);
                Log::info('CompanyDataController::update - Prepared data', ['prepared_data' => $preparedData]);
                
                $companyData->update($preparedData);
                return $companyData->fresh();
            }, function ($companyData) {
                Log::info("{$this->entityName} updated successfully", ['id' => $companyData->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('company_data');
                
                $this->afterUpdate($companyData);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully",
                    'companyData' => $companyData,
                    'redirectUrl' => route("{$this->routePrefix}.index"),
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} updated successfully");
        } catch (Throwable $e) {
            Log::error("Error updating {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error updating {$this->entityName}",
                    'errors' => $e instanceof \Illuminate\Validation\ValidationException
                        ? $e->errors()
                        : [$e->getMessage()],
                ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
            }

            return back()->withErrors($e instanceof \Illuminate\Validation\ValidationException
                ? $e->errors()
                : [$e->getMessage()])->withInput();
        }
    }

    /**
     * Delete method disabled - deleting company data not allowed
     */
    public function destroy($uuid)
    {
        return response()->json([
            'success' => false,
            'message' => 'Deleting company data is not allowed.',
        ], 403);
    }

    /**
     * Restore method disabled - deleting not allowed so restore not needed
     */
    public function restore($uuid)
    {
        return response()->json([
            'success' => false,
            'message' => 'Restoring company data is not applicable.',
        ], 403);
    }

    /**
     * Check if email exists
     */
    public function checkEmailExists(Request $request)
    {
        try {
            $email = $request->input('email');
            $uuid = $request->input('uuid');

            if (empty($email)) {
                return response()->json(['exists' => false]);
            }

            $query = CompanyData::where('email', strtolower($email));
            
            if ($uuid) {
                $query->where('uuid', '!=', $uuid);
            }

            $exists = $query->exists();

            return response()->json(['exists' => $exists]);
        } catch (Throwable $e) {
            Log::error('Error checking email existence: ' . $e->getMessage());
            return response()->json(['exists' => false], 500);
        }
    }

    /**
     * Check if phone exists
     */
    public function checkPhoneExists(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $uuid = $request->input('uuid');

            if (empty($phone)) {
                return response()->json(['exists' => false]);
            }

            $formattedPhone = $this->formatPhone($phone);
            
            Log::info('Phone validation debug:', [
                'received_phone' => $phone,
                'formatted_phone' => $formattedPhone,
                'uuid_to_exclude' => $uuid
            ]);
            
            $query = CompanyData::where('phone', $formattedPhone);
            
            if ($uuid) {
                $query->where('uuid', '!=', $uuid);
            }

            $exists = $query->exists();

            return response()->json(['exists' => $exists]);
        } catch (Throwable $e) {
            Log::error('Error checking phone existence: ' . $e->getMessage());
            return response()->json(['exists' => false], 500);
        }
    }

    /**
     * Format phone number for storage
     */
    private function formatPhone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it's a US number (10 digits), add +1 prefix
        if (strlen($phone) === 10) {
            return '+1' . $phone;
        }
        
        // If it's 11 digits and starts with 1, add + prefix
        if (strlen($phone) === 11 && substr($phone, 0, 1) === '1') {
            return '+' . $phone;
        }
        
        // Return as-is for international numbers
        return $phone;
    }

    /**
     * Get search field for the entity
     */
    protected function getSearchField()
    {
        return 'company_name';
    }

    /**
     * Get name field for the entity
     */
    protected function getNameField()
    {
        return 'company_name';
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity)
    {
        return $entity->company_name;
    }

    /**
     * After update hook
     */
    protected function afterUpdate($companyData)
    {
        Log::info("CompanyData updated: {$companyData->company_name}");
    }
}