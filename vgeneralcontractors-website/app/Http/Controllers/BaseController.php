<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\TransactionService;
use App\Traits\ChecksPermissions;
use App\Enums\RequestMethod;
use App\Enums\CacheTime;
use Carbon\Carbon;
use Throwable;

abstract class BaseController extends Controller
{
    use ChecksPermissions;
    
    protected string $modelClass;
    protected string $entityName;
    protected string $viewPrefix;
    protected string $routePrefix;
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of entities.
     * Handles both view request and AJAX data request.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        Log::debug($this->entityName.'Controller@index method entered.');
        
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view {$this->entityName}")) {
            return match(true) {
                $request->ajax() || $request->wantsJson() => response()->json(['error' => "Permission denied"], 403),
                default => redirect()->back()->with('error', "Permission denied")
            };
        }
        
        try {
            return match(true) {
                $request->ajax() || $request->wantsJson() => $this->handleAjaxRequest($request),
                default => $this->handleViewRequest($request)
            };
        } catch (Throwable $e) {
            Log::error('Error fetching entities in '.$this->entityName.'Controller@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return match(true) {
                $request->ajax() || $request->wantsJson() => response()->json([
                    'success' => false,
                    'message' => 'Error fetching entities: ' . $e->getMessage(),
                    'error_details' => $e->getTraceAsString()
                ], 500),
                default => view($this->viewPrefix . '.index')->with('error', 'Error loading entities. Please try again.')
            };
        }
    }

    protected function handleAjaxRequest(Request $request): JsonResponse
    {
        Log::debug('AJAX request detected in '.$this->entityName.'Controller@index.', $request->all());
        
        // Validate date range if provided
        $dateValidation = $this->validateDateRange($request);
        if (!$dateValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $dateValidation['message']
            ], 422);
        }

        $query = $this->modelClass::query();
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where($this->getSearchField(), 'like', $searchTerm);
            Log::debug('Applying search filter.', ['term' => $request->search]);
        }

        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        Log::debug('Applying sorting.', ['field' => $sortField, 'direction' => $sortDirection]);
        
        // Show deleted items if requested
        if ($request->has('show_deleted') && $request->show_deleted === 'true') {
            $query->withTrashed();
            Log::debug('Including soft-deleted entities.');
        }
        
        // Paginate results
        $perPage = (int) $request->input('per_page', 10);
        $entities = $query->paginate($perPage);
        
        Log::debug('Entities fetched successfully for AJAX request.', [
            'count' => $entities->count(),
            'total' => $entities->total(),
            'currentPage' => $entities->currentPage(),
            'perPage' => $entities->perPage(),
        ]);
        
        return response()->json($entities);
    }

    protected function handleViewRequest(Request $request): View
    {
        Log::debug('Non-AJAX request detected, returning view.');
        return view($this->viewPrefix . '.index');
    }

    /**
     * Show the form for creating a new entity.
     */
    public function create(): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create {$this->entityName}")) {
            return response()->json(['error' => "Permission denied"], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ready to create new ' . $this->entityName
        ]);
    }

    /**
     * Store a newly created entity.
     */
    public function store(Request $request): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create {$this->entityName}")) {
            return response()->json(['error' => "Permission denied"], 403);
        }
        
        try {
            $this->validateRequest($request);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        try {
            $entity = $this->transactionService->run(
                // Database operations
                function () use ($request) {
                    $data = $this->prepareStoreData($request);
                    $entity = $this->modelClass::create($data);
                    
                    Log::info($this->entityName.' created successfully', ['uuid' => $entity->uuid]);
                    return $entity;
                },
                // On commit
                function ($createdEntity) {
                    // Any additional actions after successful commit
                    $this->afterStore($createdEntity);
                },
                // On error
                function (Throwable $e) {
                    Log::error('Error creating '.$this->entityName, ['error' => $e->getMessage()]);
                }
            );
            
            return response()->json([
                'success' => true,
                'message' => $this->entityName.' created successfully!',
                'entity' => $entity
            ]);
        } catch (Throwable $e) {
            Log::error('Error creating '.$this->entityName, ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating '.$this->entityName.': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified entity.
     */
    public function show(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view {$this->entityName}")) {
            return response()->json(['error' => "Permission denied"], 403);
        }
        
        try {
            $entity = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
            
            return response()->json([
                'success' => true,
                $this->getEntityVarName() => $entity
            ]);
        } catch (Throwable $e) {
            Log::error('Error finding '.$this->entityName.' for show', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => $this->entityName.' not found.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified entity.
     */
    public function edit(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("UPDATE_{$this->entityName}", "You don't have permission to edit {$this->entityName}")) {
            return response()->json(['error' => "Permission denied"], 403);
        }
        
        try {
            $entity = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
            
            return response()->json([
                'success' => true,
                $this->getEntityVarName() => $entity
            ]);
        } catch (Throwable $e) {
            Log::error('Error finding '.$this->entityName.' for edit', ['uuid' => $uuid, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => $this->entityName.' not found.'
            ], 404);
        }
    }

    /**
     * Update the specified entity.
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("UPDATE_{$this->entityName}", "You don't have permission to update {$this->entityName}")) {
            return response()->json(['error' => "Permission denied"], 403);
        }
        
        try {
            $entity = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
            
            try {
                $this->validateRequest($request, $entity->id);
            } catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            }

            $entity = $this->transactionService->run(
                // Database operations
                function () use ($request, $uuid) {
                    $entity = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                    
                    $data = $this->prepareUpdateData($request);
                    $entity->update($data);
                    
                    Log::info($this->entityName.' updated successfully', ['uuid' => $uuid]);
                    return $entity;
                },
                // On commit
                function ($updatedEntity) {
                    // Any additional actions after successful commit
                    $this->afterUpdate($updatedEntity);
                },
                // On error
                function (Throwable $e) use ($uuid) {
                    Log::error('Error updating '.$this->entityName, ['uuid' => $uuid, 'error' => $e->getMessage()]);
                }
            );
            
            return response()->json([
                'success' => true,
                'message' => $this->entityName.' updated successfully!',
                $this->getEntityVarName() => $entity
            ]);
        } catch (Throwable $e) {
            Log::error('Error updating '.$this->entityName, ['uuid' => $uuid, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating '.$this->entityName.': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete the specified entity.
     */
    public function destroy(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("DELETE_{$this->entityName}", "You don't have permission to delete {$this->entityName}")) {
            return response()->json(['error' => "Permission denied"], 403);
        }
        
        try {
            $entityName = $this->transactionService->run(
                // Database operations
                function () use ($uuid) {
                    $entity = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                    $entityName = $this->getEntityDisplayName($entity);
                    
                    $entity->delete();
                    
                    Log::info($this->entityName.' deleted successfully', ['uuid' => $uuid]);
                    return $entityName;
                },
                // On commit
                function (string $deletedEntityName) {
                    // Any additional actions after successful commit
                    $this->afterDestroy($deletedEntityName);
                },
                // On error
                function (Throwable $e) use ($uuid) {
                    Log::error('Error deleting '.$this->entityName, ['uuid' => $uuid, 'error' => $e->getMessage()]);
                }
            );
            
            return response()->json([
                'success' => true,
                'message' => $this->entityName.' "' . $entityName . '" moved to trash successfully!'
            ]);
        } catch (Throwable $e) {
            Log::error('Error deleting '.$this->entityName, ['uuid' => $uuid, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting '.$this->entityName.': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft-deleted entity.
     */
    public function restore(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("RESTORE_{$this->entityName}", "You don't have permission to restore {$this->entityName}")) {
            return response()->json(['error' => "Permission denied"], 403);
        }
        
        try {
            $entityName = $this->transactionService->run(
                // Database operations
                function () use ($uuid) {
                    $entity = $this->modelClass::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
                    $entityName = $this->getEntityDisplayName($entity);
                    
                    $entity->restore();
                    
                    Log::info($this->entityName.' restored successfully', ['uuid' => $uuid]);
                    return $entityName;
                },
                // On commit
                function (string $restoredEntityName) {
                    // Any additional actions after successful commit
                    $this->afterRestore($restoredEntityName);
                },
                // On error
                function (Throwable $e) use ($uuid) {
                    Log::error('Error restoring '.$this->entityName, ['uuid' => $uuid, 'error' => $e->getMessage()]);
                }
            );
            
            return response()->json([
                'success' => true,
                'message' => $this->entityName.' "' . $entityName . '" restored successfully!'
            ]);
        } catch (Throwable $e) {
            Log::error('Error restoring '.$this->entityName, ['uuid' => $uuid, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error restoring '.$this->entityName.': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if an entity name already exists.
     * Used for real-time validation.
     */
    public function checkNameExists(Request $request): JsonResponse
    {
        $name = $request->input('name');
        $excludeUuid = $request->input('exclude_uuid');
        
        $query = $this->modelClass::where($this->getNameField(), $name);
        
        // If we're editing, exclude the current entity
        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }
        
        $exists = $query->withTrashed()->exists();
        
        return response()->json([
            'exists' => $exists
        ]);
    }

    /**
     * Validate the request data.
     */
    protected function validateRequest(Request $request, ?int $id = null): void
    {
        $validator = Validator::make(
            $request->all(), 
            $this->getValidationRules($id), 
            $this->getValidationMessages()
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    // Abstract methods that must be implemented by child classes
    abstract protected function getValidationRules(?int $id = null): array;
    abstract protected function getValidationMessages(): array;
    
    // Methods with default implementations that can be overridden by child classes
    protected function getSearchField(): string
    {
        return 'name';
    }
    
    protected function getNameField(): string
    {
        return 'name';
    }
    
    protected function getEntityVarName(): string
    {
        return strtolower($this->entityName);
    }
    
    protected function getEntityDisplayName($entity): string
    {
        return $entity->{$this->getNameField()};
    }
    
    protected function prepareStoreData(Request $request): array
    {
        return $request->all();
    }
    
    protected function prepareUpdateData(Request $request): array
    {
        return $request->all();
    }
    
    protected function afterStore($entity): void
    {
        // Default implementation does nothing
    }
    
    protected function afterUpdate($entity): void
    {
        // Default implementation does nothing
    }
    
    protected function afterDestroy(string $entityName): void
    {
        // Default implementation does nothing
    }
    
    protected function afterRestore(string $entityName): void
    {
        // Default implementation does nothing
    }

    /**
     * Apply date filters to query
     */
    protected function applyDateFilters($query, Request $request, string $defaultDateField = 'created_at')
    {
        if ($request->has('date_start') && !empty($request->date_start)) {
            $dateField = $request->input('date_field', $defaultDateField);
            $query->whereDate($dateField, '>=', $request->date_start);
            Log::debug('Applied start date filter', [
                'field' => $dateField,
                'date' => $request->date_start
            ]);
        }
        
        if ($request->has('date_end') && !empty($request->date_end)) {
            $dateField = $request->input('date_field', $defaultDateField);
            $query->whereDate($dateField, '<=', $request->date_end);
            Log::debug('Applied end date filter', [
                'field' => $dateField,
                'date' => $request->date_end
            ]);
        }

        return $query;
    }

    /**
     * Validate date range
     */
    protected function validateDateRange(Request $request): array
    {
        $startDate = $request->input('date_start');
        $endDate = $request->input('date_end');

        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);

            if ($end->lt($start)) {
                return [
                    'valid' => false,
                    'message' => 'End date cannot be earlier than start date'
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Get available date fields for filtering
     */
    protected function getAvailableDateFields(): array
    {
        return [
            'created_at' => 'Created Date',
            'updated_at' => 'Updated Date'
        ];
    }

    /**
     * Create DTO instance from Eloquent model
     * 
     * @param mixed $model Eloquent model instance
     * @return mixed DTO instance
     */
    protected function fromEloquent($model)
    {
        if (!$model) {
            return null;
        }

        // Get the DTO class name based on the model class
        $modelClass = get_class($model);
        $modelName = class_basename($modelClass);
        $dtoClass = "App\\Http\\DTOs\\{$modelName}DTO";

        // Check if DTO class exists
        if (!class_exists($dtoClass)) {
            throw new \InvalidArgumentException("DTO class {$dtoClass} not found for model {$modelName}");
        }

        // Convert model to array and create DTO
        $data = $model->toArray();
        
        // Add any special attributes that might not be in toArray()
        if (method_exists($model, 'getUuidAttribute')) {
            $data['uuid'] = $model->uuid;
        }
        
        return new $dtoClass($data);
    }
}