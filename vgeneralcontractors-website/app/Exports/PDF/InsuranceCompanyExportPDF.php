<?php

namespace App\Exports\PDF;

use App\Models\InsuranceCompany;
use App\Models\CompanyData;
use App\Helpers\CompanyDataHelper;
use Illuminate\Support\Collection;

class InsuranceCompanyExportPDF extends BaseExportPDF
{
    private array $searchFilters;
    private array $dateFilters;
    private Collection $originalData;

    public function __construct(
        Collection $data = null,
        array $searchFilters = [],
        array $dateFilters = [],
        array $options = []
    ) {
        $this->searchFilters = $searchFilters;
        $this->dateFilters = $dateFilters;

        // If no data provided, get filtered insurance companies
        if ($data === null) {
            $data = $this->getFilteredInsuranceCompanies();
        }

        // Store original data for summary calculations
        $this->originalData = $data;

        // Set default options for insurance companies
        $defaultOptions = [
            'orientation' => 'landscape', // Better for table with many columns
            'paper_size' => 'letter',
            'dpi' => 150,
            'show_borders' => true,
            'alternate_row_colors' => true,
            'show_summary' => true,
            'repeat_headers' => false, // Don't repeat table headers on each page
        ];

        $options = array_merge($defaultOptions, $options);

        $companyInfo = $this->getCompanyInformation();

        parent::__construct(
            $this->formatDataForPDF($data),
            'Insurance Companies Report',
            $companyInfo,
            $options
        );
    }

    /**
     * Get template path for insurance companies PDF
     */
    protected function getTemplatePath(): string
    {
        return 'exports.insurance-companies-pdf';
    }

    /**
     * Get table headers for insurance companies
     */
    protected function getHeaders(): array
    {
        return [
            '#' => ['width' => '5%', 'align' => 'center'],
            'Company Name' => ['width' => '22%', 'align' => 'left'],
            'Email' => ['width' => '18%', 'align' => 'left'],
            'Phone' => ['width' => '12%', 'align' => 'center'],
            'Address' => ['width' => '25%', 'align' => 'left'],
            'Status' => ['width' => '10%', 'align' => 'center'],
            'Created By' => ['width' => '8%', 'align' => 'center'],
        ];
    }

    /**
     * Get filtered insurance companies data
     */
    private function getFilteredInsuranceCompanies(): Collection
    {
        $query = InsuranceCompany::with('user');

        // Apply search filters
        if (!empty($this->searchFilters['search'])) {
            $searchTerm = '%' . $this->searchFilters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('insurance_company_name', 'like', $searchTerm)
                  ->orWhere('address', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm)
                  ->orWhere('website', 'like', $searchTerm);
            });
        }

        // Apply date filters
        if (!empty($this->dateFilters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->dateFilters['start_date']);
        }
        if (!empty($this->dateFilters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->dateFilters['end_date']);
        }

        // Apply soft delete filter
        if ($this->searchFilters['show_deleted'] ?? false) {
            $query->withTrashed();
        }

        // Apply sorting
        $sortField = $this->searchFilters['sort_field'] ?? 'insurance_company_name';
        $sortDirection = $this->searchFilters['sort_direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        return $query->get();
    }

    /**
     * Format data specifically for PDF display
     */
    protected function formatDataForPDF(Collection $data): Collection
    {
        return $data->map(function ($company, $index) {
            return [
                'number' => $index + 1,
                'company_name' => $company->insurance_company_name,
                'email' => $company->email ?: 'N/A',
                'phone' => $this->formatPhoneForDisplay($company->phone),
                'address' => $this->truncateText($company->address, 50),
                'website' => $this->formatWebsiteForDisplay($company->website),
                'status' => $company->deleted_at ? 'Inactive' : 'Active',
                'deleted_at' => $company->deleted_at,
                'assigned_user' => $company->user 
                    ? $company->user->name . ' ' . $company->user->last_name 
                    : 'System',
                'created_date' => $company->created_at ? $company->created_at->format('M j, Y') : 'N/A',
            ];
        });
    }

    /**
     * Get additional data for the template
     */
    protected function getAdditionalData(): array
    {
        return [
            'filters_applied' => $this->getFiltersDescription(),
            'summary' => $this->calculateInsuranceCompaniesSummary(),
            'date_range' => $this->getDateRangeDescription(),
        ];
    }

    /**
     * Calculate summary statistics for insurance companies
     */
    private function calculateInsuranceCompaniesSummary(): array
    {
        $summary = parent::calculateSummary($this->data);
        
        // Use original data to get accurate counts
        $activeCount = $this->originalData->where('deleted_at', null)->count();
        $inactiveCount = $this->originalData->whereNotNull('deleted_at')->count();
        $totalCount = $this->originalData->count();
        
        return array_merge($summary, [
            'active_companies' => $activeCount,
            'inactive_companies' => $inactiveCount,
            'total_companies' => $totalCount,
            'active_percentage' => $totalCount > 0 
                ? round(($activeCount / $totalCount) * 100, 1) 
                : 0,
        ]);
    }

    /**
     * Get company information for header
     */
    private function getCompanyInformation(): array
    {
        return CompanyDataHelper::getCompanyInfo();
    }

    /**
     * Get description of applied filters
     */
    private function getFiltersDescription(): string
    {
        $filters = [];
        
        if (!empty($this->searchFilters['search'])) {
            $filters[] = 'Search: "' . $this->searchFilters['search'] . '"';
        }
        
        if (!empty($this->dateFilters['start_date'])) {
            $filters[] = 'From: ' . $this->dateFilters['start_date'];
        }
        
        if (!empty($this->dateFilters['end_date'])) {
            $filters[] = 'To: ' . $this->dateFilters['end_date'];
        }
        
        if ($this->searchFilters['show_deleted'] ?? false) {
            $filters[] = 'Including inactive records';
        }

        return empty($filters) ? 'No filters applied' : implode(' | ', $filters);
    }

    /**
     * Get date range description
     */
    private function getDateRangeDescription(): string
    {
        $start = $this->dateFilters['start_date'] ?? null;
        $end = $this->dateFilters['end_date'] ?? null;

        if ($start && $end) {
            return "From {$start} to {$end}";
        } elseif ($start) {
            return "From {$start}";
        } elseif ($end) {
            return "Up to {$end}";
        }

        return "All time";
    }

    /**
     * Format phone number for display
     */
    private function formatPhoneForDisplay(?string $phone): string
    {
        if (!$phone) {
            return 'N/A';
        }

        // Basic phone formatting - you can enhance this
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6, 4)
            );
        }

        return $phone ?: 'N/A';
    }

    /**
     * Format website for display
     */
    private function formatWebsiteForDisplay(?string $website): string
    {
        if (!$website) {
            return 'N/A';
        }

        // Remove protocol for cleaner display
        return preg_replace('/^https?:\/\//', '', $website);
    }

    /**
     * Truncate text for PDF display
     */
    private function truncateText(?string $text, int $limit = 50): string
    {
        if (!$text) {
            return 'N/A';
        }

        return strlen($text) > $limit 
            ? substr($text, 0, $limit) . '...' 
            : $text;
    }

    /**
     * Calculate estimated pages for insurance companies in landscape
     */
    protected function calculateEstimatedPages(): int
    {
        $recordCount = $this->data->count();
        
        // For landscape insurance company reports, more rows fit per page
        $rowsPerPage = 35; // Landscape allows more rows
        
        // Account for header and summary sections
        $effectiveRows = max(1, $recordCount);
        
        return max(1, ceil($effectiveRows / $rowsPerPage));
    }
}
