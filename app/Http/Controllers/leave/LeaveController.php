<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use Exception;
use Inertia\Inertia;
use App\Models\Leave;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Services\LeaveApplicationService;
use App\Http\Requests\Leave\SaveDraftRequest;
use App\Http\Requests\Leave\StoreLeaveRequest;
use Inertia\RedirectResponse as InertiaRedirectResponse;

class LeaveController extends Controller
{
    public function __construct(
        private readonly LeaveApplicationService $leaveService
    ) {}

    public function index(): Response
    {
        $leaves = $this->leaveService->getUserLeaves(Auth::guard('web')->user());
        $leaveTypes = $this->leaveService->getLeaveTypes();

        return Inertia::render('leave/Index', [
            'leaves' => $leaves,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function drafts(): Response
    {
        $drafts = $this->leaveService->getUserDrafts(Auth::guard('web')->user());
        $leaveTypes = $this->leaveService->getLeaveTypes();

        return Inertia::render('leave/Drafts', [
            'drafts' => $drafts,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function create(): Response
    {
        $leaveTypes = $this->leaveService->getLeaveTypes();
        $leaveBalances = $this->leaveService->getLeaveBalances(Auth::user());
        $pendingLeaves = $this->leaveService->getUserLeaves(Auth::user())
            ->where('status', 'pending')
            ->count();

        return Inertia::render('leave/Create', [
            'leaveTypes' => $leaveTypes,
            'leaveBalances' => $leaveBalances,
            'pendingLeaves' => $pendingLeaves,
        ]);
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        try {
            $user = Auth::guard('web')->user();
            // Check for pending leaves
            $pendingLeaves = $this->leaveService->getUserLeaves($user)
                ->where('status', 'pending')
                ->count();

            if ($pendingLeaves > 0) {
                return back()->with('error', 'You have pending leave requests. Please wait for approval before submitting new requests.');
            }

            $validated = $request->validated();
            $leave = $this->leaveService->create($validated, $user);

            return redirect()->route('leaves.index')
                ->with('success', 'Leave application submitted successfully.');
        }
        catch(Exception $ex) {
            return back()->with('error', $ex->getMessage());
        }
    }

    public function saveDraft(SaveDraftRequest $request, ?Leave $leave = null): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        $userDrafts = $this->leaveService->getUserDrafts($user);
        if (config('leave.max_draft_leave_per_user') && $userDrafts->count() >= config('leave.max_draft_leave_per_user')) {
            return back()->with('error', 'You have reached the maximum number of draft leave applications.');
        }

        $validated = $request->validated();
        $leave = $this->leaveService->createOrUpdateDraft($validated, $user, $leave);
    
        return redirect()->route('leaves.drafts')
            ->with('success', 'Leave draft saved successfully.');
    }
    

    public function show(Leave $leave): Response
    {
        $leave->load(['leaveType', 'approvals.user', 'user']);
        $leave = $this->leaveService->getLeaveWithFormattedAttachment($leave);

        return Inertia::render('leave/Show', [
            'leave' => $leave,
        ]);
    }

    public function edit(Leave $leave): Response|RedirectResponse
    {
        abort_if(Auth::guard('web')->id() != $leave->user_id, 403, 'You are not authorized to edit this leave application.');

        if (!$leave->isPending() && !$leave->isDraft()) {
            return redirect()->route('leaves.index')
                ->with('error', 'Only pending leave applications can be edited.');
        }

        $leaveTypes = $this->leaveService->getLeaveTypes();
        $leaveBalances = $this->leaveService->getLeaveBalances(Auth::guard('web')->user());
        $leave = $this->leaveService->getLeaveWithFormattedAttachment($leave);
        
        return Inertia::render('leave/Edit', [
            'leave' => $leave,
            'leaveTypes' => $leaveTypes,
            'leaveBalances' => $leaveBalances,
        ]);
    }

    public function update(StoreLeaveRequest $request, Leave $leave): RedirectResponse
    {
        try {
            $user = Auth::guard('web')->user();
            abort_if($user->id != $leave->user_id, 403, 'You are not authorized to update this leave application.');

            if (!$leave->isPending() && !$leave->isDraft()) {
                return redirect()->route('leaves.index')
                    ->with('error', 'Only pending leave applications can be updated.');
            }

            $this->leaveService->update($leave, $request->validated());

            return redirect()->route('leaves.index')
                ->with('success', 'Leave application updated successfully.');
        }
        catch(Exception $ex) {
            return back()->with('error', $ex->getMessage());
        }
    }

    public function destroy(Leave $leave): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        abort_if($user->getAuthIdentifier() != $leave->user_id, 403, 'You are not authorized to delete this leave application.');
        
        if (!$leave->isPending() && !$leave->isDraft()) {
            return redirect()->route('leaves.index')
                ->with('error', 'Only pending leave applications can be cancelled.');
        }

        $leave->delete();

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application cancelled successfully.');
    }

    
    public function cancel(Leave $leave): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        abort_if($user->getAuthIdentifier() != $leave->user_id, 403, 'You are not authorized to delete this leave application.');
        
        if (!$leave->isPending() && !$leave->isDraft()) {
            return redirect()->route('leaves.index')
                ->with('error', 'Only pending leave applications can be cancelled.');
        }

        $leave->update(['is_cancelled' => true]);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application cancelled successfully.');
    }

    /**
     * Calculate leave duration based on dates and leave type
     */
    public function calculateDuration(Request $request)
    {
       $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type_id' => 'required|exists:leave_types,id'
        ]);


       $calculateDays = $this->leaveService->calculateDays($validated);
        return response()->json([
            'calendar_days' => $calculateDays['calendar_days'],
            'working_days' => $calculateDays['working_days']
        ]);
    }
} 