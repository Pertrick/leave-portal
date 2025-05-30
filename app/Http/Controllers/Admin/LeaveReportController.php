<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Department;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use App\Models\LeaveReport;
use App\Exports\LeaveReportExport;
use Maatwebsite\Excel\Facades\Excel;

class LeaveReportController extends Controller
{
    protected $cacheTime = 3600; // 1 hour cache

    public function index(Request $request)
    {
        // Get filter parameters
        $year = $request->input('year', now()->year);
        $departmentId = $request->input('department_id');
        $leaveTypeId = $request->input('leave_type_id');
        $status = $request->input('status');
        $dateRange = $request->input('date_range');

        // Generate cache key based on filters
        $cacheKey = "leave_report_{$year}_{$departmentId}_{$leaveTypeId}_{$status}_{$dateRange}";

        // Base query with eager loading
        $query = Leave::with([
            'user:id,firstname,lastname,department_id,staff_id',
            'user.department:id,name',
            'leaveType:id,name',
            'approver:id,firstname,lastname',
            'rejector:id,firstname,lastname'
        ])
        ->select('leaves.*')
        ->when($year, function ($query) use ($year) {
            $query->whereYear('leaves.start_date', $year);
        })
        ->when($departmentId, function ($query) use ($departmentId) {
            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('users.department_id', $departmentId);
            });
        })
        ->when($leaveTypeId, function ($query) use ($leaveTypeId) {
            $query->where('leaves.leave_type_id', $leaveTypeId);
        })
        ->when($status, function ($query) use ($status) {
            $query->where('leaves.status', $status);
        })
        ->when($dateRange, function ($query) use ($dateRange) {
            $dates = explode(' to ', $dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('leaves.start_date', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
            }
        });

        // Get leave statistics with caching
        $statistics = Cache::remember($cacheKey . '_statistics', $this->cacheTime, function () use ($query) {
            return [
                'total_applications' => (clone $query)->count(),
                'total_days' => (clone $query)->sum('leaves.working_days'),
                'by_status' => $this->getStatusStatistics($query),
                'by_type' => $this->getTypeStatistics($query),
                'by_department' => $this->getDepartmentStatistics($query),
                'department_comparison' => $this->getDepartmentComparison($query),
                'leave_balance_analysis' => $this->getLeaveBalanceAnalysis($query),
                'monthly_trend' => $this->getMonthlyTrend($query),
                'average_duration' => (clone $query)->avg('leaves.working_days'),
                'longest_leave' => (clone $query)->max('leaves.working_days'),
                'most_common_duration' => $this->getMostCommonDuration($query)
            ];
        });

        // Get paginated leave records with cursor pagination for better performance
        $leaves = $query->latest()
            ->cursorPaginate(10)
            ->withQueryString();

        // Cache departments and leave types
        $departments = Cache::remember('departments_list', $this->cacheTime, function () {
            return Department::select('id', 'name')->orderBy('name')->get();
        });

        $leaveTypes = Cache::remember('active_leave_types', $this->cacheTime, function () {
            return LeaveType::select('id', 'name')->where('is_active', true)->get();
        });

        return Inertia::render('Admin/LeaveReport', [
            'statistics' => $statistics,
            'leaves' => $leaves,
            'departments' => $departments,
            'leaveTypes' => $leaveTypes,
            'filters' => [
                'year' => $year,
                'department_id' => $departmentId,
                'leave_type_id' => $leaveTypeId,
                'status' => $status,
                'date_range' => $dateRange
            ]
        ]);
    }

    protected function getStatusStatistics($query)
    {
        return (clone $query)
            ->select('leaves.status', DB::raw('count(*) as count'))
            ->groupBy('leaves.status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });
    }

    protected function getTypeStatistics($query)
    {
        return (clone $query)
            ->select('leaves.leave_type_id', DB::raw('count(*) as count'), DB::raw('sum(leaves.working_days) as total_days'))
            ->groupBy('leaves.leave_type_id')
            ->with('leaveType:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->leaveType->name,
                    'count' => $item->count,
                    'total_days' => $item->total_days
                ];
            });
    }

    protected function getDepartmentStatistics($query)
    {
        return (clone $query)
            ->select(
                'departments.id',
                'departments.name',
                DB::raw('count(*) as count'),
                DB::raw('sum(leaves.working_days) as total_days'),
                DB::raw('count(DISTINCT leaves.user_id) as unique_users')
            )
            ->join('users', 'leaves.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->groupBy('departments.id', 'departments.name')
            ->get()
            ->map(function ($item) {
                return [
                    'department' => $item->name,
                    'count' => $item->count,
                    'total_days' => $item->total_days,
                    'unique_users' => $item->unique_users,
                    'average_per_user' => $item->unique_users > 0 ? round($item->total_days / $item->unique_users, 1) : 0
                ];
            });
    }

    protected function getDepartmentComparison($query)
    {
        return (clone $query)
            ->select(
                'departments.name',
                DB::raw('count(*) as total_applications'),
                DB::raw('sum(CASE WHEN leaves.status = "approved" THEN 1 ELSE 0 END) as approved_applications'),
                DB::raw('sum(CASE WHEN leaves.status = "rejected" THEN 1 ELSE 0 END) as rejected_applications'),
                DB::raw('sum(CASE WHEN leaves.status = "pending" THEN 1 ELSE 0 END) as pending_applications'),
                DB::raw('sum(leaves.working_days) as total_days'),
                DB::raw('count(DISTINCT leaves.user_id) as total_users')
            )
            ->join('users', 'leaves.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->groupBy('departments.name')
            ->get()
            ->map(function ($item) {
                return [
                    'department' => $item->name,
                    'total_applications' => $item->total_applications,
                    'approved_applications' => $item->approved_applications,
                    'rejected_applications' => $item->rejected_applications,
                    'pending_applications' => $item->pending_applications,
                    'total_days' => $item->total_days,
                    'total_users' => $item->total_users,
                    'approval_rate' => $item->total_applications > 0 ? 
                        round(($item->approved_applications / $item->total_applications) * 100, 1) : 0,
                    'average_days_per_user' => $item->total_users > 0 ? 
                        round($item->total_days / $item->total_users, 1) : 0
                ];
            });
    }

    protected function getLeaveBalanceAnalysis($query)
    {
        return (clone $query)
            ->select(
                'users.id',
                'users.firstname',
                'users.lastname',
                'departments.name as department',
                'leave_types.name as leave_type',
                DB::raw('sum(leaves.working_days) as days_taken'),
                DB::raw('leave_balances.total_entitled_days'),
                DB::raw('leave_balances.total_entitled_days - sum(leaves.working_days) as remaining_days')
            )
            ->join('users', 'leaves.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->join('leave_balances', function($join) {
                $join->on('leave_balances.user_id', '=', 'users.id')
                     ->on('leave_balances.leave_type_id', '=', 'leave_types.id');
            })
            ->where('leaves.status', 'approved')
            ->groupBy('users.id', 'users.firstname', 'users.lastname', 'departments.name', 'leave_types.name', 'leave_balances.total_entitled_days')
            ->get()
            ->groupBy('department')
            ->map(function ($department) {
                return [
                    'total_allocated' => $department->sum('total_entitled_days'),
                    'total_taken' => $department->sum('days_taken'),
                    'total_remaining' => $department->sum('remaining_days'),
                    'utilization_rate' => $department->sum('total_entitled_days') > 0 ? 
                        round(($department->sum('days_taken') / $department->sum('total_entitled_days')) * 100, 1) : 0,
                    'details' => $department->map(function ($item) {
                        return [
                            'employee' => $item->firstname . " " . $item->lastname,
                            'leave_type' => $item->leave_type,
                            'allocated' => $item->total_entitled_days,
                            'taken' => $item->days_taken,
                            'remaining' => $item->remaining_days,
                            'utilization' => $item->total_entitled_days > 0 ? 
                                round(($item->days_taken / $item->total_entitled_days) * 100, 1) : 0
                        ];
                    })
                ];
            });
    }

    protected function getMonthlyTrend($query)
    {
        return (clone $query)
            ->select(
                DB::raw('MONTH(leaves.start_date) as month'),
                DB::raw('count(*) as count'),
                DB::raw('sum(leaves.working_days) as total_days')
            )
            ->groupBy(DB::raw('MONTH(leaves.start_date)'))
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create()->month($item->month)->format('F'),
                    'count' => $item->count,
                    'total_days' => $item->total_days
                ];
            });
    }

    protected function getMostCommonDuration($query)
    {
        return (clone $query)
            ->select('leaves.working_days', DB::raw('count(*) as count'))
            ->groupBy('leaves.working_days')
            ->orderByDesc('count')
            ->first()?->working_days;
    }

    public function export(Request $request)
    {
        Gate::authorize('export-leave-reports');

        $filters = [
            'year' => $request->input('year'),
            'department_id' => $request->input('department_id'),
            'leave_type_id' => $request->input('leave_type_id'),
            'status' => $request->input('status'),
            'date_range' => $request->input('date_range')
        ];

        return Excel::download(
            new LeaveReportExport($filters),
            'leave-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
} 