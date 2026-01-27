<?php

namespace App\Http\Controllers;

use App\Models\EmailData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\TransactionService;
use App\Traits\CacheTraitCrud;
use Throwable;

class EmailDataController extends BaseCrudController
{
    use CacheTraitCrud;
    
    protected $modelClass = EmailData::class;
    protected $entityName = 'EMAIL_DATA';
    protected $routePrefix = 'email-datas';
    protected $viewPrefix = 'email-datas';
    
    // Override cache time to 1 minute - EmailData changes frequently due to user interactions
    // Trait default is 300 seconds (5 min), but this data needs faster refresh
    protected $cacheTime = 60;

    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        
        // Initialize cache properties with defaults
        $this->initializeCacheProperties();
    }

    /**
     * Get validation rules for email data
     */
    protected function getValidationRules($id = null)
    {
        $emailRule = 'required|email|max:255|unique:email_data,email';
        $phoneRule = 'required|string|max:20|unique:email_data,phone';
        
        // If we have an ID (UUID in this case), exclude it from the unique check
        if ($id) {
            $emailRule .= ',' . $id . ',uuid';
            $phoneRule .= ',' . $id . ',uuid';
        }
        
        return [
            'description' => 'required|string|max:255',
            'email' => $emailRule,
            'phone' => $phoneRule,
            'type' => 'required|in:Support,Sales,General,Technical,Billing,Collections,Admin,Info',
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get validation messages for email data
     */
    protected function getValidationMessages()
    {
        return [
            'description.required' => 'The description is required.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already taken.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'phone.required' => 'The phone number is required.',
            'phone.unique' => 'This phone number is already taken.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'type.required' => 'The type is required.',
            'type.in' => 'The type must be one of: Support, Sales, General, Technical, Billing, Collections, Admin, Info.',
            'user_id.exists' => 'The selected user does not exist.',
        ];
    }

    /**
     * Prepare data for storing an email data
     */
    protected function prepareStoreData(Request $request)
    {
        $formattedPhone = $this->formatPhone($request->phone);
        Log::info('EmailDataController::prepareStoreData - Phone formatting', [
            'original_phone' => $request->phone,
            'formatted_phone' => $formattedPhone
        ]);

        return [
            'uuid' => (string) Str::uuid(),
            'description' => $request->description,
            'email' => strtolower($request->email),
            'phone' => $formattedPhone,
            'type' => $request->type,
            'user_id' => $request->user_id,
        ];
    }

    /**
     * Prepare data for updating an email data
     */
    protected function prepareUpdateData(Request $request)
    {
        $formattedPhone = $this->formatPhone($request->phone);
        Log::info('EmailDataController::prepareUpdateData - Phone formatting', [
            'original_phone' => $request->phone,
            'formatted_phone' => $formattedPhone
        ]);

        return array_filter([
            'description' => $request->description,
            'email' => strtolower($request->email),
            'phone' => $formattedPhone,
            'type' => $request->type,
            'user_id' => $request->user_id,
        ], fn ($value) => !is_null($value));
    }

    /**
     * Display a listing of the email data
     */
    public function index(Request $request)
    {
        // Check permission first - this is critical for security
        if (!$this->checkPermission('READ_EMAIL_DATA', false)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view email data',
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view email data');
        }

        try {
            // Set up cache and search parameters
            $this->search = $request->input('search', '');
            $this->sortField = $request->input('sort_field', 'created_at');
            $this->sortDirection = $request->input('sort_direction', 'desc');
            $this->perPage = $request->input('per_page', 10);
            $this->showDeleted = $request->input('show_deleted', 'false') === 'true';
            
            Log::info('EmailDataController::index - Request parameters:', [
                'all_params' => $request->all(),
                'search_param' => $this->search,
                'has_search' => $request->has('search'),
                'is_empty' => empty($this->search)
            ]);
            
            $page = $request->input('page', 1);
            
            // Use cache for normal views
            $emailData = $this->rememberCrudCache('email_data', function() use ($request, $page) {
                $query = $this->buildEmailDataQuery($request);
                
                // Pagination
                return $query->paginate($this->perPage, ['*'], 'page', $page);
            }, $page);

            if ($request->ajax()) {
                return response()->json($emailData);
            }

            // Get users for the create/edit modals
            $users = User::orderBy('name')->get();

            return view("{$this->viewPrefix}.index", [
                'emailData' => $emailData,
                'entityName' => $this->entityName,
                'users' => $users,
            ]);
        } catch (Throwable $e) {
            Log::error("Error in EmailDataController::index: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading email data',
                ], 500);
            }

            return back()->with('error', 'Error loading email data');
        }
    }

    /**
     * Build the email data query
     */
    private function buildEmailDataQuery(Request $request)
    {
        $query = $this->modelClass::query();

        // Handle search
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm)
                  ->orWhere('type', 'like', $searchTerm);
            });
        }

        // Handle soft deletes
        if ($this->showDeleted) {
            $query->withTrashed();
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        return $query;
    }

    /**
     * Show the form for creating a new email data
     */
    public function create()
    {
        try {
            if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create {$this->entityName}")) {
                return redirect()->route($this->routePrefix . '.index')->with('error', "Permission denied");
            }
            
            $users = User::orderBy('name')->get();
            
            return view("{$this->viewPrefix}.create", [
                'entityName' => $this->entityName,
                'users' => $users,
            ]);
        } catch (Throwable $e) {
            Log::error("Error showing create form for {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            
            return redirect()->route($this->routePrefix . '.index')->with('error', "Error loading create form");
        }
    }

    /**
     * Store a newly created email data
     */
    public function store(Request $request)
    {
        try {
            Log::info('EmailDataController::store - Starting store process', [
                'request_data' => $request->all()
            ]);

            $data = $request->validate($this->getValidationRules());
            Log::info('EmailDataController::store - Validation passed', ['validated_data' => $data]);

            $emailData = $this->transactionService->run(function () use ($request) {
                $preparedData = $this->prepareStoreData($request);
                Log::info('EmailDataController::store - Prepared data', ['prepared_data' => $preparedData]);
                return $this->modelClass::create($preparedData);
            }, function ($emailData) {
                Log::info("{$this->entityName} created successfully", ['id' => $emailData->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('email_data');
                
                $this->afterStore($emailData);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} created successfully",
                    'emailData' => $emailData,
                    'redirectUrl' => route("{$this->routePrefix}.index"),
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} created successfully");
        } catch (Throwable $e) {
            Log::error("Error creating {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error creating {$this->entityName}",
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
     * Show the form for editing the specified email data
     */
    public function edit($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $emailData = $this->modelClass::withTrashed()->where('uuid', $uuid)->first();

            if (!$emailData) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("{$this->entityName} not found");
            }

            if (request()->ajax()) {
                Log::info('EmailDataController::edit - Returning data:', [
                    'emailData' => $emailData->toArray()
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $emailData,
                ]);
            }

            $users = User::orderBy('name')->get();

            return view("{$this->viewPrefix}.edit", [
                'emailData' => $emailData,
                'entityName' => $this->entityName,
                'users' => $users,
            ]);
        } catch (Throwable $e) {
            Log::error("Error retrieving {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error retrieving {$this->entityName}",
                ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }

            return back()->with('error', "Error retrieving {$this->entityName}");
        }
    }

    /**
     * Update the specified email data
     */
    public function update(Request $request, $uuid)
    {
        try {
            Log::info('EmailDataController::update - Starting update process', [
                'uuid' => $uuid,
                'request_data' => $request->all()
            ]);

            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $data = $request->validate($this->getValidationRules($uuid));
            Log::info('EmailDataController::update - Validation passed', ['validated_data' => $data]);

            $emailData = $this->transactionService->run(function () use ($uuid, $request) {
                $emailData = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                Log::info('EmailDataController::update - Found email data', ['current_data' => $emailData->toArray()]);
                
                $preparedData = $this->prepareUpdateData($request);
                Log::info('EmailDataController::update - Prepared data', ['prepared_data' => $preparedData]);
                
                $emailData->update($preparedData);
                return $emailData->fresh();
            }, function ($emailData) {
                Log::info("{$this->entityName} updated successfully", ['id' => $emailData->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('email_data');
                
                $this->afterUpdate($emailData);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully",
                    'emailData' => $emailData,
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
     * Remove the specified email data from storage
     */
    public function destroy($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $emailData = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                $emailData->delete();
                return $emailData;
            }, function ($emailData) {
                Log::info("{$this->entityName} deleted successfully", ['id' => $emailData->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('email_data');
                
                $this->afterDestroy($emailData);
            });

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} deleted successfully",
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} deleted successfully");
        } catch (Throwable $e) {
            Log::error("Error deleting {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error deleting {$this->entityName}",
                ], 500);
            }

            return back()->with('error', "Error deleting {$this->entityName}");
        }
    }

    /**
     * Restore the specified email data
     */
    public function restore($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $emailData = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                $emailData->restore();
                return $emailData;
            }, function ($emailData) {
                Log::info("{$this->entityName} restored successfully", ['id' => $emailData->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('email_data');
                
                $this->afterRestore($emailData);
            });

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} restored successfully",
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} restored successfully");
        } catch (Throwable $e) {
            Log::error("Error restoring {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error restoring {$this->entityName}",
                ], 500);
            }

            return back()->with('error', "Error restoring {$this->entityName}");
        }
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

            $query = EmailData::where('email', strtolower($email));
            
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
            
            // Debug: log what we're receiving and comparing
            Log::info('Phone validation debug:', [
                'received_phone' => $phone,
                'formatted_phone' => $formattedPhone,
                'uuid_to_exclude' => $uuid
            ]);
            
            $query = EmailData::where('phone', $formattedPhone);
            
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
        return 'description';
    }

    /**
     * Get name field for the entity
     */
    protected function getNameField()
    {
        return 'description';
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity)
    {
        return $entity->description . ' (' . $entity->email . ')';
    }

    /**
     * After store hook
     */
    protected function afterStore($emailData)
    {
        // Add any post-creation logic here
        Log::info("EmailData created: {$emailData->description} ({$emailData->email})");
    }

    /**
     * After update hook
     */
    protected function afterUpdate($emailData)
    {
        // Add any post-update logic here
        Log::info("EmailData updated: {$emailData->description} ({$emailData->email})");
    }

    /**
     * After destroy hook
     */
    protected function afterDestroy($emailData)
    {
        // Add any post-deletion logic here
        Log::info("EmailData deleted: {$emailData->description} ({$emailData->email})");
    }

    /**
     * After restore hook
     */
    protected function afterRestore($emailData)
    {
        // Add any post-restoration logic here
        Log::info("EmailData restored: {$emailData->description} ({$emailData->email})");
    }

    /**
     * TEMPORARY - Test new CRUD cache functionality
     */
    public function testCrudCache()
    {
        Log::info('EmailDataController::testCrudCache - Starting CRUD cache test');
        
        // Test cache key generation
        $cacheKey = $this->generateCrudCacheKey('email_data', 1);
        Log::info('EmailDataController::testCrudCache - Generated cache key', ['key' => $cacheKey]);
        
        // Test cache clearing
        $this->markSignificantDataChange();
        $this->clearCrudCache('email_data');
        
        // Test cache remember
        $testData = $this->rememberCrudCache('email_data', function() {
            return ['test' => 'data', 'timestamp' => now()->toDateTimeString()];
        }, 1);
        
        Log::info('EmailDataController::testCrudCache - Cache remember test', ['data' => $testData]);
        
        return response()->json([
            'success' => true,
            'message' => 'CRUD cache test completed - check logs for details',
            'cache_key' => $cacheKey,
            'test_data' => $testData
        ]);
    }
} 