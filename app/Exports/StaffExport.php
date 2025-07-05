<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class StaffExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data->map(function ($staff) {
            return [
                $staff->staff_id,
                $staff->firstname . ' ' . $staff->lastname,
                $staff->email,
                $staff->department?->name ?? 'N/A',
                $staff->designation ?? 'N/A',
                $staff->user_level?->name ?? 'N/A',
                $staff->roles?->first()?->name ?? 'No Role',
                $staff->is_active ? 'Active' : 'Inactive',
                $staff->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Staff ID',
            'Full Name',
            'Email',
            'Department',
            'Designation',
            'User Level',
            'Role',
            'Status',
            'Date Created',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Staff ID
            'B' => 25, // Full Name
            'C' => 30, // Email
            'D' => 20, // Department
            'E' => 20, // Designation
            'F' => 15, // User Level
            'G' => 15, // Role
            'H' => 10, // Status
            'I' => 20, // Date Created
        ];
    }

    public function title(): string
    {
        return 'Staff List';
    }

    public function map($staff): array
    {
        return [
            $staff->staff_id,
            $staff->firstname . ' ' . $staff->lastname,
            $staff->email,
            $staff->department?->name ?? 'N/A',
            $staff->designation ?? 'N/A',
            $staff->user_level?->name ?? 'N/A',
            $staff->roles?->first()?->name ?? 'No Role',
            $staff->is_active ? 'Active' : 'Inactive',
            $staff->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
        ];
    }
} 