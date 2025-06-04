<?php

namespace Database\Seeders;

use App\Models\Navigation;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        Navigation::truncate();

        // Dashboard
        $dashboard = Navigation::create([
            'path' =>  'dashboard',
            'title' => 'Dashboard',
            'roles' => ['admin', 'supervisor', 'hod', 'hr', 'employee'],
            'icon' => 'home',
            'order' => 1,
            'is_dropdown' => false
        ]);

        // Leave Management
        $leave = Navigation::create([
            'path' => null,
            'title' => 'Leave Management',
            'roles' => ['admin', 'supervisor', 'hod', 'hr', 'employee'],
            'icon' => 'calendar',
            'order' => 2,
            'is_dropdown' => true
        ]);

        // Leave Sub-items
        Navigation::create([
            'path' => 'leaves',
            'title' => 'My Applications',
            'parent_id' => $leave->id,
            'roles' => ['supervisor', 'hod', 'hr', 'employee'],
            'icon' => 'document-text',
            'order' => 1,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'leaves/create',
            'title' => 'New Application',
            'parent_id' => $leave->id,
            'roles' => ['supervisor', 'hod', 'hr', 'employee'],
            'icon' => 'plus-circle',
            'order' => 2,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'leaves/drafts',
            'title' => 'Drafts',
            'parent_id' => $leave->id,
            'roles' => ['supervisor', 'hod', 'hr', 'employee'],
            'icon' => 'document-duplicate',
            'order' => 3,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'leave/approvals',
            'title' => 'Leave Approvals',
            'parent_id' => $leave->id,
            'roles' => ['supervisor', 'hod', 'hr'],
            'icon' => 'check-circle',
            'order' => 4,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'leave/types',
            'title' => 'Leave Types',
            'parent_id' => $leave->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'tag',
            'order' => 5,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'leave/balances',
            'title' => 'Leave Balances',
            'parent_id' => $leave->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'calculator',
            'order' => 6,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'admin/holidays',
            'title' => 'Holiday Management',
            'parent_id' => $leave->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'calendar-days',
            'order' => 7,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'admin/leave-applications',
            'title' => 'Leave Applications',
            'parent_id' => $leave->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'clipboard-document-list',
            'order' => 8,
            'is_dropdown' => false
        ]);

        // Staff Management
        $staff = Navigation::create([
            'path' => 'staff',
            'title' => 'Staff Management',
            'roles' => ['admin','hr'],
            'icon' => 'users',
            'is_dropdown' => true
        ]);

        // Staff Sub-items
        Navigation::create([
            'path' => 'staff',
            'title' => 'Staff List',
            'parent_id' => $staff->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'list',
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'register',
            'title' => 'Register Staff',
            'parent_id' => $staff->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'user-plus',
            'order' => 2,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'admin/departments/relationships',
            'title' => 'Assign Supervisors/HODs',
            'parent_id' => $staff->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'users',
            'order' => 3,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'staff/account-requests',
            'title' => 'Accounts Request',
            'parent_id' => $staff->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'user',
            'order' => 1,
            'is_dropdown' => false
        ]);

        // Departments
        // Navigation::create([
        //     'path' => 'departments',
        //     'title' => 'Departments',
        //     'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
        //     'icon' => 'building',
        //     'order' => 4,
        //     'is_dropdown' => false
        // ]);

        // Reports
        $reports = Navigation::create([
            'path' => 'reports',
            'title' => 'Reports',
            'roles' => ['admin', 'supervisor', 'hod', 'hr'],
            'icon' => 'bar-chart',
            'order' => 5,
            'is_dropdown' => true
        ]);

        // Reports Sub-items
        Navigation::create([
            'path' => 'admin/leave/report',
            'title' => 'Leave Reports',
            'parent_id' => $reports->id,
            'roles' => ['admin', 'hr'],
            'icon' => 'calendar',
            'order' => 1,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'staff-report',
            'title' => 'Staff Reports',
            'parent_id' => $reports->id,
            'roles' => ['admin', 'supervisor', 'hod', 'hr'],
            'icon' => 'users',
            'order' => 2,
            'is_dropdown' => false
        ]);

        // Settings
        Navigation::create([
            'path' => 'settings',
            'title' => 'Settings',
            'roles' => ['admin', 'employee','supervisor', 'hod', 'hr'],
            'icon' => 'settings',
            'order' => 6,
            'is_dropdown' => false
        ]);

        // Profile
        Navigation::create([
            'path' => 'profile',
            'title' => 'Profile',
            'roles' => ['admin','employee', 'supervisor', 'hod', 'hr'],
            'icon' => 'user',
            'order' => 7,
            'is_dropdown' => false
        ]);

        // New additions
        Navigation::create([
            'path' => 'leave-entitlements',
            'title' => 'Leave Entitlements',
            'roles' => ['admin'],
            'icon' => 'calendar',
            'parent_id' => $leave->id,
            'order' => 7,
            'is_dropdown' => false,
        ]);

    }
} 