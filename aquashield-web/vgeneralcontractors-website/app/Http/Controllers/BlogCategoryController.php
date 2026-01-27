<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\TransactionService;
use App\Traits\CacheTraitCrud;
use Throwable;

class BlogCategoryController extends BaseCrudController
{
    use CacheTraitCrud;
    
    protected $modelClass = BlogCategory::class;
    protected $entityName = 'BLOG_CATEGORY';
    protected $routePrefix = 'blog-categories';
    protected $viewPrefix = 'blog-categories';
    
    // Cache time override: 10 minutes (blog categories don't change very frequently)
    protected $cacheTime = 600;

    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        
        // Initialize cache properties with defaults
        $this->initializeCacheProperties();
    }

    /**
     * Get validation rules for blog category
     */
    protected function getValidationRules($id = null)
    {
        $categoryNameRule = 'required|string|min:3|max:100|unique:blog_categories,blog_category_name';
        
        // If we have an ID (UUID in this case), exclude it from the unique check
        if ($id) {
            $categoryNameRule .= ',' . $id . ',uuid';
        }
        
        return [
            'blog_category_name' => $categoryNameRule,
            'blog_category_description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get validation messages for blog category
     */
    protected function getValidationMessages()
    {
        return [
            'blog_category_name.required' => 'The category name is required.',
            'blog_category_name.string' => 'The category name must be a string.',
            'blog_category_name.min' => 'The category name must be at least 3 characters.',
            'blog_category_name.max' => 'The category name may not be greater than 100 characters.',
            'blog_category_name.unique' => 'This category name is already taken.',
            'blog_category_description.string' => 'The description must be a string.',
            'blog_category_description.max' => 'The description may not be greater than 500 characters.',
        ];
    }

    /**
     * Prepare data for storing a blog category
     */
    protected function prepareStoreData(Request $request)
    {
        Log::info('BlogCategoryController::prepareStoreData - Preparing data', [
            'blog_category_name' => $request->blog_category_name,
            'blog_category_description' => $request->blog_category_description
        ]);

        return [
            'uuid' => (string) Str::uuid(),
            'blog_category_name' => trim($request->blog_category_name),
            'blog_category_description' => $request->blog_category_description ? trim($request->blog_category_description) : null,
            'user_id' => auth()->id(), // Always set to current user
        ];
    }

    /**
     * Prepare data for updating a blog category
     */
    protected function prepareUpdateData(Request $request)
    {
        Log::info('BlogCategoryController::prepareUpdateData - Preparing data', [
            'blog_category_name' => $request->blog_category_name,
            'blog_category_description' => $request->blog_category_description
        ]);

        $data = array_filter([
            'blog_category_name' => trim($request->blog_category_name),
            'user_id' => auth()->id(), // Always set to current user
        ], fn ($value) => !is_null($value));

        // Allow null description
        if ($request->has('blog_category_description')) {
            $data['blog_category_description'] = $request->blog_category_description ? trim($request->blog_category_description) : null;
        }

        return $data;
    }

    /**
     * Display a listing of the blog categories
     */
    public function index(Request $request)
    {
        // Check permission first - this is critical for security
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view {$this->entityName}")) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view blog categories',
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view blog categories');
        }

        try {
            // Set up cache and search parameters
            $this->search = $request->input('search', '');
            $this->sortField = $request->input('sort_field', 'created_at');
            $this->sortDirection = $request->input('sort_direction', 'desc');
            $this->perPage = $request->input('per_page', 10);
            $this->showDeleted = $request->input('show_deleted', 'false') === 'true';
            
            Log::info('BlogCategoryController::index - Request parameters:', [
                'all_params' => $request->all(),
                'search_param' => $this->search,
                'has_search' => $request->has('search'),
                'is_empty' => empty($this->search)
            ]);
            
            $page = $request->input('page', 1);
            
            // Use cache for normal views
            $blogCategories = $this->rememberCrudCache('blog_categories', function() use ($request, $page) {
                $query = $this->buildBlogCategoriesQuery($request);
                
                // Pagination
                return $query->paginate($this->perPage, ['*'], 'page', $page);
            }, $page);

            if ($request->ajax()) {
                return response()->json($blogCategories);
            }

            return view("{$this->viewPrefix}.index", [
                'blogCategories' => $blogCategories,
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error in BlogCategoryController::index: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading blog categories',
                ], 500);
            }

            return back()->with('error', 'Error loading blog categories');
        }
    }

    /**
     * Build the blog categories query
     */
    private function buildBlogCategoriesQuery(Request $request)
    {
        $query = $this->modelClass::query();

        // Handle search
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('blog_category_name', 'like', $searchTerm)
                  ->orWhere('blog_category_description', 'like', $searchTerm);
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
     * Store a newly created blog category
     */
    public function store(Request $request)
    {
        try {
            Log::info('BlogCategoryController::store - Starting store process', [
                'request_data' => $request->all()
            ]);

            $data = $request->validate($this->getValidationRules());
            Log::info('BlogCategoryController::store - Validation passed', ['validated_data' => $data]);

            $blogCategory = $this->transactionService->run(function () use ($request) {
                $preparedData = $this->prepareStoreData($request);
                Log::info('BlogCategoryController::store - Prepared data', ['prepared_data' => $preparedData]);
                return $this->modelClass::create($preparedData);
            }, function ($blogCategory) {
                Log::info("{$this->entityName} created successfully", ['id' => $blogCategory->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('blog_categories');
                
                $this->afterStore($blogCategory);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} created successfully",
                    'blogCategory' => $blogCategory,
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
     * Show the form for editing the specified blog category
     */
    public function edit($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $blogCategory = $this->modelClass::withTrashed()->where('uuid', $uuid)->first();

            if (!$blogCategory) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("{$this->entityName} not found");
            }

            if (request()->ajax()) {
                Log::info('BlogCategoryController::edit - Returning data:', [
                    'blogCategory' => $blogCategory->toArray()
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $blogCategory,
                ]);
            }

            return view("{$this->viewPrefix}.edit", [
                'blogCategory' => $blogCategory,
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
     * Update the specified blog category
     */
    public function update(Request $request, $uuid)
    {
        try {
            Log::info('BlogCategoryController::update - Starting update process', [
                'uuid' => $uuid,
                'request_data' => $request->all()
            ]);

            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $data = $request->validate($this->getValidationRules($uuid));
            Log::info('BlogCategoryController::update - Validation passed', ['validated_data' => $data]);

            $blogCategory = $this->transactionService->run(function () use ($uuid, $request) {
                $blogCategory = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                Log::info('BlogCategoryController::update - Found blog category', ['current_data' => $blogCategory->toArray()]);
                
                $preparedData = $this->prepareUpdateData($request);
                Log::info('BlogCategoryController::update - Prepared data', ['prepared_data' => $preparedData]);
                
                $blogCategory->update($preparedData);
                return $blogCategory->fresh();
            }, function ($blogCategory) {
                Log::info("{$this->entityName} updated successfully", ['id' => $blogCategory->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('blog_categories');
                
                $this->afterUpdate($blogCategory);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully",
                    'blogCategory' => $blogCategory,
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
     * Remove the specified blog category from storage
     */
    public function destroy($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $blogCategory = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                $blogCategory->delete();
                return $blogCategory;
            }, function ($blogCategory) {
                Log::info("{$this->entityName} deleted successfully", ['id' => $blogCategory->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('blog_categories');
                
                $this->afterDestroy($blogCategory);
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
     * Restore the specified blog category
     */
    public function restore($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $blogCategory = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                $blogCategory->restore();
                return $blogCategory;
            }, function ($blogCategory) {
                Log::info("{$this->entityName} restored successfully", ['id' => $blogCategory->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('blog_categories');
                
                $this->afterRestore($blogCategory);
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
     * Check if category name exists
     */
    public function checkCategoryNameExists(Request $request)
    {
        try {
            $categoryName = $request->input('blog_category_name');
            $uuid = $request->input('uuid');

            if (empty($categoryName)) {
                return response()->json(['exists' => false]);
            }

            $query = BlogCategory::where('blog_category_name', trim($categoryName));
            
            if ($uuid) {
                $query->where('uuid', '!=', $uuid);
            }

            $exists = $query->exists();

            return response()->json(['exists' => $exists]);
        } catch (Throwable $e) {
            Log::error('Error checking category name existence: ' . $e->getMessage());
            return response()->json(['exists' => false], 500);
        }
    }

    /**
     * Get search field for the entity
     */
    protected function getSearchField()
    {
        return 'blog_category_name';
    }

    /**
     * Get name field for the entity
     */
    protected function getNameField()
    {
        return 'blog_category_name';
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity)
    {
        return $entity->blog_category_name;
    }

    /**
     * After store hook
     */
    protected function afterStore($blogCategory)
    {
        // Add any post-creation logic here
        Log::info("BlogCategory created: {$blogCategory->blog_category_name}");
    }

    /**
     * After update hook
     */
    protected function afterUpdate($blogCategory)
    {
        // Add any post-update logic here
        Log::info("BlogCategory updated: {$blogCategory->blog_category_name}");
    }

    /**
     * After destroy hook
     */
    protected function afterDestroy($blogCategory)
    {
        // Add any post-deletion logic here
        Log::info("BlogCategory deleted: {$blogCategory->blog_category_name}");
    }

    /**
     * After restore hook
     */
    protected function afterRestore($blogCategory)
    {
        // Add any post-restoration logic here
        Log::info("BlogCategory restored: {$blogCategory->blog_category_name}");
    }

    /**
     * TEMPORARY - Test new CRUD cache functionality for Blog Categories
     */
    public function testCrudCache()
    {
        Log::info('BlogCategoryController::testCrudCache - Starting CRUD cache test');
        
        // Test cache key generation
        $cacheKey = $this->generateCrudCacheKey('blog_categories', 1);
        Log::info('BlogCategoryController::testCrudCache - Generated cache key', ['key' => $cacheKey]);
        
        // Test cache clearing
        $this->markSignificantDataChange();
        $this->clearCrudCache('blog_categories');
        
        // Test cache remember
        $testData = $this->rememberCrudCache('blog_categories', function() {
            return ['test' => 'data', 'timestamp' => now()->toDateTimeString()];
        }, 1);
        
        Log::info('BlogCategoryController::testCrudCache - Cache remember test', ['data' => $testData]);
        
        return response()->json([
            'success' => true,
            'message' => 'Blog Categories CRUD cache test completed - check logs for details',
            'cache_key' => $cacheKey,
            'test_data' => $testData
        ]);
    }
} 