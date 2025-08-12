<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveApplicationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LeaveApprovalController extends Controller
{
    public function __construct(
        protected LeaveApplicationService $leaveService,
        protected NotificationService $notificationService
    ) {
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        
        $query = Leave::with(['user.department', 'leaveType', 'approvals.approver', 'approvals.approvalLevel']);

        // Filter based on tab (pending vs history)
        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } else {
            // History tab - show approved and rejected leaves
            $query->whereIn('status', ['approved', 'rejected']);
        }

        // Filter based on user role and permissions
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr')) {
            // Admin and HR can see all leaves
        } elseif (auth()->user()->hasRole('hod')) {
            // HOD can see leaves where they are assigned as approver
            $query->whereHas('approvals', function ($q) use ($tab) {
                $q->where('approver_id', auth()->user()->id);
                if ($tab === 'pending') {
                    $q->where('status', 'pending');
                } else {
                    $q->whereIn('status', ['approved', 'rejected']);
                }
            });
        } elseif (auth()->user()->hasRole('supervisor')) {
            // Supervisor can see leaves where they are assigned as approver
            $query->whereHas('approvals', function ($q) use ($tab) {
                $q->where('approver_id', auth()->user()->id);
                if ($tab === 'pending') {
                    $q->where('status', 'pending');
                } else {
                    $q->whereIn('status', ['approved', 'rejected']);
                }
            });
        } else {
            // Regular users can only see their own leaves
            $query->where('user_id', auth()->user()->id);
        }

        // Apply additional filters
        if ($request->filled('type')) {
            $query->where('leave_type_id', $request->type);
        }
        
        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('leaveType', function ($typeQuery) use ($search) {
                    $typeQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $leaves = $query->latest()->paginate(15);

        // Get leave types for filter dropdown
        $leaveTypes = \App\Models\LeaveType::orderBy('name')->get(['id', 'name']);

        // Get employees for filter dropdown (only for admin/HR/HOD)
        $employees = collect();
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr')) {
            $employees = User::orderBy('firstname')->get(['id', 'firstname', 'lastname', 'email']);
        } elseif (auth()->user()->hasRole('hod')) {
            // HOD can see employees in their department
            $employees = User::where('department_id', auth()->user()->department_id)
                ->orderBy('firstname')
                ->get(['id', 'firstname', 'lastname', 'email']);
        }

        return Inertia::render('leave/Approvals/Index', [
            'leaves' => $leaves,
            'leaveTypes' => $leaveTypes,
            'employees' => $employees,
            'filters' => [
                'tab' => $tab,
                'type' => $request->get('type'),
                'employee' => $request->get('employee'),
                'search' => $request->get('search'),
            ]
        ]);
    }

    public function show(Leave $leave): Response
    {
        $leave->load(['leaveType', 'approvals.approver', 'approvals.approvalLevel', 'user']);

        // Get the current user's approval record
        $currentUserApproval = $leave->approvals()
            ->where('approver_id', Auth::id())
            ->first();

        // Check if user is the next approver in sequence
        $isNextApprover = false;
        if ($currentUserApproval && $currentUserApproval->status === 'pending') {
            $isNextApprover = $leave->approvals()
                ->where('status', 'pending')
                ->orderBy('sequence')
                ->first()
                ->id === $currentUserApproval->id;
        }

        return Inertia::render('leave/Approvals/Show', [
            'leave' => $leave,
            'currentUserApproval' => $currentUserApproval,
            'isNextApprover' => $isNextApprover,
        ]);
    }

    public function approve(Request $request, Leave $leave)
    {
        $this->leaveService->approve($leave, Auth::user(), $request->input('comment'));

        // Send notification for approval
        $this->notificationService->notifyLeaveApproved($leave, Auth::user());

        return redirect()->route('leave.approvals.index')
            ->with('success', 'Leave application approved successfully.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10',
            'comment' => 'nullable|string'
        ]);

        $this->leaveService->reject($leave, Auth::user(), $validated['reason'], $validated['comment']);

        // Send notification for rejection
        $this->notificationService->notifyLeaveRejected($leave, Auth::user(), $validated['reason']);

        return redirect()->route('leave.approvals.index')
            ->with('success', 'Leave application rejected successfully.');
    }
}