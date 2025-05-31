<?php

namespace App\Exports;

use App\Models\LeaveBalance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeaveBalancesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = LeaveBalance::with(['user.department', 'leaveType'])
            ->when(!auth()->user()->hasRole('admin'), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->when($this->filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('staff_id', 'like', "%{$search}%");
                });
            })
            ->when($this->filters['department'] ?? null, function ($query, $department) {
                $query->whereHas('user', function ($q) use ($department) {
                    $q->where('department_id', $department);
                });
            })
            ->when($this->filters['leaveType'] ?? null, function ($query, $leaveType) {
                $query->where('leave_type_id', $leaveType);
            })
            ->when($this->filters['year'] ?? null, function ($query, $year) {
                $query->where('year', $year);
            })
            ->select([
                'leave_balances.*',
                'users.staff_id',
                'users.firstname',
                'users.lastname',
                'departments.name as department_name',
                'leave_types.name as leave_type_name'
            ])
            ->join('users', 'leave_balances.user_id', '=', 'users.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->join('leave_types', 'leave_balances.leave_type_id', '=', 'leave_types.id');

        return $query;
    }

    public function headings(): array
    {
        return [
            'Staff ID',
            'Name',
            'Department',
            'Leave Type',
            'Year',
            'Entitled Days',
            'Days Taken',
            'Remaining Days',
            'Status'
        ];
    }

    public function map($balance): array
    {
        // Ensure we have numeric values, defaulting to 0 if null
        $totalEntitledDays = (int)($balance->total_entitled_days ?: 0);
        $daysTaken = (int)($balance->days_taken ?: 0);
        $remainingDays = max(0, $totalEntitledDays - $daysTaken);

        return [
            $balance->staff_id,
            $balance->firstname . ' ' . $balance->lastname,
            $balance->department_name ?? 'N/A',
            $balance->leave_type_name,
            $balance->year,
            $totalEntitledDays,
            $daysTaken,
            $remainingDays,
            $remainingDays > 0 ? 'Available' : 'Exhausted'
        ];
    }
} 