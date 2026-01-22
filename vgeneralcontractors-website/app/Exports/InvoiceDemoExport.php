<?php

namespace App\Exports;

use App\Models\InvoiceDemo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class InvoiceDemoExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $invoices;

    public function __construct($filters = [], $invoices = null)
    {
        $this->filters = $filters;
        $this->invoices = $invoices;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        // If invoices are provided directly, use them
        if ($this->invoices) {
            return $this->invoices;
        }

        // Otherwise, build query based on filters
        $query = InvoiceDemo::query();

        // Apply search filter
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('bill_to_name', 'like', "%{$search}%")
                  ->orWhere('bill_to_email', 'like', "%{$search}%")
                  ->orWhere('bill_to_company', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Apply date range filter
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        // Include deleted records if specified
        if (!empty($this->filters['include_deleted'])) {
            $query->withTrashed();
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('invoices_demo_traduccion_invoice_number'),
            __('invoices_demo_traduccion_bill_to_name'),
            __('invoices_demo_traduccion_bill_to_company'),
            __('invoices_demo_traduccion_bill_to_email'),
            __('invoices_demo_traduccion_bill_to_phone'),
            __('invoices_demo_traduccion_bill_to_address'),
            __('invoices_demo_traduccion_status'),
            __('invoices_demo_traduccion_subtotal'),
            __('invoices_demo_traduccion_tax_amount'),
            __('invoices_demo_traduccion_total_amount'),
            __('notes'),
            __('invoices_demo_traduccion_created_date'),
            __('invoices_demo_traduccion_updated_date'),
            __('invoices_demo_traduccion_items_count')
        ];
    }

    /**
     * @param mixed $invoice
     * @return array
     */
    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->bill_to_name,
            $invoice->bill_to_company,
            $invoice->bill_to_email,
            $invoice->bill_to_phone,
            $this->formatAddress($invoice),
            ucfirst($invoice->status),
            '$' . number_format($invoice->subtotal, 2),
            '$' . number_format($invoice->tax_amount, 2),
            '$' . number_format($invoice->total_amount, 2),
            $invoice->notes,
            $invoice->created_at->format('Y-m-d H:i:s'),
            $invoice->updated_at->format('Y-m-d H:i:s'),
            is_array($invoice->items) ? count($invoice->items) : 0
        ];
    }

    /**
     * Format address for export
     */
    private function formatAddress($invoice): string
    {
        $addressParts = array_filter([
            $invoice->bill_to_address,
            $invoice->bill_to_city,
            $invoice->bill_to_state,
            $invoice->bill_to_zip,
            $invoice->bill_to_country
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            // Style all data rows
            'A2:N1000' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]
        ];
    }
}