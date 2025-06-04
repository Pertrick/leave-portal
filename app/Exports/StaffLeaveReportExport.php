<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class StaffLeaveReportExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithMapping
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return array_keys($this->data[0] ?? []);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:Z1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Staff ID
            'B' => 30, // Name
            'C' => 20, // Department
            'D' => 15, // Leave Type 1 Total
            'E' => 15, // Leave Type 1 Used
            'F' => 15, // Leave Type 1 Remaining
            'G' => 15, // Leave Type 2 Total
            'H' => 15, // Leave Type 2 Used
            'I' => 15, // Leave Type 2 Remaining
            'J' => 15, // Total Leaves Taken
            'K' => 15, // Total Days Used
            'L' => 15, // Pending Leaves
            'M' => 15, // Pending Days
        ];
    }

    public function title(): string
    {
        return 'Staff Leave Report';
    }

    public function map($row): array
    {
        return array_values($row);
    }
} 