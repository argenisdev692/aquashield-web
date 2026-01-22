<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AppointmentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email', 
            'Phone',
            'Address',
            'City',
            'State',
            'Zipcode',
            'Inspection Date',
            'Status',
            'Insurance',
            'SMS Consent',
            'Lead Source',
            'Created At'
        ];
    }

    /**
     * @param mixed $appointment
     * @return array
     */
    public function map($appointment): array
    {
        return [
            $appointment->first_name . ' ' . $appointment->last_name,
            $appointment->email,
            $appointment->phone,
            $appointment->address,
            $appointment->city,
            $appointment->state,
            $appointment->zipcode,
            $appointment->inspection_date ? date('m/d/Y', strtotime($appointment->inspection_date)) : 'N/A',
            $appointment->status_lead,
            $appointment->insurance_property ? 'Yes' : 'No',
            $appointment->sms_consent ? 'Yes' : 'No',
            $appointment->lead_source,
            $appointment->created_at ? date('m/d/Y', strtotime($appointment->created_at)) : 'N/A',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '4472C4',
                ],
            ],
        ]);
        
        // Add borders to all cells
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
        ]);
        
        // Alternate row colors
        $lastRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'F2F2F2',
                        ],
                    ],
                ]);
            }
        }
    }
} 