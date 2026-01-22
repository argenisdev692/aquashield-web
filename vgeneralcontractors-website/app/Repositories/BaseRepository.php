<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Traits\CacheTraitCrud;
use App\Enums\CacheTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Throwable;

abstract class BaseRepository implements BaseRepositoryInterface
{
    use CacheTraitCrud;

    protected Model $model;
    protected array $searchableFields = [];
    protected array $filterableFields = [];
    protected array $sortableFields = ['created_at', 'updated_at'];
    protected string $defaultSortField = 'created_at';
    protected string $defaultSortDirection = 'desc';

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->initializeCacheProperties();
    }

    public function create(array $data): Model
    {
        Log::info("Creating {$this->model->getTable()} record", ['data' => $data]);
        
        $entity = $this->model->create($data);
        
        // Clear cache after creation
        $this->markSignificantDataChange();
        $this->clearCrudCache($this->model->getTable());
        
        return $entity;
    }

    public function update(Model $model, array $data): Model
    {
        Log::info("Updating {$this->model->getTable()} record", [
            'id' => $model->id, 
            'data' => $data
        ]);
        
        $model->update($data);
        
        // Clear cache after update
        $this->markSignificantDataChange();
        $this->clearCrudCache($this->model->getTable());
        
        return $model->fresh();
    }

    public function delete(Model $model): bool
    {
        Log::info("Soft deleting {$this->model->getTable()} record", ['id' => $model->id]);
        
        $result = $model->delete();
        
        // Clear cache after deletion
        $this->markSignificantDataChange();
        $this->clearCrudCache($this->model->getTable());
        
        return $result;
    }

    public function restore(Model $model): bool
    {
        Log::info("Restoring {$this->model->getTable()} record", ['id' => $model->id]);
        
        $result = $model->restore();
        
        // Clear cache after restoration
        $this->markSignificantDataChange();
        $this->clearCrudCache($this->model->getTable());
        
        return $result;
    }

    public function forceDelete(Model $model): bool
    {
        Log::info("Force deleting {$this->model->getTable()} record", ['id' => $model->id]);
        
        $result = $model->forceDelete();
        
        // Clear cache after force deletion
        $this->markSignificantDataChange();
        $this->clearCrudCache($this->model->getTable());
        
        return $result;
    }

    public function findById(string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findByUuid(string $uuid): ?Model
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery();
        return $this->applyFilters($query, $filters)->get();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = $this->getCacheKey('paginate', $filters, $perPage);
        
        return $this->rememberCrudCache($this->model->getTable(), function() use ($filters, $perPage) {
            $query = $this->model->newQuery();
            
            // Apply filters
            $query = $this->applyFilters($query, $filters);
            
            // Apply sorting
            $sortField = $filters['sort_field'] ?? $this->defaultSortField;
            $sortDirection = $filters['sort_direction'] ?? $this->defaultSortDirection;
            
            if (in_array($sortField, $this->sortableFields)) {
                $query->orderBy($sortField, $sortDirection);
            }
            
            return $query->paginate($perPage);
        });
    }

    public function search(string $term, array $fields = []): Collection
    {
        $searchFields = empty($fields) ? $this->searchableFields : $fields;
        
        if (empty($searchFields)) {
            Log::warning("No searchable fields defined for {$this->model->getTable()}");
            return new Collection();
        }
        
        $query = $this->model->newQuery();
        
        $query->where(function ($q) use ($term, $searchFields) {
            foreach ($searchFields as $index => $field) {
                if ($index === 0) {
                    $q->where($field, 'LIKE', "%{$term}%");
                } else {
                    $q->orWhere($field, 'LIKE', "%{$term}%");
                }
            }
        });
        
        return $query->get();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (in_array($field, $this->filterableFields) && !empty($value)) {
                match(true) {
                    is_array($value) => $query->whereIn($field, $value),
                    $field === 'search' && !empty($this->searchableFields) => $query->where(function ($q) use ($value) {
                        foreach ($this->searchableFields as $index => $searchField) {
                            if ($index === 0) {
                                $q->where($searchField, 'LIKE', "%{$value}%");
                            } else {
                                $q->orWhere($searchField, 'LIKE', "%{$value}%");
                            }
                        }
                    }),
                    $field === 'show_deleted' && $value === 'true' => $query->withTrashed(),
                    default => $query->where($field, $value)
                };
            }
        }

        return $query;
    }

    private function getCacheKey(string $method, array $filters = [], int $perPage = 15): string
    {
        return $this->model->getTable() . "_{$method}_" . md5(serialize($filters) . $perPage);
    }
}