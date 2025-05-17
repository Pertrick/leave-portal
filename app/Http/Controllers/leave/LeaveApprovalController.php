<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use App\Services\LeaveApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LeaveApprovalController extends Controller
{
    public function __construct(
        protected LeaveApplicationService $leaveService
    ) {}

    public function index(): Response
    {
        $pendingLeaves = $this->leaveService->getPendingLeaves();
        $leaveTypes = $this->leaveService->getLeaveTypes();
        $employees = User::where('is_active', true)
            ->where('department_id', Auth::user()->department_id)
            ->get(['id','firstname','lastname','email']);

        return Inertia::render('leave/Approvals/Index', [
            'leaves' => $pendingLeaves,
            'leaveTypes' => $leaveTypes,
            'employees' => $employees,
        ]);
    }

    public function show(Leave $leave): Response
    {
        $leave->load(['leaveType', 'approvals.approver', 'approvals.approvalLevel', 'user']);

        return Inertia::render('leave/Approvals/Show', [
            'leave' => $leave,
        ]);
    }

    public function approve(Request $request, Leave $leave)
    {
        $this->leaveService->approve($leave, Auth::user(), $request->input('comment'));

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

        return redirect()->route('leave.approvals.index')
            ->with('success', 'Leave application rejected successfully.');
    }
} 