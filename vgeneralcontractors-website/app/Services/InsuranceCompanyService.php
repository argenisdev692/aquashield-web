<?php

namespace App\Services;

use App\Models\InsuranceCompany;
use App\Repositories\Interfaces\InsuranceCompanyRepositoryInterface;
use App\Services\TransactionService;
use App\Services\LoggerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InsuranceCompanyService extends BaseService
{
    protected InsuranceCompanyRepositoryInterface $insuranceCompanyRepository;

    public function __construct(
        InsuranceCompanyRepositoryInterface $repository,
        TransactionService $transactionService,
        LoggerService $logger
    ) {
        parent::__construct($repository, $transactionService, $logger);
        $this->insuranceCompanyRepository = $repository;
    }

    /**
     * Check if insurance company name exists
     */
    public function nameExists(string $name, ?string $excludeUuid = null): bool
    {
        return $this->insuranceCompanyRepository->nameExists($name, $excludeUuid);
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?string $excludeUuid = null): bool
    {
        return $this->insuranceCompanyRepository->emailExists($email, $excludeUuid);
    }

    /**
     * Check if phone exists
     */
    public function phoneExists(string $phone, ?string $excludeUuid = null): bool
    {
        return $this->insuranceCompanyRepository->phoneExists($phone, $excludeUuid);
    }

    /**
     * Get insurance companies by user
     */
    public function getByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->insuranceCompanyRepository->findByUser($userId);
    }

    /**
     * Get active insurance companies
     */
    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->insuranceCompanyRepository->getActive();
    }

    /**
     * Get insurance companies with user relationship
     */
    public function getWithUser(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->insuranceCompanyRepository->getWithUser();
    }

    /**
     * Search insurance companies with advanced filters
     */
    public function searchAdvanced(array $filters): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->insuranceCompanyRepository->searchAdvanced($filters);
    }

    /**
     * Prepare data before creating insurance company
     */
    protected function prepareCreateData(array $data): array
    {
        $prepared = array_merge($data, [
            'uuid' => (string) Str::uuid(),
            'created_by' => auth()->id(),
        ]);
        
        // Ensure user_id is set if not provided
        if (!isset($prepared['user_id']) || $prepared['user_id'] === null) {
            $prepared['user_id'] = auth()->id();
        }

        // Format phone number
        if (isset($prepared['phone'])) {
            $prepared['phone'] = $this->formatPhoneForStorage($prepared['phone']);
        }

        // Format email
        if (isset($prepared['email'])) {
            $prepared['email'] = strtolower(trim($prepared['email']));
        }

        // Format website
        if (isset($prepared['website']) && !empty($prepared['website'])) {
            $prepared['website'] = $this->formatWebsite($prepared['website']);
        }

        // Trim company name
        if (isset($prepared['insurance_company_name'])) {
            $prepared['insurance_company_name'] = trim($prepared['insurance_company_name']);
        }

        return $this->sanitizeData($prepared);
    }

    /**
     * Prepare data before updating insurance company
     */
    protected function prepareUpdateData(array $data): array
    {
        $prepared = array_merge($data, [
            'updated_by' => auth()->id(),
        ]);

        // Format phone number
        if (isset($prepared['phone'])) {
            $prepared['phone'] = $this->formatPhoneForStorage($prepared['phone']);
        }

        // Format email
        if (isset($prepared['email'])) {
            $prepared['email'] = strtolower(trim($prepared['email']));
        }

        // Format website
        if (isset($prepared['website']) && !empty($prepared['website'])) {
            $prepared['website'] = $this->formatWebsite($prepared['website']);
        }

        // Trim company name
        if (isset($prepared['insurance_company_name'])) {
            $prepared['insurance_company_name'] = trim($prepared['insurance_company_name']);
        }

        return $this->sanitizeData($prepared);
    }

    /**
     * Format phone number for storage
     */
    protected function formatPhoneForStorage(string $phone): string
    {
        // If phone is already in (xxx) xxx-xxxx format, keep it as is
        if (preg_match('/^\(\d{3}\)\s\d{3}-\d{4}$/', $phone)) {
            return $phone;
        }
        
        // Remove all non-digits
        $cleaned = preg_replace('/\D/', '', $phone);
        
        // If it's 10 digits, format to (xxx) xxx-xxxx
        if (strlen($cleaned) === 10) {
            return '(' . substr($cleaned, 0, 3) . ') ' . substr($cleaned, 3, 3) . '-' . substr($cleaned, 6, 4);
        }
        
        // If it's 11 digits and starts with 1, remove the 1 and format
        if (strlen($cleaned) === 11 && str_starts_with($cleaned, '1')) {
            $cleaned = substr($cleaned, 1);
            return '(' . substr($cleaned, 0, 3) . ') ' . substr($cleaned, 3, 3) . '-' . substr($cleaned, 6, 4);
        }
        
        return $cleaned;
    }

    /**
     * Format website URL
     */
    protected function formatWebsite(string $website): string
    {
        $website = trim($website);
        // Si no tiene protocolo y empieza con www., agrega https://
        if (!preg_match('/^https?:\/\//', $website)) {
            if (preg_match('/^www\./', $website)) {
                $website = 'https://' . $website;
            } else {
                $website = 'https://' . $website;
            }
        }
        return $website;
    }

    /**
     * Execute after successful creation (OUTSIDE transaction)
     */
    protected function afterCreate(Model $entity): void
    {
        $this->logger->logCrudOperation('CREATE', $entity, [
            'insurance_company_name' => $entity->insurance_company_name,
            'user_id' => $entity->user_id
        ]);
    }

    /**
     * Execute after successful update (OUTSIDE transaction)
     */
    protected function afterUpdate(Model $entity): void
    {
        $this->logger->logCrudOperation('UPDATE', $entity, [
            'insurance_company_name' => $entity->insurance_company_name,
            'changes' => $entity->getChanges()
        ]);
    }

    /**
     * Execute after successful deletion (OUTSIDE transaction)
     */
    protected function afterDelete(Model $entity): void
    {
        $this->logger->logCrudOperation('DELETE', $entity, [
            'insurance_company_name' => $entity->insurance_company_name
        ]);
    }

    /**
     * Execute after successful restoration (OUTSIDE transaction)
     */
    protected function afterRestore(Model $entity): void
    {
        $this->logger->logCrudOperation('RESTORE', $entity, [
            'insurance_company_name' => $entity->insurance_company_name
        ]);
    }

    public function findByUuid(string $uuid): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->insuranceCompanyRepository->findByUuid($uuid);
    }

    public function delete(Model $entity): bool
    {
        return parent::delete($entity);
    }

    public function deleteByUuid(string $uuid): bool
    {
        $entity = $this->insuranceCompanyRepository->findByUuid($uuid);
        if (!$entity) return false;
        return $this->delete($entity);
    }

    public function restore(Model $entity): bool
    {
        return parent::restore($entity);
    }

    public function restoreByUuid(string $uuid): bool
    {
        $entity = $this->insuranceCompanyRepository->findByUuidWithTrashed($uuid);
        if (!$entity) return false;
        return $this->restore($entity);
    }
}