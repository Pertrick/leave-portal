<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'approve_leaves',
            'manage_users',
            'manage_leave_types',
            'manage_holidays',
            'view_reports'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo($permissions);

        $hodRole = Role::create(['name' => 'HOD']);
        $hodRole->givePermissionTo(['approve_leaves', 'view_reports']);

        $supervisorRole = Role::create(['name' => 'Supervisor']);
        $supervisorRole->givePermissionTo(['approve_leaves']);

        $hrRole = Role::create(['name' => 'HR']);
        $hrRole->givePermissionTo(['manage_users', 'manage_leave_types', 'manage_holidays']);

        $employeeRole = Role::create(['name' => 'Employee']);
    }
} 