<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Department;
use App\Models\LeaveType;
use App\Exports\StaffLeaveReportExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LeaveBalance;
use App\Models\User;

class StaffReportController extends Controller
{
    public function index(Request $request)
    {
        // Get all users based on role
        $query = User::with(['department', 'roles']);

        // Role-based data access
        if (auth()->user()->hasRole('supervisor')) {
            // Get supervised users
            $supervisedUsers = auth()->user()->supervisedUsers()->pluck('id');
            $query->whereIn('id', $supervisedUsers);
        } elseif (auth()->user()->hasRole('hod')) {
            // Get department users
            $departmentUsers = auth()->user()->department->users()->pluck('id');
            $query->whereIn('id', $departmentUsers);
        }
        // Admin can see all data, no additional filters needed

        // Apply filters
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Get paginated users
        $users = $query->latest()
            ->paginate(10)
            ->withQueryString();

        // Get leave statistics for each user
        $currentYear = Carbon::now()->year;
        $userLeaveStats = [];

        foreach ($users as $user) {
            // Get all leave balances for the user
            $leaveBalances = LeaveBalance::where('user_id', $user->id)
                ->with('leaveType')
                ->get();

            // Get approved leaves for the current year
            $approvedLeaves = Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $currentYear)
                ->get();

            // Get pending leaves
            $pendingLeaves = Leave::where('user_id', $user->id)
                ->where('status', 'pending')
                ->whereYear('start_date', $currentYear)
                ->get();

            // Calculate statistics
            $userLeaveStats[$user->id] = [
                'entitlements' => $leaveBalances->map(function ($balance) {
                    return [
                        'type' => $balance->leaveType->name,
                        'total' => $balance->total_days,
                        'used' => $balance->used_days,
                        'remaining' => $balance->remaining_days,
                        'percentage_used' => $balance->total_days > 0 
                            ? round(($balance->used_days / $balance->total_days) * 100, 1) 
                            : 0
                    ];
                }),
                'total_used' => $approvedLeaves->sum('working_days'),
                'leave_count' => [
                    'total' => $approvedLeaves->count(),
                    'pending' => $pendingLeaves->count(),
                    'by_type' => $approvedLeaves->groupBy('leave_type_id')
                        ->map(function ($leaves) {
                            return [
                                'count' => $leaves->count(),
                                'days' => $leaves->sum('working_days')
                            ];
                        })
                ],
                'monthly_usage' => $approvedLeaves->groupBy(function ($leave) {
                    return Carbon::parse($leave->start_date)->format('M Y');
                })->map(function ($leaves) {
                    return $leaves->sum('working_days');
                }),
                'pending_leaves' => $pendingLeaves->map(function ($leave) {
                    return [
                        'type' => $leave->leaveType->name,
                        'start_date' => $leave->start_date,
                        'end_date' => $leave->end_date,
                        'days' => $leave->working_days
                    ];
                })
            ];
        }

        return Inertia::render('Admin/StaffReport', [
            'users' => $users,
            'departments' => Department::all(),
            'leaveTypes' => LeaveType::all(),
            'filters' => $request->only([
                'department',
                'search'
            ]),
            'userLeaveStats' => $userLeaveStats,
            'userRole' => auth()->user()->roles->first()->name,
            'currentYear' => $currentYear
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['department', 'search']);
        $currentYear = Carbon::now()->year;

        // Get users based on role and filters
        $query = User::with(['department', 'roles']);

        if (auth()->user()->hasRole('supervisor')) {
            $supervisedUsers = auth()->user()->supervisedUsers()->pluck('id');
            $query->whereIn('id', $supervisedUsers);
        } elseif (auth()->user()->hasRole('hod')) {
            $departmentUsers = auth()->user()->department->users()->pluck('id');
            $query->whereIn('id', $departmentUsers);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $users = $query->get();

        $data = [];
        foreach ($users as $user) {
            $leaveBalances = LeaveBalance::where('user_id', $user->id)
                ->with('leaveType')
                ->get();

            $approvedLeaves = Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $currentYear)
                ->get();

            $pendingLeaves = Leave::where('user_id', $user->id)
                ->where('status', 'pending')
                ->whereYear('start_date', $currentYear)
                ->get();

            $row = [
                'Staff ID' => $user->staff_id,
                'Name' => $user->firstname . ' ' . $user->lastname,
                'Department' => $user->department->name ?? 'N/A',
            ];

            // Add leave entitlements
            foreach ($leaveBalances as $balance) {
                $row[$balance->leaveType->name . ' Total'] = $balance->total_days;
                $row[$balance->leaveType->name . ' Used'] = $balance->used_days;
                $row[$balance->leaveType->name . ' Remaining'] = $balance->remaining_days;
            }

            // Add summary
            $row['Total Leaves Taken'] = $approvedLeaves->count();
            $row['Total Days Used'] = $approvedLeaves->sum('working_days');
            $row['Pending Leaves'] = $pendingLeaves->count();
            $row['Pending Days'] = $pendingLeaves->sum('working_days');

            $data[] = $row;
        }

        return Excel::download(
            new StaffLeaveReportExport($data),
            'staff-leave-report-' . Carbon::now()->format('Y-m-d') . '.xlsx'
        );
    }
} 