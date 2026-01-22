<?php

namespace App\Services\Claims;

use App\Services\BaseService;
use App\Repositories\Interfaces\ClaimRepositoryInterface;
use App\Services\TransactionService;
use App\Jobs\ProcessNewClaimNotification;
use App\Jobs\ProcessClaimStatusUpdateNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ClaimService extends BaseService
{
    protected ClaimRepositoryInterface $claimRepository;

    public function __construct(
        ClaimRepositoryInterface $claimRepository, 
        TransactionService $transactionService
    ) {
        parent::__construct($claimRepository, $transactionService);
        $this->claimRepository = $claimRepository;
    }

    /**
     * Prepare data before creating claim
     */
    protected function prepareCreateData(array $data): array
    {
        $prepared = array_merge($data, [
            'uuid' => (string) Str::uuid(),
            'claim_number' => $this->generateClaimNumber(),
            'status' => 'pending',
            'created_by' => auth()->id()
        ]);

        return $this->sanitizeData($prepared);
    }

    /**
     * Execute after successful creation (OUTSIDE transaction)
     */
    protected function afterCreate(Model $claim): void
    {
        // Dispatch job OUTSIDE transaction as per memory guidance
        ProcessNewClaimNotification::dispatch($claim);
        
        Log::info('Claim created successfully', [
            'claim_id' => $claim->id,
            'claim_number' => $claim->claim_number,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Execute after successful update (OUTSIDE transaction)
     */
    protected function afterUpdate(Model $claim): void
    {
        // Send notifications if status changed
        if ($claim->wasChanged('status')) {
            ProcessClaimStatusUpdateNotification::dispatch($claim, $claim->getOriginal('status'));
            
            Log::info('Claim status updated', [
                'claim_id' => $claim->id,
                'old_status' => $claim->getOriginal('status'),
                'new_status' => $claim->status,
                'user_id' => auth()->id()
            ]);
        }
    }

    /**
     * Generate unique claim number
     */
    private function generateClaimNumber(): string
    {
        $prefix = 'VGC';
        $year = date('Y');
        
        do {
            $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $claimNumber = "{$prefix}-{$year}-{$random}";
            $exists = $this->claimRepository->findByClaimNumber($claimNumber);
        } while ($exists);
        
        return $claimNumber;
    }

    /**
     * Get claims by status
     */
    public function getClaimsByStatus(string $status)
    {
        return $this->claimRepository->findByStatus($status);
    }

    /**
     * Search claims
     */
    public function searchClaims(string $term, array $fields = [])
    {
        return $this->claimRepository->search($term, $fields);
    }

    /**
     * Get pending claims
     */
    public function getPendingClaims()
    {
        return $this->claimRepository->getPendingClaims();
    }

    /**
     * Get urgent claims
     */
    public function getUrgentClaims()
    {
        return $this->claimRepository->getUrgentClaims();
    }

    /**
     * Get claims for inspection
     */
    public function getClaimsForInspection()
    {
        return $this->claimRepository->getClaimsForInspection();
    }

    /**
     * Get claims by date range
     */
    public function getClaimsByDateRange(string $startDate, string $endDate)
    {
        return $this->claimRepository->findByDateRange($startDate, $endDate);
    }

    /**
     * Get claims by insurance company
     */
    public function getClaimsByInsuranceCompany(string $company)
    {
        return $this->claimRepository->findByInsuranceCompany($company);
    }

    /**
     * Get claims needing follow up
     */
    public function getClaimsNeedingFollowUp()
    {
        return $this->claimRepository->getClaimsNeedingFollowUp();
    }

    /**
     * Schedule inspection for claim
     */
    public function scheduleInspection(Model $claim, string $inspectionDate, ?string $notes = null): Model
    {
        return $this->update($claim, [
            'scheduled_inspection_date' => $inspectionDate,
            'inspection_notes' => $notes,
            'status' => 'inspection_scheduled'
        ]);
    }

    /**
     * Complete inspection for claim
     */
    public function completeInspection(Model $claim, string $notes): Model
    {
        return $this->update($claim, [
            'inspection_notes' => $notes,
            'status' => 'inspection_completed'
        ]);
    }

    /**
     * Approve claim
     */
    public function approveClaim(Model $claim): Model
    {
        return $this->update($claim, [
            'status' => 'approved'
        ]);
    }

    /**
     * Decline claim
     */
    public function declineClaim(Model $claim, string $reason): Model
    {
        return $this->update($claim, [
            'status' => 'declined',
            'inspection_notes' => $reason
        ]);
    }
} 