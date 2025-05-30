<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Leave;
use App\Models\Holiday;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get leave balances with leave type
        $leaveBalances = LeaveBalance::where('user_id', $user->id)
            ->with('leaveType')
            ->get();
        
        $totalAvailableDays = $leaveBalances->sum('days_remaining');
        
        // Get pending requests
        $pendingRequests = Leave::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        
        // Get upcoming leaves - total days
        $upcomingLeaves = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '>=', now())
            ->sum('working_days');

        // Get leave distribution by type
        $leaveDistribution = LeaveBalance::where('user_id', $user->id)
            ->with(['leaveType' => function($query) use ($user) {
                $query->with(['leaveEntitlements' => function($query) use ($user) {
                    $query->where('user_level_id', $user->user_level_id)
                        ->where('is_active', true);
                }]);
            }])
            ->get()
            ->map(function ($balance) {
                // Get the entitlement for this user's level and leave type
              //  $entitlement = $balance->leaveType->leaveEntitlements->first();
                return [
                    'type' => $balance->leaveType->name,
                    'used' => $balance->days_taken,
                    'total' => $balance->total_entitled_days,
                    'remaining' => $balance->days_remaining,
                    'color' => $this->getLeaveTypeColor($balance->leaveType->name)
                ];
            });

        // Get leave type usage analysis
        $leaveTypeAnalysis = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->with('leaveType')
            ->get()
            ->groupBy('leaveType.name')
            ->map(function ($leaves, $type) {
                return [
                    'type' => $type,
                    'total_days' => $leaves->sum('working_days'),
                    'count' => $leaves->count(),
                    'average_duration' => round($leaves->avg('working_days'), 1),
                    'longest_leave' => $leaves->max('working_days'),
                    'shortest_leave' => $leaves->min('working_days'),
                    'color' => $this->getLeaveTypeColor($type)
                ];
            })
            ->values();

        // Get status overview
        $statusOverview = Leave::where('user_id', $user->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst($item->status),
                    'count' => $item->count,
                    'color' => $this->getStatusColor($item->status)
                ];
            });
        
        // Get team members with their current leave status
        $teamMembers = User::where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->with(['leaves' => function ($query) {
                $query->where('status', 'approved')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            }])
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'designation' => $member->designation,
                    'avatar' => $member->profile_photo_url,
                    'isOnLeave' => $member->leaves->isNotEmpty(),
                    'leaveDetails' => $member->leaves->map(function ($leave) {
                        return [
                            'type' => $leave->leaveType->name,
                            'start_date' => $leave->start_date,
                            'end_date' => $leave->end_date,
                            'days' => $leave->working_days
                        ];
                    })->first()
                ];
            });
        
        // Get recent leave requests with leave type
        $recentRequests = Leave::where('user_id', $user->id)
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'type' => $request->leaveType->name,
                    'days' => $request->working_days,
                    'start_date' => $request->start_date,
                    'status' => $request->status,
                ];
            });
        
        // Get calendar data with leaves and holidays
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $calendarDays = collect();
        $currentDate = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        
        // Get all approved leaves for the user in the current month
        $userLeaves = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('start_date', '<=', $startOfMonth)
                            ->where('end_date', '>=', $endOfMonth);
                    });
            })
            ->with('leaveType')
            ->get();
        
        while ($currentDate <= $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY)) {
            $isCurrentMonth = $currentDate->month === now()->month;
            $isToday = $currentDate->isToday();
            
            // Check for leaves
            $hasLeave = Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where(function ($query) use ($currentDate) {
                    $query->whereDate('start_date', '<=', $currentDate)
                        ->whereDate('end_date', '>=', $currentDate);
                })
                ->exists();
            
            // Check for holidays
            $holiday = Holiday::whereDate('date', $currentDate)->first();
            $isHoliday = $holiday !== null;
            
            $calendarDays->push([
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->day,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $isToday,
                'hasLeave' => $hasLeave,
                'isHoliday' => $isHoliday,
                'holidayName' => $holiday ? $holiday->name : null,
            ]);
            
            $currentDate->addDay();
        }
        
        return Inertia::render('Dashboard', [
            'totalAvailableDays' => $totalAvailableDays,
            'pendingRequests' => $pendingRequests,
            'upcomingLeaves' => $upcomingLeaves,
            'teamMembers' => $teamMembers,
            'recentRequests' => $recentRequests,
            'calendarDays' => $calendarDays,
            'leaveDistribution' => $leaveDistribution,
            'leaveTypeAnalysis' => $leaveTypeAnalysis,
            'statusOverview' => $statusOverview,
        ]);
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