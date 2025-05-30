<?php

namespace App\Exports;

use App\Models\Leave;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LeaveReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Leave::with([
            'user:id,firstname,lastname,department_id,staff_id,email',
            'user.department:id,name',
            'leaveType:id,name',
            'approver:id,firstname,lastname',
            'rejector:id,firstname,lastname'
        ])
        ->select('leaves.*')
        ->when($this->filters['year'] ?? null, function ($query) {
            $query->whereYear('leaves.start_date', $this->filters['year']);
        })
        ->when($this->filters['department_id'] ?? null, function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('users.department_id', $this->filters['department_id']);
            });
        })
        ->when($this->filters['leave_type_id'] ?? null, function ($query) {
            $query->where('leaves.leave_type_id', $this->filters['leave_type_id']);
        })
        ->when($this->filters['status'] ?? null, function ($query) {
            $query->where('leaves.status', $this->filters['status']);
        })
        ->when($this->filters['date_range'] ?? null, function ($query) {
            $dates = explode(' to ', $this->filters['date_range']);
            if (count($dates) === 2) {
                $query->whereBetween('leaves.start_date', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
            }
        });
    }

    public function headings(): array
    {
        return [
            'Staff ID',
            'Employee Name',
            'Email',
            'Department',
            'Leave Type',
            'Start Date',
            'End Date',
            'Working Days',
            'Status',
            'Applied On',
            'Approved/Rejected By',
            'Approval Date',
            'Reason'
        ];
    }

    public function map($leave): array
    {
        return [
            $leave->user->staff_id,
            $leave->user->firstname . " " . $leave->user->lastname,
            $leave->user->email,
            $leave->user->department->name,
            $leave->leaveType->name,
            Carbon::parse($leave->start_date)->format('Y-m-d'),
            Carbon::parse($leave->end_date)->format('Y-m-d'),
            $leave->working_days,
            ucfirst($leave->status),
            Carbon::parse($leave->created_at)->format('Y-m-d'),
            $leave->status === 'approved' 
                ? ($leave->approver ? $leave->approver->firstname . " " . $leave->approver->lastname : '')
                : ($leave->status === 'rejected' 
                    ? ($leave->rejector ? $leave->rejector->firstname . " " . $leave->rejector->lastname : '')
                    : ''),
            $leave->status !== 'pending' 
                ? Carbon::parse($leave->status === 'approved' ? $leave->approved_at : $leave->rejected_at)->format('Y-m-d')
                : '',
            $leave->reason
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
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
            'B' => 25, // Employee Name
            'C' => 30, // Email
            'D' => 20, // Department
            'E' => 20, // Leave Type
            'F' => 15, // Start Date
            'G' => 15, // End Date
            'H' => 15, // Working Days
            'I' => 15, // Status
            'J' => 15, // Applied On
            'K' => 25, // Approved/Rejected By
            'L' => 15, // Approval Date
            'M' => 40, // Reason
        ];
    }
} 