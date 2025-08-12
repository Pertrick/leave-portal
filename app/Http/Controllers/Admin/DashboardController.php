<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Debug information
            Log::info('Admin Dashboard Debug', [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'department_id' => $user->department_id,
                'user_level_id' => $user->user_level_id ?? 'null'
            ]);
            
            // Get admin-specific stats
            $pendingApprovals = Leave::where('status', 'pending')->count();
            $onLeaveToday = Leave::where('status', 'approved')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count();
            
            // Get leave balance stats
            $lowLeaveBalance = LeaveBalance::where('days_remaining', '<=', 5)
                ->where('days_remaining', '>', 0)
                ->count();
            $exhaustedLeave = LeaveBalance::where('days_remaining', '<=', 0)->count();
            
            // Get total leave stats
            $totalEntitledDays = LeaveBalance::sum('total_entitled_days');
            $totalDaysTaken = LeaveBalance::sum('days_taken');
            $totalRemainingDays = LeaveBalance::sum('days_remaining');
            
            // Get recent applications
            $recentApplications = Leave::with(['user', 'leaveType'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'user' => [
                            'firstname' => $application->user->firstname,
                            'lastname' => $application->user->lastname,
                            'staff_id' => $application->user->staff_id,
                            'avatar' => $application->user->profile_photo_url,
                        ],
                        'leave_type' => [
                            'name' => $application->leaveType->name,
                        ],
                        'start_date' => $application->start_date,
                        'end_date' => $application->end_date,
                        'status' => $application->status,
                    ];
                });
            
            // Get upcoming leave
            $upcomingLeave = Leave::where('status', 'approved')
                ->where('start_date', '>=', now())
                ->with(['user', 'leaveType'])
                ->take(5)
                ->get()
                ->map(function ($leave) {
                    return [
                        'id' => $leave->id,
                        'user' => [
                            'firstname' => $leave->user->firstname,
                            'lastname' => $leave->user->lastname,
                            'staff_id' => $leave->user->staff_id,
                            'avatar' => $leave->user->profile_photo_url,
                        ],
                        'leave_type' => [
                            'name' => $leave->leaveType->name,
                        ],
                        'start_date' => $leave->start_date,
                        'end_date' => $leave->end_date,
                        'status' => $leave->status,
                    ];
                });
            
            // Get staff on leave today
            $staffOnLeave = Leave::where('status', 'approved')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->with(['user.department', 'leaveType'])
                ->get()
                ->map(function ($leave) {
                    return [
                        'id' => $leave->id,
                        'user' => [
                            'firstname' => $leave->user->firstname,
                            'lastname' => $leave->user->lastname,
                            'staff_id' => $leave->user->staff_id,
                            'avatar' => $leave->user->profile_photo_url,
                            'email' => $leave->user->email,
                            'phone' => $leave->user->phone,
                            'department' => $leave->user->department ? [
                                'name' => $leave->user->department->name,
                            ] : null,
                        ],
                        'leave_type' => [
                            'name' => $leave->leaveType->name,
                        ],
                        'start_date' => $leave->start_date,
                        'end_date' => $leave->end_date,
                        'status' => $leave->status,
                        'replacement_staff_name' => $leave->replacement_staff_name ?? null,
                        'replacement_staff_phone' => $leave->replacement_staff_phone ?? null,
                    ];
                });
            
            // Get leave distribution data
            $leaveDistribution = LeaveType::withCount(['leaves' => function($query) {
                $query->where('status', 'approved');
            }])
            ->get()
            ->map(function ($type) {
                return [
                    'labels' => [$type->name],
                    'data' => [$type->leaves_count],
                ];
            });
            
            // Get department leave data - simplified approach
            $departmentLeave = Department::withCount('users')
                ->get()
                ->map(function ($dept) {
                    return [
                        'labels' => [$dept->name],
                        'data' => [$dept->users_count],
                    ];
                });
            
            $response = [
                'stats' => [
                    'pendingApprovals' => $pendingApprovals,
                    'onLeaveToday' => $onLeaveToday,
                    'totalRemainingDays' => $totalRemainingDays,
                    'totalEntitledDays' => $totalEntitledDays,
                    'totalDaysTaken' => $totalDaysTaken,
                    'exhaustedLeave' => $exhaustedLeave,
                    'lowLeaveBalance' => $lowLeaveBalance,
                ],
                'recentApplications' => $recentApplications,
                'upcomingLeave' => $upcomingLeave,
                'staffOnLeave' => $staffOnLeave,
                'leaveDistribution' => [
                    'labels' => $leaveDistribution->pluck('labels')->flatten()->toArray(),
                    'data' => $leaveDistribution->pluck('data')->flatten()->toArray(),
                ],
                'departmentLeave' => [
                    'labels' => $departmentLeave->pluck('labels')->flatten()->toArray(),
                    'data' => $departmentLeave->pluck('data')->flatten()->toArray(),
                ],
            ];
            
            Log::info('Admin Dashboard Response', [
                'pendingApprovals' => $pendingApprovals,
                'onLeaveToday' => $onLeaveToday,
                'recentApplications_count' => count($recentApplications),
                'upcomingLeave_count' => count($upcomingLeave),
                'staffOnLeave_count' => count($staffOnLeave),
            ]);
            
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'stats' => [
                    'pendingApprovals' => 0,
                    'onLeaveToday' => 0,
                    'totalRemainingDays' => 0,
                    'totalEntitledDays' => 0,
                    'totalDaysTaken' => 0,
                    'exhaustedLeave' => 0,
                    'lowLeaveBalance' => 0,
                ],
                'recentApplications' => [],
                'upcomingLeave' => [],
                'staffOnLeave' => [],
                'leaveDistribution' => [
                    'labels' => [],
                    'data' => [],
                ],
                'departmentLeave' => [
                    'labels' => [],
                    'data' => [],
                ],
            ]);
        }
    }

    private function getLeaveTypeColor($type)
    {
        return match (strtolower($type)) {
            'annual' => 'bg-blue-500',
            'sick' => 'bg-red-500',
            'unpaid' => 'bg-gray-500',
            'maternity' => 'bg-pink-500',
            'paternity' => 'bg-purple-500',
            default => 'bg-indigo-500',
        };
    }

    private function getStatusColor($status)
    {
        return match (strtolower($status)) {
            'approved' => 'bg-emerald-500',
            'pending' => 'bg-amber-500',
            'rejected' => 'bg-red-500',
            default => 'bg-gray-500',
        };
    }
} 