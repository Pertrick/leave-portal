<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role that has assigned users.');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users' => function ($query) {
            $query->select('users.id', 'firstname', 'lastname', 'email', 'staff_id');
        }]);

        return Inertia::render('Admin/Roles/Show', [
            'role' => $role,
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }
} 