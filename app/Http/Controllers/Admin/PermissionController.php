<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('roles')
            ->withCount('roles')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Permission created successfully.');
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete permission that is assigned to roles.');
        }

        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }

    public function show(Permission $permission)
    {
        $permission->load(['roles' => function ($query) {
            $query->withCount('users');
        }]);

        return Inertia::render('Admin/Permissions/Show', [
            'permission' => $permission,
        ]);
    }
} 