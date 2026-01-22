<?php

namespace App\Repositories\Interfaces;

use App\Models\InsuranceCompany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InsuranceCompanyRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find insurance companies by user
     */
    public function findByUser(int $userId): Collection;

    /**
     * Find insurance company by name
     */
    public function findByName(string $name): ?InsuranceCompany;

    /**
     * Find insurance company by email
     */
    public function findByEmail(string $email): ?InsuranceCompany;

    /**
     * Check if insurance company name exists (excluding specific UUID)
     */
    public function nameExists(string $name, ?string $excludeUuid = null): bool;

    /**
     * Check if email exists (excluding specific UUID)
     */
    public function emailExists(string $email, ?string $excludeUuid = null): bool;

    /**
     * Check if phone exists (excluding specific UUID)
     */
    public function phoneExists(string $phone, ?string $excludeUuid = null): bool;

    /**
     * Get active insurance companies
     */
    public function getActive(): Collection;

    /**
     * Get insurance companies with user relationship
     */
    public function getWithUser(): Collection;

    /**
     * Search insurance companies with advanced filters
     */
    public function searchAdvanced(array $filters): LengthAwarePaginator;

    /**
     * Find insurance company by UUID
     */
    public function findByUuid(string $uuid): ?InsuranceCompany;

    /**
     * Find insurance company by UUID with trashed
     */
    public function findByUuidWithTrashed(string $uuid): ?InsuranceCompany;
}