<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use Carbon\Carbon;
use App\Models\User;
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

class LeaveController extends Controller
{
    public function __construct(
        private readonly LeaveApplicationService $leaveService
    ) {}

    public function index(): Response
    {
        $leaves = $this->leaveService->getUserLeaves(auth()->user());
        $leaveTypes = $this->leaveService->getLeaveTypes();

        return Inertia::render('leave/Index', [
            'leaves' => $leaves,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function drafts(): Response
    {
        $drafts = $this->leaveService->getUserDrafts(auth()->user());
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

        return Inertia::render('leave/Create', [
            'leaveTypes' => $leaveTypes,
            'leaveBalances' => $leaveBalances,
        ]);
    }

    public function store(StoreLeaveRequest $request)
    {
        $validated = $request->validated();
        $leave = $this->leaveService->create($validated, auth()->user());

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application submitted successfully.');
    }

    public function saveDraft(SaveDraftRequest $request)
    {
        $validated = $request->validated();
        $leave = $this->leaveService->createDraft($validated, auth()->user());

        return redirect()->route('leaves.drafts')
            ->with('success', 'Leave application saved as draft successfully.');
    }

    public function show(Leave $leave): Response
    {
        $leave->load(['leaveType', 'approvals.user', 'user']);

        return Inertia::render('leave/Show', [
            'leave' => $leave,
        ]);
    }

    public function edit(Leave $leave): Response|RedirectResponse
    {
        if (!$leave->isPending() && !$leave->isDraft()) {
            return redirect()->route('leaves.index')
                ->with('error', 'Only pending leave applications can be edited.');
        }

        $leaveTypes = $this->leaveService->getLeaveTypes();
        
        $leave->start_date = Carbon::parse($leave->start_date)->format('m/d/Y');
        $leave->end_date = Carbon::parse($leave->end_date)->format('m/d/Y');

        return Inertia::render('leave/Edit', [
            'leave' => $leave,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function update(Request $request, Leave $leave)
    {
        if (!$leave->isPending()) {
            return redirect()->route('leaves.index')
                ->with('error', 'Only pending leave applications can be updated.');
        }

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'applicant_comment' => 'nullable|string',
            'replacement_staff_name' => 'nullable|string|max:255',
            'replacement_staff_phone' => 'nullable|string|max:20',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $this->leaveService->update($leave, $validated);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application updated successfully.');
    }

    public function destroy(Leave $leave)
    {
        if (!$leave->isPending()) {
            return redirect()->route('leaves.index')
                ->with('error', 'Only pending leave applications can be cancelled.');
        }

        $leave->delete();

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application cancelled successfully.');
    }
} 