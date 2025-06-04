<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\LeaveBalance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $currentYear = $today->year;
        
        // Get staff on leave today with detailed information
        $staffOnLeave = Leave::with([
            'user.department', 
            'leaveType', 
            'approvals.approver',
            'approvals.approvalLevel'
        ])
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('status', 'approved')
            ->where('is_cancelled', false)
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'user' => [
                        'firstname' => $leave->user->firstname,
                        'lastname' => $leave->user->lastname,
                        'staff_id' => $leave->user->staff_id,
                        'avatar' => $leave->user->avatar,
                        'email' => $leave->user->email,
                        'phone' => $leave->user->phone,
                        'department' => $leave->user->department ? [
                            'name' => $leave->user->department->name
                        ] : null
                    ],
                    'leave_type' => [
                        'name' => $leave->leaveType->name
                    ],
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'working_days' => $leave->working_days,
                    'status' => $leave->status,
                    'replacement_staff_name' => $leave->replacement_staff_name,
                    'replacement_staff_phone' => $leave->replacement_staff_phone,
                    'approvals' => $leave->approvals->map(function ($approval) {
                        return [
                            'approver' => $approval->approver ? [
                                'name' => $approval->approver->firstname . ' ' . $approval->approver->lastname,
                                'role' => $approval->approvalLevel ? $approval->approvalLevel->role_name : null
                            ] : null,
                            'status' => $approval->status,
                            'remark' => $approval->remark,
                            'action_date' => $approval->action_date
                        ];
                    })
                ];
            });

        // Get dashboard stats
        $stats = [
            'pendingApprovals' => Leave::where('status', 'pending')
                ->where('is_cancelled', false)
                ->count(),
            'onLeaveToday' => $staffOnLeave->count(),
            'totalLeaveBalance' => LeaveBalance::where('year', $currentYear)
                ->sum('total_entitled_days'),
            'exhaustedLeave' => LeaveBalance::where('year', $currentYear)
                ->whereRaw('days_remaining <= 0')
                ->count()
        ];

        // Get leave distribution by type
        $leaveDistribution = LeaveType::withCount(['leaves' => function ($query) {
            $query->where('status', 'approved')
                ->where('is_cancelled', false);
        }])->get();

        // Get department-wise leave usage
        $departmentLeave = Department::withCount(['users' => function ($query) {
            $query->whereHas('leaves', function ($q) {
                $q->where('status', 'approved')
                    ->where('is_cancelled', false);
            });
        }])->get();

        // Get recent leave applications with approval chain
        $recentApplications = Leave::with([
            'user.department', 
            'leaveType', 
            'approvals.approver',
            'approvals.approvalLevel'
        ])
            ->where('is_cancelled', false)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'user' => [
                        'firstname' => $leave->user->firstname,
                        'lastname' => $leave->user->lastname,
                        'staff_id' => $leave->user->staff_id,
                        'avatar' => $leave->user->avatar,
                        'department' => $leave->user->department ? [
                            'name' => $leave->user->department->name
                        ] : null
                    ],
                    'leave_type' => [
                        'name' => $leave->leaveType->name
                    ],
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'working_days' => $leave->working_days,
                    'status' => $leave->status,
                    'current_approval_level' => $leave->current_approval_level,
                    'approvals' => $leave->approvals->map(function ($approval) {
                        return [
                            'approver' => $approval->approver ? [
                                'name' => $approval->approver->firstname . ' ' . $approval->approver->lastname,
                                'role' => $approval->approvalLevel ? $approval->approvalLevel->role_name : null
                            ] : null,
                            'status' => $approval->status,
                            'remark' => $approval->remark,
                            'action_date' => $approval->action_date
                        ];
                    })
                ];
            });

        // Get upcoming approved leave
        $upcomingLeave = Leave::with(['user.department', 'leaveType'])
            ->where('start_date', '>', $today)
            ->where('status', 'approved')
            ->where('is_cancelled', false)
            ->orderBy('start_date')
            ->take(5)
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'user' => [
                        'firstname' => $leave->user->firstname,
                        'lastname' => $leave->user->lastname,
                        'staff_id' => $leave->user->staff_id,
                        'avatar' => $leave->user->avatar,
                        'department' => $leave->user->department ? [
                            'name' => $leave->user->department->name
                        ] : null
                    ],
                    'leave_type' => [
                        'name' => $leave->leaveType->name
                    ],
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'working_days' => $leave->working_days
                ];
            });

        return response()->json([
            'stats' => $stats,
            'staffOnLeave' => $staffOnLeave,
            'recentApplications' => $recentApplications,
            'upcomingLeave' => $upcomingLeave,
            'leaveDistribution' => [
                'labels' => $leaveDistribution->pluck('name'),
                'data' => $leaveDistribution->pluck('leaves_count')
            ],
            'departmentLeave' => [
                'labels' => $departmentLeave->pluck('name'),
                'data' => $departmentLeave->pluck('users_count')
            ]
        ]);
    }
} 