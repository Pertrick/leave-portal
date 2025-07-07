<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
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

    public function index()
    {
        $this->authorize('view_leaves');
        
        $user = auth()->user();
        $query = Leave::with(['user.department', 'leaveType', 'approvals.user'])
            ->where('status', 'pending');

        // Filter based on user role and permissions
        if ($user->hasRole('admin') || $user->hasRole('hr')) {
            // Admin and HR can see all pending leaves
        } elseif ($user->hasRole('hod')) {
            // HOD can see leaves from their department
            $query->whereHas('user.department', function ($q) use ($user) {
                $q->where('id', $user->department_id);
            });
        } elseif ($user->hasRole('supervisor')) {
            // Supervisor can see leaves from users they supervise
            $query->whereHas('user.activeSupervisors', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            });
        } else {
            // Regular users can only see their own leaves
            $query->where('user_id', $user->id);
        }

        $leaves = $query->latest()->paginate(15);

        return Inertia::render('leave/Approvals/Index', [
            'leaves' => $leaves,
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