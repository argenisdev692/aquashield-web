<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\TransactionService;
use App\Traits\CacheTraitCrud;
use Throwable;

class ServiceCategoryController extends BaseCrudController
{
    use CacheTraitCrud;
    
    protected $modelClass = ServiceCategory::class;
    protected $entityName = 'SERVICE_CATEGORY';
    protected $routePrefix = 'service-categories';
    protected $viewPrefix = 'service-categories';
    
    // Cache time override: 5 minutes (service categories don't change as frequently)
    protected $cacheTime = 300;

    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        
        // Initialize cache properties with defaults
        $this->initializeCacheProperties();
    }

    /**
     * Get validation rules for service category
     */
    protected function getValidationRules($id = null)
    {
        $categoryRule = 'required|string|min:3|max:100|unique:service_categories,category';
        
        // If we have an ID (UUID in this case), exclude it from the unique check
        if ($id) {
            $categoryRule .= ',' . $id . ',uuid';
        }
        
        return [
            'category' => $categoryRule,
        ];
    }

    /**
     * Get validation messages for service category
     */
    protected function getValidationMessages()
    {
        return [
            'category.required' => 'The category name is required.',
            'category.string' => 'The category name must be a string.',
            'category.min' => 'The category name must be at least 3 characters.',
            'category.max' => 'The category name may not be greater than 100 characters.',
            'category.unique' => 'This category name is already taken.',
        ];
    }

    /**
     * Prepare data for storing a service category
     */
    protected function prepareStoreData(Request $request)
    {
        Log::info('ServiceCategoryController::prepareStoreData - Preparing data', [
            'category' => $request->category
        ]);

        return [
            'uuid' => (string) Str::uuid(),
            'category' => trim($request->category),
            'user_id' => auth()->id(), // Always set to current user
        ];
    }

    /**
     * Prepare data for updating a service category
     */
    protected function prepareUpdateData(Request $request)
    {
        Log::info('ServiceCategoryController::prepareUpdateData - Preparing data', [
            'category' => $request->category
        ]);

        return array_filter([
            'category' => trim($request->category),
            'user_id' => auth()->id(), // Always set to current user
        ], fn ($value) => !is_null($value));
    }

    /**
     * Display a listing of the service categories
     */
    public function index(Request $request)
    {
        // Check permission first - this is critical for security
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view {$this->entityName}")) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view service categories',
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view service categories');
        }

        try {
            // Set up cache and search parameters
            $this->search = $request->input('search', '');
            $this->sortField = $request->input('sort_field', 'created_at');
            $this->sortDirection = $request->input('sort_direction', 'desc');
            $this->perPage = $request->input('per_page', 10);
            $this->showDeleted = $request->input('show_deleted', 'false') === 'true';
            
            Log::info('ServiceCategoryController::index - Request parameters:', [
                'all_params' => $request->all(),
                'search_param' => $this->search,
                'has_search' => $request->has('search'),
                'is_empty' => empty($this->search)
            ]);
            
            $page = $request->input('page', 1);
            
            // Use cache for normal views
            $serviceCategories = $this->rememberCrudCache('service_categories', function() use ($request, $page) {
                $query = $this->buildServiceCategoriesQuery($request);
                
                // Pagination
                return $query->paginate($this->perPage, ['*'], 'page', $page);
            }, $page);

            if ($request->ajax()) {
                return response()->json($serviceCategories);
            }

            return view("{$this->viewPrefix}.index", [
                'serviceCategories' => $serviceCategories,
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error in ServiceCategoryController::index: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading service categories',
                ], 500);
            }

            return back()->with('error', 'Error loading service categories');
        }
    }

    /**
     * Build the service categories query
     */
    private function buildServiceCategoriesQuery(Request $request)
    {
        $query = $this->modelClass::query();

        // Handle search
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('category', 'like', $searchTerm);
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

    // Create method not needed - using modal forms

    /**
     * Store a newly created service category
     */
    public function store(Request $request)
    {
        try {
            Log::info('ServiceCategoryController::store - Starting store process', [
                'request_data' => $request->all()
            ]);

            $data = $request->validate($this->getValidationRules());
            Log::info('ServiceCategoryController::store - Validation passed', ['validated_data' => $data]);

            $serviceCategory = $this->transactionService->run(function () use ($request) {
                $preparedData = $this->prepareStoreData($request);
                Log::info('ServiceCategoryController::store - Prepared data', ['prepared_data' => $preparedData]);
                return $this->modelClass::create($preparedData);
            }, function ($serviceCategory) {
                Log::info("{$this->entityName} created successfully", ['id' => $serviceCategory->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('service_categories');
                
                $this->afterStore($serviceCategory);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} created successfully",
                    'serviceCategory' => $serviceCategory,
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
     * Show the form for editing the specified service category
     */
    public function edit($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $serviceCategory = $this->modelClass::withTrashed()->where('uuid', $uuid)->first();

            if (!$serviceCategory) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("{$this->entityName} not found");
            }

            if (request()->ajax()) {
                Log::info('ServiceCategoryController::edit - Returning data:', [
                    'serviceCategory' => $serviceCategory->toArray()
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $serviceCategory,
                ]);
            }

            return view("{$this->viewPrefix}.edit", [
                'serviceCategory' => $serviceCategory,
                'entityName' => $this->entityName,
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
     * Update the specified service category
     */
    public function update(Request $request, $uuid)
    {
        try {
            Log::info('ServiceCategoryController::update - Starting update process', [
                'uuid' => $uuid,
                'request_data' => $request->all()
            ]);

            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $data = $request->validate($this->getValidationRules($uuid));
            Log::info('ServiceCategoryController::update - Validation passed', ['validated_data' => $data]);

            $serviceCategory = $this->transactionService->run(function () use ($uuid, $request) {
                $serviceCategory = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                Log::info('ServiceCategoryController::update - Found service category', ['current_data' => $serviceCategory->toArray()]);
                
                $preparedData = $this->prepareUpdateData($request);
                Log::info('ServiceCategoryController::update - Prepared data', ['prepared_data' => $preparedData]);
                
                $serviceCategory->update($preparedData);
                return $serviceCategory->fresh();
            }, function ($serviceCategory) {
                Log::info("{$this->entityName} updated successfully", ['id' => $serviceCategory->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('service_categories');
                
                $this->afterUpdate($serviceCategory);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully",
                    'serviceCategory' => $serviceCategory,
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
     * Remove the specified service category from storage
     */
    public function destroy($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $serviceCategory = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                $serviceCategory->delete();
                return $serviceCategory;
            }, function ($serviceCategory) {
                Log::info("{$this->entityName} deleted successfully", ['id' => $serviceCategory->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('service_categories');
                
                $this->afterDestroy($serviceCategory);
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
     * Restore the specified service category
     */
    public function restore($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $serviceCategory = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                $serviceCategory->restore();
                return $serviceCategory;
            }, function ($serviceCategory) {
                Log::info("{$this->entityName} restored successfully", ['id' => $serviceCategory->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('service_categories');
                
                $this->afterRestore($serviceCategory);
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
     * Check if category exists
     */
    public function checkCategoryExists(Request $request)
    {
        try {
            $category = $request->input('category');
            $uuid = $request->input('uuid');

            if (empty($category)) {
                return response()->json(['exists' => false]);
            }

            $query = ServiceCategory::where('category', trim($category));
            
            if ($uuid) {
                $query->where('uuid', '!=', $uuid);
            }

            $exists = $query->exists();

            return response()->json(['exists' => $exists]);
        } catch (Throwable $e) {
            Log::error('Error checking category existence: ' . $e->getMessage());
            return response()->json(['exists' => false], 500);
        }
    }

    /**
     * Get search field for the entity
     */
    protected function getSearchField()
    {
        return 'category';
    }

    /**
     * Get name field for the entity
     */
    protected function getNameField()
    {
        return 'category';
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity)
    {
        return $entity->category;
    }

    /**
     * After store hook
     */
    protected function afterStore($serviceCategory)
    {
        // Add any post-creation logic here
        Log::info("ServiceCategory created: {$serviceCategory->category}");
    }

    /**
     * After update hook
     */
    protected function afterUpdate($serviceCategory)
    {
        // Add any post-update logic here
        Log::info("ServiceCategory updated: {$serviceCategory->category}");
    }

    /**
     * After destroy hook
     */
    protected function afterDestroy($serviceCategory)
    {
        // Add any post-deletion logic here
        Log::info("ServiceCategory deleted: {$serviceCategory->category}");
    }

    /**
     * After restore hook
     */
    protected function afterRestore($serviceCategory)
    {
        // Add any post-restoration logic here
        Log::info("ServiceCategory restored: {$serviceCategory->category}");
    }

    /**
     * TEMPORARY - Test new CRUD cache functionality for Service Categories
     */
    public function testCrudCache()
    {
        Log::info('ServiceCategoryController::testCrudCache - Starting CRUD cache test');
        
        // Test cache key generation
        $cacheKey = $this->generateCrudCacheKey('service_categories', 1);
        Log::info('ServiceCategoryController::testCrudCache - Generated cache key', ['key' => $cacheKey]);
        
        // Test cache clearing
        $this->markSignificantDataChange();
        $this->clearCrudCache('service_categories');
        
        // Test cache remember
        $testData = $this->rememberCrudCache('service_categories', function() {
            return ['test' => 'data', 'timestamp' => now()->toDateTimeString()];
        }, 1);
        
        Log::info('ServiceCategoryController::testCrudCache - Cache remember test', ['data' => $testData]);
        
        return response()->json([
            'success' => true,
            'message' => 'Service Categories CRUD cache test completed - check logs for details',
            'cache_key' => $cacheKey,
            'test_data' => $testData
        ]);
    }

    /**
     * Get service categories for API use (Portfolio CRUD)
     */
    public function getForApi(Request $request)
    {
        try {
            // Check basic read permission
            if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view service categories")) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view service categories',
                ], 403);
            }

            // Get all active service categories
            $serviceCategories = ServiceCategory::select('id', 'category', 'category as service_category_name')
                ->orderBy('category')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $serviceCategories,
            ]);
        } catch (Throwable $e) {
            Log::error("Error in getForApi: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading service categories',
            ], 500);
        }
    }
} 