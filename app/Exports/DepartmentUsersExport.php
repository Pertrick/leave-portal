<?php

namespace App\Exports;

use App\Models\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentUsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $users;
    protected $department;

    public function __construct(Collection $users, Department $department)
    {
        $this->users = $users;
        $this->department = $department;
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            'Staff ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'User Level',
            'Level Number',
            'Designation',
            'Join Date',
            'Status',
            'Department',
            'Location'
        ];
    }

    public function map($user): array
    {
        return [
            $user->staff_id,
            $user->firstname,
            $user->lastname,
            $user->email,
            $user->phone ?? 'N/A',
            $user->userLevel?->name ?? 'N/A',
            $user->userLevel?->level ?? 'N/A',
            $user->designation ?? 'N/A',
            $user->join_date ? date('Y-m-d', strtotime($user->join_date)) : 'N/A',
            $user->is_active ? 'Active' : 'Inactive',
            $this->department->name,
            $this->department->location?->name ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ]
            ]
        ];
    }

    public function title(): string
    {
        return $this->department->name . ' Users';
    }

    public static function download(Collection $users, Department $department)
    {
        $export = new self($users, $department);
        $filename = $department->name . '_Users_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }
} 