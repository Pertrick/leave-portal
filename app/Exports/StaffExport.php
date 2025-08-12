<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data; 
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