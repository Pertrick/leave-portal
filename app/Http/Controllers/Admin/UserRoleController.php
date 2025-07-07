<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'permissions', 'department'])
            ->withCount('roles')
            ->orderBy('firstname')
            ->paginate(15);

        return Inertia::render('Admin/UserRoles/Index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function show(User $user)
    {
        $user->load(['roles', 'permissions', 'department']);

        return Inertia::render('Admin/UserRoles/Show', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->syncRoles($request->roles);

        return redirect()->back()->with('success', 'User roles updated successfully.');
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $user->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'User permissions updated successfully.');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $role = Role::find($request->role_id);
        $user->assignRole($role);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

    public function removeRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $role = Role::find($request->role_id);
        $user->removeRole($role);

        return redirect()->back()->with('success', 'Role removed successfully.');
    }

    public function assignPermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => ['required', 'exists:permissions,id'],
        ]);

        $permission = Permission::find($request->permission_id);
        $user->givePermissionTo($permission);

        return redirect()->back()->with('success', 'Permission assigned successfully.');
    }

    public function removePermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => ['required', 'exists:permissions,id'],
        ]);

        $permission = Permission::find($request->permission_id);
        $user->revokePermissionTo($permission);

        return redirect()->back()->with('success', 'Permission removed successfully.');
    }
} 