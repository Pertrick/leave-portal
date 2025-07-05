<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Department::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->input('status') !== null, function ($query) use ($request) {
                $query->where('status', $request->boolean('status'));
            });

        $departments = $query->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Departments/Index', [
            'departments' => $departments,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        Department::create($validated);

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $department->update($validated);

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully.');
    }

    public function toggleStatus(Department $department)
    {
        $department->update(['status' => !$department->status]);

        return redirect()->back()->with('success', 'Department status updated successfully.');
    }

    public function showUsers(Department $department, Request $request)
    {
        $query = $department->users()
            ->with(['userLevel', 'department'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('staff_id', 'like', "%{$search}%");
                });
            })
            ->when($request->input('user_level_id'), function ($query, $userLevelId) {
                $query->where('user_level_id', $userLevelId);
            })
            ->when($request->input('status') !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('status'));
            });

        $users = $query->paginate(15)->withQueryString();

        $userLevels = UserLevel::orderBy('level')->get();

        return Inertia::render('Admin/Departments/Users', [
            'department' => $department,
            'users' => $users,
            'userLevels' => $userLevels,
            'filters' => $request->only(['search', 'user_level_id', 'status']),
        ]);
    }

    public function exportUsers(Department $department, Request $request)
    {
        $query = $department->users()
            ->with(['userLevel', 'department'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('staff_id', 'like', "%{$search}%");
                });
            })
            ->when($request->input('user_level_id'), function ($query, $userLevelId) {
                $query->where('user_level_id', $userLevelId);
            })
            ->when($request->input('status') !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('status'));
            });

        $users = $query->get();

        return \App\Exports\DepartmentUsersExport::download($users, $department);
    }
} 