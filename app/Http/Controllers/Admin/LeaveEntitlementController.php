<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveEntitlement;
use App\Models\LeaveType;
use App\Models\UserLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveEntitlementController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveEntitlement::query()
            ->with(['userLevel', 'leaveType'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('userLevel', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('leaveType', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($request->input('user_level_id'), function ($query, $userLevelId) {
                $query->where('user_level_id', $userLevelId);
            })
            ->when($request->input('leave_type_id'), function ($query, $leaveTypeId) {
                $query->where('leave_type_id', $leaveTypeId);
            })
            ->when($request->input('status') !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('status'));
            });

        $entitlements = $query->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/LeaveEntitlements/Index', [
            'entitlements' => $entitlements,
            'userLevels' => UserLevel::orderBy('level')->get(),
            'leaveTypes' => LeaveType::where('is_active', true)->orderBy('name')->get(),
            'filters' => $request->only(['search', 'user_level_id', 'leave_type_id', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_level_id' => 'required|exists:user_levels,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'days_per_year' => 'required|integer|min:0',
            'can_carry_over' => 'boolean',
            'max_carry_over_days' => 'required_if:can_carry_over,true|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate entitlement
        $existing = LeaveEntitlement::where('user_level_id', $validated['user_level_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->first();

        if ($existing) {
            return back()->withErrors(['duplicate' => 'An entitlement for this user level and leave type already exists.']);
        }

        LeaveEntitlement::create($validated);

        return redirect()->back()->with('success', 'Leave entitlement created successfully.');
    }

    public function update(Request $request, LeaveEntitlement $entitlement)
    {
        $validated = $request->validate([
            'user_level_id' => 'required|exists:user_levels,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'days_per_year' => 'required|integer|min:0',
            'can_carry_over' => 'boolean',
            'max_carry_over_days' => 'required_if:can_carry_over,true|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate entitlement (excluding current one)
        $existing = LeaveEntitlement::where('user_level_id', $validated['user_level_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('id', '!=', $entitlement->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['duplicate' => 'An entitlement for this user level and leave type already exists.']);
        }

        $entitlement->update($validated);

        return redirect()->back()->with('success', 'Leave entitlement updated successfully.');
    }

    public function destroy(LeaveEntitlement $entitlement)
    {
        $entitlement->delete();

        return redirect()->back()->with('success', 'Leave entitlement deleted successfully.');
    }

    public function toggleStatus(LeaveEntitlement $entitlement)
    {
        $entitlement->update(['is_active' => !$entitlement->is_active]);

        return redirect()->back()->with('success', 'Leave entitlement status updated successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'user_level_id' => 'required|exists:user_levels,id',
            'entitlements' => 'required|array',
            'entitlements.*.leave_type_id' => 'required|exists:leave_types,id',
            'entitlements.*.days_per_year' => 'required|integer|min:0',
            'entitlements.*.can_carry_over' => 'boolean',
            'entitlements.*.max_carry_over_days' => 'required_if:entitlements.*.can_carry_over,true|integer|min:0',
            'entitlements.*.is_active' => 'boolean',
        ]);

        foreach ($validated['entitlements'] as $entitlementData) {
            LeaveEntitlement::updateOrCreate(
                [
                    'user_level_id' => $validated['user_level_id'],
                    'leave_type_id' => $entitlementData['leave_type_id'],
                ],
                $entitlementData
            );
        }

        return redirect()->back()->with('success', 'Leave entitlements updated successfully.');
    }

    public function showUsersByLevel(UserLevel $userLevel, Request $request)
    {
        $query = $userLevel->users()
            ->with(['department', 'userLevel'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('staff_id', 'like', "%{$search}%");
                });
            })
            ->when($request->input('department_id'), function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($request->input('status') !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('status'));
            });

        $users = $query->paginate(15)->withQueryString();

        $departments = \App\Models\Department::orderBy('name')->get();

        return Inertia::render('Admin/LeaveEntitlements/Users', [
            'userLevel' => $userLevel,
            'users' => $users,
            'departments' => $departments,
            'filters' => $request->only(['search', 'department_id', 'status']),
        ]);
    }
} 