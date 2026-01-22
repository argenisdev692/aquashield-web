<?php

namespace App\Repositories\Claims;

use App\Models\Claim;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\ClaimRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ClaimRepository extends BaseRepository implements ClaimRepositoryInterface
{
    protected array $searchableFields = [
        'claim_number',
        'property_address',
        'damage_type',
        'insurance_company',
        'policy_number',
        'contact_name',
        'contact_phone',
        'contact_email'
    ];

    protected array $filterableFields = [
        'status',
        'priority',
        'damage_type',
        'insurance_company',
        'created_at',
        'estimated_cost',
        'search',
        'show_deleted'
    ];

    protected array $sortableFields = [
        'created_at',
        'updated_at',
        'claim_number',
        'property_address',
        'estimated_cost',
        'status',
        'priority',
        'scheduled_inspection_date'
    ];

    public function __construct(Claim $model)
    {
        parent::__construct($model);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->rememberCrudCache('claims_by_status_' . $status, function() use ($status) {
            return $this->model->byStatus($status)->get();
        });
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        $cacheKey = 'claims_date_range_' . md5($startDate . $endDate);
        
        return $this->rememberCrudCache($cacheKey, function() use ($startDate, $endDate) {
            return $this->model->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByInsuranceCompany(string $company): Collection
    {
        return $this->rememberCrudCache('claims_insurance_' . md5($company), function() use ($company) {
            return $this->model->where('insurance_company', $company)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByPriority(string $priority): Collection
    {
        return $this->rememberCrudCache('claims_priority_' . $priority, function() use ($priority) {
            return $this->model->byPriority($priority)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function getPendingClaims(): Collection
    {
        return $this->rememberCrudCache('claims_pending', function() {
            return $this->model->pending()
                ->orderBy('created_at', 'asc')
                ->get();
        });
    }

    public function getUrgentClaims(): Collection
    {
        return $this->rememberCrudCache('claims_urgent', function() {
            return $this->model->byPriority('urgent')
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('created_at', 'asc')
                ->get();
        });
    }

    public function getClaimsForInspection(): Collection
    {
        return $this->rememberCrudCache('claims_for_inspection', function() {
            return $this->model->where('status', 'inspection_scheduled')
                ->whereNotNull('scheduled_inspection_date')
                ->orderBy('scheduled_inspection_date', 'asc')
                ->get();
        });
    }

    public function findByClaimNumber(string $claimNumber): ?Claim
    {
        return $this->model->where('claim_number', $claimNumber)->first();
    }

    public function getClaimsNeedingFollowUp(): Collection
    {
        return $this->rememberCrudCache('claims_follow_up', function() {
            return $this->model->where('status', 'pending')
                ->where('created_at', '<=', now()->subDays(3))
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();
        });
    }
}