<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveApplication;
use App\Models\LeaveBalance;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Get pending approvals count
        $pendingApprovals = LeaveApplication::where('status', 'pending')->count();

        // Get employees on leave today
        $onLeaveToday = LeaveApplication::where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();

        // Get total leave balance
        $totalLeaveBalance = LeaveBalance::sum('total_entitled_days');

        // Get exhausted leave count
        $exhaustedLeave = LeaveBalance::whereRaw('total_entitled_days <= days_taken')
            ->count();

        // Get leave distribution by type
        $leaveDistribution = LeaveApplication::where('status', 'approved')
            ->select('leave_type_id', DB::raw('COUNT(*) as count'))
            ->with('leaveType:id,name')
            ->groupBy('leave_type_id')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->leaveType->name,
                    'value' => $item->count
                ];
            });

        // Get department-wise leave usage
        $departmentLeave = Department::withCount(['users' => function ($query) {
            $query->whereHas('leaveApplications', function ($q) {
                $q->where('status', 'approved');
            });
        }])->get()
        ->map(function ($dept) {
            return [
                'label' => $dept->name,
                'value' => $dept->users_count
            ];
        });

        // Get recent leave applications
        $recentApplications = LeaveApplication::with(['user', 'leaveType'])
            ->latest()
            ->take(5)
            ->get();

        // Get upcoming leave
        $upcomingLeave = LeaveApplication::with(['user', 'leaveType'])
            ->where('status', 'approved')
            ->where('start_date', '>', $today)
            ->orderBy('start_date')
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'pendingApprovals' => $pendingApprovals,
                'onLeaveToday' => $onLeaveToday,
                'totalLeaveBalance' => $totalLeaveBalance,
                'exhaustedLeave' => $exhaustedLeave
            ],
            'leaveDistribution' => [
                'labels' => $leaveDistribution->pluck('label'),
                'data' => $leaveDistribution->pluck('value')
            ],
            'departmentLeave' => [
                'labels' => $departmentLeave->pluck('label'),
                'data' => $departmentLeave->pluck('value')
            ],
            'recentApplications' => $recentApplications,
            'upcomingLeave' => $upcomingLeave
        ]);
    }
} 