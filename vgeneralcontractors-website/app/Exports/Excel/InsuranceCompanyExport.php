<?php

namespace App\Exports\Excel;

use App\Models\InsuranceCompany;
use Illuminate\Support\Collection;

class InsuranceCompanyExport extends BaseExportExcel
{
    private $searchFilters;
    private $dateFilters;

    public function __construct($data = null, $searchFilters = [], $dateFilters = [])
    {
        $this->searchFilters = $searchFilters;
        $this->dateFilters = $dateFilters;
        
        // If no data provided, get all insurance companies
        if ($data === null) {
            $data = $this->getInsuranceCompaniesData();
        }

        $companyInfo = [
            'name' => config('app.name', 'V General Contractors'),
            'report_title' => 'Insurance Companies Report',
            'generated_by' => auth()->user()->name ?? 'System',
            'filters_applied' => $this->getFiltersDescription()
        ];

        parent::__construct($data, 'Insurance Companies Export', $companyInfo);
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Nro',
            'Company Name',
            'Email',
            'Phone',
            'Address',
            'Website',
            'Assigned User',
            'Created Date',
            'Status'
        ];
    }

    /**
     * Get formatted data for export
     */
    private function getInsuranceCompaniesData(): Collection
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

        $companies = $query->get();

        return $companies->map(function ($company, $index) {
            return [
                $index + 1,
                $company->insurance_company_name,
                $company->email ?: 'N/A',
                $company->phone ?: 'N/A',
                $company->address ?: 'N/A',
                $company->website ?: 'N/A',
                $company->user ? $company->user->name . ' ' . $company->user->last_name : 'N/A',
                $company->created_at->format('Y-m-d H:i:s'),
                $company->deleted_at ? 'Inactive' : 'Active'
            ];
        });
    }

    /**
     * Get description of applied filters
     */
    private function getFiltersDescription(): string
    {
        $filters = [];
        
        if (!empty($this->searchFilters['search'])) {
            $filters[] = 'Search: ' . $this->searchFilters['search'];
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

        return empty($filters) ? 'No filters applied' : implode(', ', $filters);
    }

    /**
     * Override to add custom styling for insurance companies
     */
    public function styles($sheet)
    {
        parent::styles($sheet);

        // Add custom styling for insurance company specific columns
        // Company name column (B) - make it bold
        $lastRow = $this->data->count() + 1;
        if ($lastRow > 1) {
            $sheet->getStyle('B2:B' . $lastRow)->getFont()->setBold(true);
            
            // Email column (C) - blue color for emails
            $sheet->getStyle('C2:C' . $lastRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0066CC'));
            
            // Status column (I) - conditional formatting
            for ($row = 2; $row <= $lastRow; $row++) {
                $statusCell = 'I' . $row;
                $status = $sheet->getCell($statusCell)->getValue();
                
                if ($status === 'Active') {
                    $sheet->getStyle($statusCell)->applyFromArray([
                        'font' => ['color' => ['rgb' => '059669']], // Green
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'ECFDF5'] // Light green background
                        ]
                    ]);
                } elseif ($status === 'Inactive') {
                    $sheet->getStyle($statusCell)->applyFromArray([
                        'font' => ['color' => ['rgb' => 'DC2626']], // Red
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FEF2F2'] // Light red background
                        ]
                    ]);
                }
            }
        }

        return $sheet;
    }
}
