<?php

namespace App\Services;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Services\TransactionService;
use App\Services\LoggerService;
use App\Enums\CacheTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseService
{
        protected BaseRepositoryInterface $repository;
    protected TransactionService $transactionService;
    protected LoggerService $logger;

    public function __construct(
        BaseRepositoryInterface $repository,
        TransactionService $transactionService,
        LoggerService $logger
    ) {
        $this->repository = $repository;
        $this->transactionService = $transactionService;
        $this->logger = $logger;
    }

    /**
     * Create a new entity with transaction support
     */
    public function create(array $data): Model
    {
        return $this->transactionService->run(
            function () use ($data) {
                $preparedData = $this->prepareCreateData($data);
                $entity = $this->repository->create($preparedData);
                
                $logMessage = match(true) {
                    isset($entity->uuid) => 'Entity created successfully with UUID: '.$entity->uuid,
                    default => 'Entity created successfully with ID: '.$entity->id
                };
                
                $this->logger->logCrudOperation('CREATE', $entity, [
                    'data_summary' => $this->getDataSummary($preparedData),
                    'message' => $logMessage
                ]);

                return $entity;
            },
            function ($entity) {
                // Execute OUTSIDE transaction as per memory guidance
                $this->afterCreate($entity);
            }
        );
    }

    /**
     * Update an entity with transaction support
     */
    public function update(Model $entity, array $data): Model
    {
        return $this->transactionService->run(
            function () use ($entity, $data) {
                $preparedData = $this->prepareUpdateData($data);
                $updatedEntity = $this->repository->update($entity, $preparedData);
                
                $changes = $updatedEntity->getChanges();
                $logMessage = match(true) {
                    empty($changes) => 'No changes detected during update',
                    count($changes) === 1 => 'Updated 1 field: '.array_keys($changes)[0],
                    default => 'Updated '.count($changes).' fields: '.implode(', ', array_keys($changes))
                };
                
                $this->logger->logCrudOperation('UPDATE', $updatedEntity, [
                    'changes' => $changes,
                    'message' => $logMessage
                ]);

                return $updatedEntity;
            },
            function ($updatedEntity) {
                // Execute OUTSIDE transaction as per memory guidance
                $this->afterUpdate($updatedEntity);
            }
        );
    }

    /**
     * Delete an entity with transaction support
     */
    public function delete(Model $entity): bool
    {
        return $this->transactionService->run(
            function () use ($entity) {
                $entityInfo = [
                    'type' => get_class($entity),
                    'id' => $entity->id,
                    'uuid' => $entity->uuid ?? null
                ];
                
                $result = $this->repository->delete($entity);
                
                $this->logger->logCrudOperation('DELETE', $entity);

                return $result;
            },
            function ($result) use ($entity) {
                if ($result) {
                    // Execute OUTSIDE transaction as per memory guidance
                    $this->afterDelete($entity);
                }
            }
        );
    }

    /**
     * Restore a soft-deleted entity
     */
    public function restore(Model $entity): bool
    {
        return $this->transactionService->run(
            function () use ($entity) {
                $result = $this->repository->restore($entity);
                
                $this->logger->logCrudOperation('RESTORE', $entity);

                return $result;
            },
            function ($result) use ($entity) {
                if ($result) {
                    $this->afterRestore($entity);
                }
            }
        );
    }

    /**
     * Find entity by UUID
     */
    public function findByUuid(string $uuid): ?Model
    {
        return $this->repository->findByUuid($uuid);
    }

    /**
     * Search entities
     */
    public function search(string $term, array $fields = [])
    {
        return $this->repository->search($term, $fields);
    }

    /**
     * Get paginated results with filters
     */
    public function paginate(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    // Hook methods - override in child classes
    
    /**
     * Prepare data before creating entity
     */
    protected function prepareCreateData(array $data): array
    {
        // Add common fields
        $prepared = array_merge($data, [
            'uuid' => (string) Str::uuid(),
            'created_by' => auth()->id(),
        ]);

        return $this->sanitizeData($prepared);
    }

    /**
     * Prepare data before updating entity
     */
    protected function prepareUpdateData(array $data): array
    {
        // Add common fields
        $prepared = array_merge($data, [
            'updated_by' => auth()->id(),
        ]);

        return $this->sanitizeData($prepared);
    }

    /**
     * Sanitize data by removing null values and trimming strings
     */
    protected function sanitizeData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim($value);
            } elseif ($value !== null) {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Handle checkbox values as per memory guidance
     */
    protected function handleCheckboxValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
        }
        
        return (bool) $value;
    }

    // Hook methods that can be overridden in child classes

    /**
     * Execute after successful creation (OUTSIDE transaction)
     */
    protected function afterCreate(Model $entity): void
    {
        // Override in child classes
        // This is where you dispatch jobs, send notifications, etc.
    }

    /**
     * Execute after successful update (OUTSIDE transaction)
     */
    protected function afterUpdate(Model $entity): void
    {
        // Override in child classes
        // This is where you dispatch jobs, send notifications, etc.
    }

    /**
     * Execute after successful deletion (OUTSIDE transaction)
     */
    protected function afterDelete(Model $entity): void
    {
        // Override in child classes
        // This is where you dispatch jobs, send notifications, etc.
    }

    /**
     * Execute after successful restoration (OUTSIDE transaction)
     */
    protected function afterRestore(Model $entity): void
    {
        // Override in child classes
        // This is where you dispatch jobs, send notifications, etc.
    }

    /**
     * Get data summary for logging (avoid logging sensitive data)
     */
    protected function getDataSummary(array $data): array
    {
        $summary = [];
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $sensitiveFields)) {
                $summary[$key] = '[REDACTED]';
            } elseif (is_string($value) && strlen($value) > 100) {
                $summary[$key] = substr($value, 0, 100) . '...';
            } else {
                $summary[$key] = $value;
            }
        }
        
        return $summary;
    }
}