<?php

namespace App\Http\Controllers;

use App\Models\ModelAI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\TransactionService;
use App\Traits\CacheTraitCrud;
use Throwable;

class ModelAIController extends BaseCrudController
{
    use CacheTraitCrud;
    
    protected $modelClass = ModelAI::class;
    protected $entityName = 'MODEL_AI';
    protected $routePrefix = 'model-ais';
    protected $viewPrefix = 'model-ais';
    
    // Override cache time to 5 minutes - ModelAI data doesn't change frequently
    protected $cacheTime = 300;

    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        
        // Initialize cache properties with defaults
        $this->initializeCacheProperties();
    }

    /**
     * Get validation rules for model AI
     */
    protected function getValidationRules($id = null)
    {
        $nameRule = 'required|string|max:255|unique:model_a_i_s,name';
        
        // If we have an ID (UUID in this case), exclude it from the unique check
        if ($id) {
            $nameRule .= ',' . $id . ',uuid';
        }
        
        return [
            'name' => $nameRule,
            'email' => 'required|email|max:255',
            'type' => 'required|in:Content,Image,Mixed',
            'description' => 'nullable|string|max:1000',
            'api_key' => 'required|string|max:1000',
        ];
    }

    /**
     * Get validation messages for model AI
     */
    protected function getValidationMessages()
    {
        return [
            'name.required' => 'The name is required.',
            'name.unique' => 'This model name is already taken.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'type.required' => 'The type is required.',
            'type.in' => 'The type must be one of: Content, Image, Mixed.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'api_key.required' => 'The API key is required.',
            'api_key.max' => 'The API key may not be greater than 1000 characters.',
        ];
    }

    /**
     * Prepare data for storing a model AI
     */
    protected function prepareStoreData(Request $request)
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'description' => $request->description,
            'api_key' => $request->api_key,
            'user_id' => auth()->id(),
        ];
    }

    /**
     * Prepare data for updating a model AI
     */
    protected function prepareUpdateData(Request $request)
    {
        return array_filter([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'description' => $request->description,
            'api_key' => $request->api_key,
            // user_id no se actualiza, se mantiene el original
        ], fn ($value) => !is_null($value));
    }

    /**
     * Display a listing of the model AIs
     */
    public function index(Request $request)
    {
        // Check permission first - this is critical for security
        if (!$this->checkPermission('READ_MODEL_AI', false)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view AI models',
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view AI models');
        }

        try {
            // Set up cache and search parameters
            $this->search = $request->input('search', '');
            $this->sortField = $request->input('sort_field', 'created_at');
            $this->sortDirection = $request->input('sort_direction', 'desc');
            $this->perPage = $request->input('per_page', 10);
            $this->showDeleted = $request->input('show_deleted', 'false') === 'true';
            
            Log::info('ModelAIController::index - Request parameters:', [
                'all_params' => $request->all(),
                'search_param' => $this->search,
                'has_search' => $request->has('search'),
                'is_empty' => empty($this->search)
            ]);
            
            $page = $request->input('page', 1);
            
            // Use cache for normal views
            $modelAIs = $this->rememberCrudCache('model_ais', function() use ($request, $page) {
                $query = $this->buildModelAIQuery($request);
                
                // Pagination
                return $query->paginate($this->perPage, ['*'], 'page', $page);
            }, $page);

            if ($request->ajax()) {
                // Transform data to include user_name for the JavaScript table
                $transformedData = $modelAIs->getCollection()->map(function ($modelAI) {
                    $data = $modelAI->toArray();
                    $data['user_name'] = $modelAI->user ? $modelAI->user->name : 'N/A';
                    return $data;
                });
                $modelAIs->setCollection($transformedData);
                
                return response()->json($modelAIs);
            }

            return view("{$this->viewPrefix}.index", [
                'modelAIs' => $modelAIs,
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error in ModelAIController::index: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading AI models',
                ], 500);
            }

            return back()->with('error', 'Error loading AI models');
        }
    }

    /**
     * Build the model AI query
     */
    private function buildModelAIQuery(Request $request)
    {
        $query = $this->modelClass::query()->with('user:id,name');

        // Handle search
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('type', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
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
     * Show the form for creating a new model AI
     */
    public function create()
    {
        try {
            if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create {$this->entityName}")) {
                return redirect()->route($this->routePrefix . '.index')->with('error', "Permission denied");
            }
            
            return view("{$this->viewPrefix}.create", [
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error showing create form for {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            
            return redirect()->route($this->routePrefix . '.index')->with('error', "Error loading create form");
        }
    }

    /**
     * Store a newly created model AI
     */
    public function store(Request $request)
    {
        try {
            Log::info('ModelAIController::store - Starting store process', [
                'request_data' => $request->all()
            ]);

            $data = $request->validate($this->getValidationRules());
            Log::info('ModelAIController::store - Validation passed', ['validated_data' => $data]);

            $modelAI = $this->transactionService->run(function () use ($request) {
                $preparedData = $this->prepareStoreData($request);
                Log::info('ModelAIController::store - Prepared data', ['prepared_data' => $preparedData]);
                return $this->modelClass::create($preparedData);
            }, function ($modelAI) {
                Log::info("{$this->entityName} created successfully", ['id' => $modelAI->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('model_ais');
                
                $this->afterStore($modelAI);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} created successfully",
                    'modelAI' => $modelAI,
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
     * Show the form for editing the specified model AI
     */
    public function edit($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $modelAI = ModelAI::withTrashed()->with('user:id,name')->where('uuid', $uuid)->firstOrFail();

            if (!$this->checkPermissionWithMessage("UPDATE_{$this->entityName}", "You don't have permission to edit {$this->entityName}")) {
                return $this->respondWithPermissionError();
            }

            if (request()->ajax()) {
                Log::info('ModelAIController::edit - Returning data:', [
                    'modelAI' => $modelAI->toArray()
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $modelAI,
                ]);
            }

            return view("{$this->viewPrefix}.edit", [
                'modelAI' => $modelAI,
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
     * Update the specified model AI
     */
    public function update(Request $request, $uuid)
    {
        try {
            Log::info('ModelAIController::update - Starting update process', [
                'uuid' => $uuid,
                'request_data' => $request->all()
            ]);

            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $data = $request->validate($this->getValidationRules($uuid));
            Log::info('ModelAIController::update - Validation passed', ['validated_data' => $data]);

            $modelAI = $this->transactionService->run(function () use ($uuid, $request) {
                $modelAI = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                Log::info('ModelAIController::update - Found model AI', ['current_data' => $modelAI->toArray()]);
                
                $preparedData = $this->prepareUpdateData($request);
                Log::info('ModelAIController::update - Prepared data', ['prepared_data' => $preparedData]);
                
                $modelAI->update($preparedData);
                return $modelAI->fresh();
            }, function ($modelAI) {
                Log::info("{$this->entityName} updated successfully", ['id' => $modelAI->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('model_ais');
                
                $this->afterUpdate($modelAI);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully",
                    'modelAI' => $modelAI,
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
     * Remove the specified model AI from storage
     */
    public function destroy($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $modelAI = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                $modelAI->delete();
                return $modelAI;
            }, function ($modelAI) {
                Log::info("{$this->entityName} deleted successfully", ['id' => $modelAI->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('model_ais');
                
                $this->afterDestroy($modelAI);
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
     * Restore the specified model AI
     */
    public function restore($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $modelAI = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                $modelAI->restore();
                return $modelAI;
            }, function ($modelAI) {
                Log::info("{$this->entityName} restored successfully", ['id' => $modelAI->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('model_ais');
                
                $this->afterRestore($modelAI);
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
     * Check if name exists
     */
    public function checkNameExists(Request $request)
    {
        try {
            $name = $request->input('name');
            $uuid = $request->input('uuid');

            if (empty($name)) {
                return response()->json(['exists' => false]);
            }

            $query = ModelAI::where('name', $name);
            
            if ($uuid) {
                $query->where('uuid', '!=', $uuid);
            }

            $exists = $query->exists();

            return response()->json(['exists' => $exists]);
        } catch (Throwable $e) {
            Log::error('Error checking name existence: ' . $e->getMessage());
            return response()->json(['exists' => false], 500);
        }
    }

    /**
     * Get search field for the entity
     */
    protected function getSearchField()
    {
        return 'name';
    }

    /**
     * Get name field for the entity
     */
    protected function getNameField()
    {
        return 'name';
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity)
    {
        return $entity->name;
    }

    /**
     * After store hook
     */
    protected function afterStore($modelAI)
    {
        // Add any post-creation logic here
        Log::info("ModelAI created: {$modelAI->name}");
    }

    /**
     * After update hook
     */
    protected function afterUpdate($modelAI)
    {
        // Add any post-update logic here
        Log::info("ModelAI updated: {$modelAI->name}");
    }

    /**
     * After destroy hook
     */
    protected function afterDestroy($modelAI)
    {
        // Add any post-deletion logic here
        Log::info("ModelAI deleted: {$modelAI->name}");
    }

    /**
     * After restore hook
     */
    protected function afterRestore($modelAI)
    {
        // Add any post-restoration logic here
        Log::info("ModelAI restored: {$modelAI->name}");
    }
} 