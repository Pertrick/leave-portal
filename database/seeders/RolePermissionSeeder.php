<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permissions with consistent snake_case naming
        $permissions = [
            // Leave management
            'view_leaves',
            'create_leaves',
            'edit_leaves',
            'delete_leaves',
            'approve_leaves',
            'reject_leaves',
            'cancel_leaves',
            'manage_leave_types',
            'manage_leave_entitlements',
            'manage_leave_balances',
            
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_user_roles',
            'manage_user_permissions',
            
            // Department management
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',
            'manage_department_heads',
            'manage_supervisors',
            
            // Reports and analytics
            'view_reports',
            'export_reports',
            'view_analytics',
            
            // System management
            'manage_holidays',
            'manage_settings',
            'manage_roles',
            'manage_permissions',
            'view_audit_logs',
            
            // Profile management
            'edit_own_profile',
            'view_own_leave_balance',
            
            // Support management
            'manage_support_requests',
            'view_support_requests',
            'create_support_requests',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'admin' => $permissions, // Admin gets all permissions
            'hr' => [
                'view_users', 'create_users', 'edit_users', 'manage_user_roles',
                'view_departments', 'create_departments', 'edit_departments',
                'manage_leave_types', 'manage_leave_entitlements', 'manage_leave_balances',
                'view_reports', 'export_reports', 'manage_holidays',
                'view_leaves', 'approve_leaves', 'reject_leaves',
            ],
            'hod' => [
                'view_departments', 'manage_department_heads', 'manage_supervisors',
                'view_users', 'view_reports', 'export_reports',
                'view_leaves', 'approve_leaves', 'reject_leaves',
                'view_own_leave_balance',
            ],
            'supervisor' => [
                'view_leaves', 'approve_leaves', 'reject_leaves',
                'view_reports', 'view_own_leave_balance',
            ],
            'employee' => [
                'view_leaves', 'create_leaves', 'edit_leaves', 'cancel_leaves',
                'edit_own_profile', 'view_own_leave_balance',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
} 