<?php
declare(strict_types=1);

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaveTypeController extends Controller
{
    public function index(): Response
    {
        $leaveTypes = LeaveType::withCount('leaves')->get();

        return Inertia::render('Leave/Types/Index', [
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Leave/Types/Create');
    }

    public function store(Request $request):RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:leave_types',
            'is_active' => 'boolean',
            'requires_medical_proof' => 'boolean',
        ]);

        LeaveType::create($validated);

        return redirect()->route('leave.types.index')
            ->with('success', 'Leave type created successfully.');
    }

    public function edit(LeaveType $leaveType): Response
    {
        return Inertia::render('Leave/Types/Edit', [
            'leaveType' => $leaveType,
        ]);
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:leave_types,name,' . $leaveType->id,
            'is_active' => 'boolean',
            'requires_medical_proof' => 'boolean',
        ]);

        $leaveType->update($validated);

        return redirect()->route('leave.types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType): RedirectResponse
    {
        if ($leaveType->leaves()->exists()) {
            return redirect()->route('leave.types.index')
                ->with('error', 'Cannot delete leave type that has associated leave applications.');
        }

        $leaveType->delete();

        return redirect()->route('leave.types.index')
            ->with('success', 'Leave type deleted successfully.');
    }
} 