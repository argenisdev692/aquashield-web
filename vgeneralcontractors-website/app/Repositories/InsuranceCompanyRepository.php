<?php

namespace App\Repositories;

use App\Models\InsuranceCompany;
use App\Repositories\Interfaces\InsuranceCompanyRepositoryInterface;

class InsuranceCompanyRepository extends BaseRepository implements InsuranceCompanyRepositoryInterface
{
    protected array $searchableFields = [
        'insurance_company_name',
        'address', 
        'phone',
        'email',
        'website'
    ];
    
    protected array $filterableFields = [
        'user_id',
        'search',
        'show_deleted'
    ];
    
    protected array $sortableFields = [
        'insurance_company_name',
        'address',
        'email',
        'website',
        'created_at',
        'updated_at'
    ];
    
    protected string $defaultSortField = 'insurance_company_name';
    protected string $defaultSortDirection = 'asc';

    public function __construct(InsuranceCompany $model)
    {
        parent::__construct($model);
    }

    /**
     * Find insurance companies by user
     */
    public function findByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * Find insurance company by name
     */
    public function findByName(string $name): ?InsuranceCompany
    {
        return $this->model->where('insurance_company_name', $name)->first();
    }

    /**
     * Find insurance company by email
     */
    public function findByEmail(string $email): ?InsuranceCompany
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Check if insurance company name exists (excluding specific UUID)
     */
    public function nameExists(string $name, ?string $excludeUuid = null): bool
    {
        $query = $this->model->where('insurance_company_name', $name);
        
        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }
        
        return $query->withTrashed()->exists();
    }

    /**
     * Check if email exists (excluding specific UUID)
     */
    public function emailExists(string $email, ?string $excludeUuid = null): bool
    {
        $query = $this->model->where('email', $email);
        
        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }
        
        return $query->withTrashed()->exists();
    }

    /**
     * Check if phone exists (excluding specific UUID)
     */
    public function phoneExists(string $phone, ?string $excludeUuid = null): bool
    {
        $query = $this->model->where('phone', $phone);
        
        if ($excludeUuid) {
            $query->where('uuid', '!=', $excludeUuid);
        }
        
        return $query->withTrashed()->exists();
    }

    /**
     * Get active insurance companies
     */
    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->whereNull('deleted_at')
                          ->orderBy('insurance_company_name')
                          ->get();
    }

    /**
     * Get insurance companies with user relationship
     */
    public function getWithUser(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with('user')
                          ->orderBy('insurance_company_name')
                          ->get();
    }

    /**
     * Search insurance companies with advanced filters
     */
    public function searchAdvanced(array $filters): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Include user relationship
        $query->with('user');

        // Apply search
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('insurance_company_name', 'like', $searchTerm)
                  ->orWhere('address', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('website', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm);
            });
        }

        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Show deleted
        if (!empty($filters['show_deleted']) && $filters['show_deleted'] === 'true') {
            $query->withTrashed();
        }

        // Apply sorting
        $sortField = $filters['sort_field'] ?? $this->defaultSortField;
        $sortDirection = $filters['sort_direction'] ?? $this->defaultSortDirection;
        
        if (in_array($sortField, $this->sortableFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $perPage = (int) ($filters['per_page'] ?? 15);
        return $query->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?InsuranceCompany
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function findByUuidWithTrashed(string $uuid): ?InsuranceCompany
    {
        return $this->model->withTrashed()->where('uuid', $uuid)->first();
    }
}