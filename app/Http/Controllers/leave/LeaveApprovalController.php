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
    ) {
    }

    public function index(Request $request): Response
    {
        $query = Leave::query()
            ->with(['leaveType', 'user', 'approvals.approver', 'approvals.approvalLevel']);

        if ($request->input('tab') === 'history') {
            $query->whereHas('approvals', function ($q) {
                $q->where('approver_id', Auth::id())
                    ->whereIn('status', ['approved', 'rejected']);
            });
        } else {
            $query->where('status', 'pending')
                ->whereHas('approvals', function ($q) {
                    $q->where('approver_id', Auth::id())
                        ->where('status', 'pending')
                        ->where('sequence', function ($subQuery) {
                            $subQuery->select('sequence')
                                ->from('leave_approvals')
                                ->whereColumn('leave_id', 'leaves.id')
                                ->where('status', 'pending')
                                ->orderBy('sequence')
                                ->limit(1);
                        });
                });
        }

        // Apply common filters
        $query->when($request->input('type'), function ($query) use ($request) {
            $query->where('leave_type_id', $request->input('type'));
        })
            ->when($request->input('employee'), function ($query) use ($request) {
                $query->where('user_id', $request->input('employee'));
            })
            ->when($request->input('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                        ->orWhereHas('leaveType', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            });

        $leaves = $query->latest()->paginate(10);

        $leaveTypes = $this->leaveService->getLeaveTypes();
        $employees = User::where('is_active', true)
            ->where('department_id', Auth::user()->department_id)
            ->get(['id', 'firstname', 'lastname', 'email']);

        return Inertia::render('leave/Approvals/Index', [
            'leaves' => $leaves,
            'leaveTypes' => $leaveTypes,
            'employees' => $employees,
            'filters' => $request->only(['tab', 'type', 'employee', 'search', 'status', 'time_period']),
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