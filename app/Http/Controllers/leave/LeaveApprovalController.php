<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Services\LeaveApplicationService;
use Illuminate\Http\Request;
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

        return Inertia::render('Leave/Approvals/Index', [
            'pendingLeaves' => $pendingLeaves,
        ]);
    }

    public function show(Leave $leave): Response
    {
        $leave->load(['leaveType', 'approvals.user', 'user']);

        return Inertia::render('Leave/Approvals/Show', [
            'leave' => $leave,
        ]);
    }

    public function approve(Request $request, Leave $leave)
    {
        $this->leaveService->approve($leave, auth()->user());

        return redirect()->route('leave.approvals.index')
            ->with('success', 'Leave application approved successfully.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $this->leaveService->reject($leave, auth()->user(), $validated['rejection_reason']);

        return redirect()->route('leave.approvals.index')
            ->with('success', 'Leave application rejected successfully.');
    }
} 