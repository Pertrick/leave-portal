<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'manage-leave',
            'view-reports',
            'manage-users',
            'manage-departments',
            'manage-supervisors',
            'manage-hods',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'admin' => $permissions,
            'leave-manager' => ['manage-leave'],
            'report-viewer' => ['view-reports'],
            'user-manager' => ['manage-users'],
            'employee' => [''],
        ];

        foreach ($roles as $role => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $role]);
            $role->givePermissionTo($rolePermissions);
        }
    }
} 