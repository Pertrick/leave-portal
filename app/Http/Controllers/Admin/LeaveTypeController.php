<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveType::query();

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply status filter
        if ($request->has('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $leaveTypes = $query->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/LeaveTypes/Index', [
            'leaveTypes' => $leaveTypes,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'requires_medical_proof' => 'boolean',
            'weekend_days' => 'required|array',
            'is_active' => 'boolean'
        ]);

        LeaveType::create($validated);

        return redirect()->back()->with('success', 'Leave type created successfully.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'requires_medical_proof' => 'boolean',
            'weekend_days' => 'required|array',
            'is_active' => 'boolean'
        ]);

        $leaveType->update($validated);

        return redirect()->back()->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();

        return redirect()->back()->with('success', 'Leave type deleted successfully.');
    }
} 