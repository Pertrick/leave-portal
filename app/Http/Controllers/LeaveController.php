<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveController extends Controller
{
    /**
     * Show the form for editing the specified leave application.
     */
    public function edit(Leave $leave)
    {
        // Check if the leave belongs to the authenticated user
        if ($leave->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if the leave is in draft status
        if ($leave->status !== 'draft') {
            return redirect()->route('leaves.show', $leave)
                ->with('error', 'Only draft applications can be edited.');
        }

        return Inertia::render('Leave/Edit', [
            'leave' => $leave->load('leaveType'),
            'leaveTypes' => LeaveType::where('is_active', true)->get(),
        ]);
    }

    /**
     * Update the specified leave application.
     */
    public function update(Request $request, Leave $leave)
    {
        // Check if the leave belongs to the authenticated user
        if ($leave->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if the leave is in draft status
        if ($leave->status !== 'draft') {
            return redirect()->route('leaves.show', $leave)
                ->with('error', 'Only draft applications can be edited.');
        }

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'applicant_comment' => 'nullable|string|max:1000',
            'replacement_staff_name' => 'nullable|string|max:255',
            'replacement_staff_phone' => 'nullable|string|max:20',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Handle file upload if present
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('leave-attachments', 'public');
        }

        $leave->update($validated);

        return redirect()->route('leaves.show', $leave)
            ->with('success', 'Leave application updated successfully.');
    }
} 