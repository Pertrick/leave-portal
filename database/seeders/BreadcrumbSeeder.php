<?php

namespace Database\Seeders;

use App\Models\Breadcrumb;
use Illuminate\Database\Seeder;

class BreadcrumbSeeder extends Seeder
{
    public function run(): void
    {
        // Dashboard
        $dashboard = Breadcrumb::create([
            'path' => 'dashboard',
            'title' => 'Dashboard',
            'roles' => ['admin', 'manager', 'staff'],
            'icon' => 'home',
            'order' => 1
        ]);

        // Leave Management
        $leave = Breadcrumb::create([
            'path' => 'leave',
            'title' => 'Leave Management',
            'roles' => ['admin', 'manager', 'staff'],
            'icon' => 'calendar',
            'order' => 2
        ]);

        // Leave Sub-items
        Breadcrumb::create([
            'path' => 'apply',
            'title' => 'Apply Leave',
            'parent_id' => $leave->id,
            'roles' => ['admin', 'manager', 'staff'],
            'icon' => 'file-plus',
            'order' => 1
        ]);

        Breadcrumb::create([
            'path' => 'history',
            'title' => 'Leave History',
            'parent_id' => $leave->id,
            'roles' => ['admin', 'manager', 'staff'],
            'icon' => 'history',
            'order' => 2
        ]);

        Breadcrumb::create([
            'path' => 'approvals',
            'title' => 'Leave Approvals',
            'parent_id' => $leave->id,
            'roles' => ['admin', 'manager'],
            'icon' => 'check-circle',
            'order' => 3
        ]);

        // Staff Management
        $staff = Breadcrumb::create([
            'path' => 'staff',
            'title' => 'Staff Management',
            'roles' => ['admin'],
            'icon' => 'users',
            'order' => 3
        ]);

        // Staff Sub-items
        Breadcrumb::create([
            'path' => 'list',
            'title' => 'Staff List',
            'parent_id' => $staff->id,
            'roles' => ['admin'],
            'icon' => 'list',
            'order' => 1
        ]);

        Breadcrumb::create([
            'path' => 'register',
            'title' => 'Register Staff',
            'parent_id' => $staff->id,
            'roles' => ['admin'],
            'icon' => 'user-plus',
            'order' => 2
        ]);

        // Departments
        Breadcrumb::create([
            'path' => 'departments',
            'title' => 'Departments',
            'roles' => ['admin'],
            'icon' => 'building',
            'order' => 4
        ]);

        // Reports
        $reports = Breadcrumb::create([
            'path' => 'reports',
            'title' => 'Reports',
            'roles' => ['admin', 'manager'],
            'icon' => 'bar-chart',
            'order' => 5
        ]);

        // Reports Sub-items
        Breadcrumb::create([
            'path' => 'leave',
            'title' => 'Leave Reports',
            'parent_id' => $reports->id,
            'roles' => ['admin', 'manager'],
            'icon' => 'calendar',
            'order' => 1
        ]);

        Breadcrumb::create([
            'path' => 'staff',
            'title' => 'Staff Reports',
            'parent_id' => $reports->id,
            'roles' => ['admin'],
            'icon' => 'users',
            'order' => 2
        ]);

        // Settings
        Breadcrumb::create([
            'path' => 'settings',
            'title' => 'Settings',
            'roles' => ['admin'],
            'icon' => 'settings',
            'order' => 6
        ]);

        // Profile
        Breadcrumb::create([
            'path' => 'profile',
            'title' => 'Profile',
            'roles' => ['admin', 'manager', 'staff'],
            'icon' => 'user',
            'order' => 7
        ]);
    }
} 