<?php

namespace Database\Seeders;

use App\Models\Navigation;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        // Dashboard
        $dashboard = Navigation::create([
            'path' => 'dashboard',
            'title' => 'Dashboard',
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
            'icon' => 'home',
            'order' => 1,
            'is_dropdown' => false
        ]);

        // Leave Management
        $leave = Navigation::create([
            'path' => null,
            'title' => 'Leave Management',
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
            'icon' => 'calendar',
            'order' => 2,
            'is_dropdown' => true
        ]);

        // Leave Sub-items
        Navigation::create([
            'path' => 'leaves',
            'title' => 'My Applications',
            'parent_id' => $leave->id,
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
            'icon' => 'document-text',
            'order' => 1,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'leave/approvals',
            'title' => 'Leave Approvals',
            'parent_id' => $leave->id,
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR'],
            'icon' => 'check-circle',
            'order' => 2,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'leave/types',
            'title' => 'Leave Types',
            'parent_id' => $leave->id,
            'roles' => ['Admin', 'HR'],
            'icon' => 'tag',
            'order' => 3,
            'is_dropdown' =>false
        ]);

        Navigation::create([
            'path' => 'leave/balances',
            'title' => 'Leave Balances',
            'parent_id' => $leave->id,
            'roles' => ['Admin', 'HR'],
            'icon' => 'calculator',
            'order' => 4,
            'is_dropdown' => false
        ]);

        // Staff Management
        $staff = Navigation::create([
            'path' => 'staff',
            'title' => 'Staff Management',
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
            'icon' => 'users',
            'order' => 3,
            'is_dropdown' => false
        ]);

        // Staff Sub-items
        Navigation::create([
            'path' => 'list',
            'title' => 'Staff List',
            'parent_id' => $staff->id,
            'roles' => ['Admin', 'HR'],
            'icon' => 'list',
            'order' => 1,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'register',
            'title' => 'Register Staff',
            'parent_id' => $staff->id,
            'roles' => ['Admin', 'HR'],
            'icon' => 'user-plus',
            'order' => 2,
            'is_dropdown' => false
        ]);

        // Departments
        Navigation::create([
            'path' => 'departments',
            'title' => 'Departments',
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
            'icon' => 'building',
            'order' => 4,
            'is_dropdown' => false
        ]);

        // Reports
        $reports = Navigation::create([
            'path' => 'reports',
            'title' => 'Reports',
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR'],
            'icon' => 'bar-chart',
            'order' => 5,
            'is_dropdown' => false
        ]);

        // Reports Sub-items
        Navigation::create([
            'path' => 'leave',
            'title' => 'Leave Reports',
            'parent_id' => $reports->id,
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
            'icon' => 'calendar',
            'order' => 1,
            'is_dropdown' => false
        ]);

        Navigation::create([
            'path' => 'staff',
            'title' => 'Staff Reports',
            'parent_id' => $reports->id,
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR'],
            'icon' => 'users',
            'order' => 2,
            'is_dropdown' => false
        ]);

        // Settings
        Navigation::create([
            'path' => 'settings',
            'title' => 'Settings',
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
            'icon' => 'settings',
            'order' => 6,
            'is_dropdown' => false
        ]);

        // Profile
        Navigation::create([
            'path' => 'profile',
            'title' => 'Profile',
            'roles' => ['Admin', 'Supervisor', 'HOD', 'HR', 'Employee'],
            'icon' => 'user',
            'order' => 7,
            'is_dropdown' => false
        ]);
    }
} 