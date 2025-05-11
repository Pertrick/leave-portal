<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use Inertia\Inertia;
use App\Models\Leave;
use App\Models\User;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\LeaveApplicationService;

class LeaveController extends Controller
{
    public function __construct(
        protected LeaveApplicationService $leaveService
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

    public function create(): Response
    {
        $leaveTypes = $this->leaveService->getLeaveTypes();

        return Inertia::render('leave/Create', [
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function store(Request $request)
    {
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

        $leave = $this->leaveService->create($validated, auth()->user());

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application submitted successfully.');
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
        if (!$leave->isPending()) {
            return redirect()->route('leaves.index')
                ->with('error', 'Only pending leave applications can be edited.');
        }

        $leaveTypes = $this->leaveService->getLeaveTypes();

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