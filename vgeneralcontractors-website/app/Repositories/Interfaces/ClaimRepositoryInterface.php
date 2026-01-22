<?php

namespace App\Repositories\Interfaces;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Collection;

interface ClaimRepositoryInterface extends BaseRepositoryInterface
{
    public function findByStatus(string $status): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByInsuranceCompany(string $company): Collection;
    public function findByPriority(string $priority): Collection;
    public function getPendingClaims(): Collection;
    public function getUrgentClaims(): Collection;
    public function getClaimsForInspection(): Collection;
    public function findByClaimNumber(string $claimNumber): ?Claim;
    public function getClaimsNeedingFollowUp(): Collection;
}