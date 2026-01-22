<?php

namespace App\Exports\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

abstract class BaseExportExcel implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $title;
    protected $companyInfo;

    public function __construct($data, $title = 'Export', $companyInfo = [])
    {
        $this->data = $data;
        $this->title = $title;
        $this->companyInfo = $companyInfo;
    }

    /**
     * Return collection of data to export
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * Define column headings - must be implemented by child classes
     */
    abstract public function headings(): array;

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Header row styles
        $sheet->getStyle('A1:' . $this->getLastColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'] // Indigo background
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
        ]);

        // Data rows styles
        $lastRow = $this->data->count() + 1;
        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $this->getLastColumn() . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            // Alternate row colors
            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':' . $this->getLastColumn() . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8F9FA']
                        ]
                    ]);
                }
            }
        }

        return $sheet;
    }

    /**
     * Register events for additional customization
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $worksheet = $event->sheet->getDelegate();
                
                // Set row heights
                $worksheet->getDefaultRowDimension()->setRowHeight(20);
                $worksheet->getRowDimension('1')->setRowHeight(25);
                
                // Add title and company info if provided
                if (!empty($this->companyInfo)) {
                    $this->addHeaderInfo($worksheet);
                }
                
                // Add footer with export date
                $this->addFooter($worksheet);
            }
        ];
    }

    /**
     * Add company header information
     */
    protected function addHeaderInfo(Worksheet $worksheet)
    {
        // This would insert company info above the data
        // Implementation depends on specific requirements
    }

    /**
     * Add footer with export information
     */
    protected function addFooter(Worksheet $worksheet)
    {
        $lastRow = $this->data->count() + 3; // Add some spacing
        $worksheet->setCellValue('A' . $lastRow, 'Exported on: ' . now()->format('Y-m-d H:i:s'));
        $worksheet->getStyle('A' . $lastRow)->getFont()->setItalic(true)->setSize(10);
    }

    /**
     * Get the last column letter for styling
     */
    protected function getLastColumn(): string
    {
        $headings = $this->headings();
        $columnCount = count($headings);
        return chr(65 + $columnCount - 1); // A, B, C, etc.
    }
}
