<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\DepartmentHeadController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LeaveBalanceController;
use App\Http\Controllers\Admin\LeaveApplicationController;
use App\Http\Controllers\Admin\DepartmentRelationshipController;
use App\Http\Controllers\Admin\StaffReportController;
use App\Http\Controllers\NotificationController;

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['role_redirect'])->name('dashboard');

    // Dashboard API
     Route::get('/api/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);

    // Profile Routes
    Route::get('/profile', function () {
        $user = Auth::user();
        
        // Load relationships based on user role
        $relationships = [
            'department.activeHead.user',
            'userLevel',
            'leaveBalances.leaveType',
            'roles'
        ];
        
        // Only load supervisor relationships for non-admin users
        if (!$user->hasRole('admin')) {
            $relationships[] = 'activeSupervisors';
        }
        
        return Inertia::render('Profile/Index', [
            'user' => $user->load($relationships),
        ]);
    })->name('profile');

    // Route::get('/pass', function(){
    //     dd(User::get());
    //     User::find(2)->update(['password' => bcrypt('12345678')]);
    // });

    Route::middleware(['role:admin|hr'])->prefix('admin')->name('admin.')->group(function () {
        // Holidays
        Route::resource('holidays', HolidayController::class);
        Route::post('holidays/{holiday}/toggle', [HolidayController::class, 'toggleStatus'])->name('holidays.toggle');

        // Leave Applications
        Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index');
        Route::get('/leave-applications/{leave}', [LeaveApplicationController::class, 'show'])->name('leave-applications.show');
        Route::get('/leave-applications/export', [LeaveApplicationController::class, 'export'])->name('leave-applications.export');

        // Leave Reports
        Route::get('/leave/report', [App\Http\Controllers\Admin\LeaveReportController::class, 'index'])->name('leave.report');
        Route::get('/leave/export', [App\Http\Controllers\Admin\LeaveReportController::class, 'export'])->name('leave.export');

        // Department Management
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::put('/departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
        Route::get('/departments/{department}/users', [DepartmentController::class, 'showUsers'])->name('departments.users');
        Route::get('/departments/{department}/users/export', [DepartmentController::class, 'exportUsers'])->name('departments.users.export');

        // Leave Entitlement Management
        Route::get('/leave-entitlements', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'index'])->name('leave-entitlements.index');
        Route::post('/leave-entitlements', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'store'])->name('leave-entitlements.store');
        Route::put('/leave-entitlements/{entitlement}', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'update'])->name('leave-entitlements.update');
        Route::delete('/leave-entitlements/{entitlement}', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'destroy'])->name('leave-entitlements.destroy');
        Route::put('/leave-entitlements/{entitlement}/toggle-status', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'toggleStatus'])->name('leave-entitlements.toggle-status');
        Route::post('/leave-entitlements/bulk-update', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'bulkUpdate'])->name('leave-entitlements.bulk-update');
        Route::get('/leave-entitlements/user-level/{userLevel}/users', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'showUsersByLevel'])->name('leave-entitlements.users-by-level');

        // Dashboard
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->middleware(['role_redirect'])->name('dashboard');
        Route::get('/api/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index']);

    });
});

// Supervisor Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/supervisors', [SupervisorController::class, 'index'])->name('supervisors.index');
    Route::post('/users/{user}/supervisor', [SupervisorController::class, 'assign'])->name('supervisors.assign');
    Route::post('/supervisors/{supervisor}/deactivate', [SupervisorController::class, 'deactivate'])->name('supervisors.deactivate');

    // Department Head Management Routes
    Route::get('/department-heads', [DepartmentHeadController::class, 'index'])->name('department-heads.index');
    Route::post('/departments/{department}/head', [DepartmentHeadController::class, 'assign'])->name('department-heads.assign');
    Route::post('/departments/{department}/head/deactivate', [DepartmentHeadController::class, 'deactivate'])->name('department-heads.deactivate');

    // Department Relationships
    Route::get('/admin/departments/relationships', [DepartmentRelationshipController::class, 'index'])
        ->name('admin.departments.relationships');
    Route::get('/admin/departments/{department}/relationships', [DepartmentRelationshipController::class, 'show'])
        ->name('admin.departments.relationships.show');
    Route::post('/admin/departments/{department}/head', [DepartmentRelationshipController::class, 'assignHead'])
        ->name('admin.departments.head.assign');
    Route::delete('/admin/departments/{department}/head', [DepartmentRelationshipController::class, 'deactivateHead'])
        ->name('admin.departments.head.deactivate');
    Route::post('/admin/departments/{department}/supervisors', [DepartmentRelationshipController::class, 'assignSupervisor'])
        ->name('admin.departments.supervisors.assign');
    Route::delete('/admin/supervisors/{supervisor}', [DepartmentRelationshipController::class, 'deactivateSupervisor'])
        ->name('admin.supervisors.deactivate');
    Route::put('/admin/departments/{department}/head', [DepartmentRelationshipController::class, 'updateHead'])->name('admin.departments.head.update');

    // Staff Management Routes
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'list'])->name('list');
        Route::get('/export', [StaffController::class, 'export'])->name('export');
        Route::get('/account-requests', [StaffController::class, 'pendingLeaveAccounts'])->name('pending-leave-accounts');
        Route::get('/{user}', [StaffController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{user}', [StaffController::class, 'update'])->name('update');
        Route::put('/{user}/toggle-status', [StaffController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Staff Leave Balance Management
    Route::get('/staff/{staff}/leave-balances', [StaffController::class, 'leaveBalances'])
        ->name('staff.leave-balances');
    Route::post('/staff/{staff}/leave-balances', [StaffController::class, 'updateLeaveBalances'])
        ->name('staff.leave-balances.update');


        Route::middleware(['role:admin|hr|supervisor'])->group(function () {
            Route::get('/leave/balances', [LeaveBalanceController::class, 'index'])->name('leave.balances.index');
            Route::get('/leave/balances/export', [LeaveBalanceController::class, 'export'])->name('leave.balances.export');

              // Staff Report Routes
        Route::get('/staff-report', [StaffReportController::class, 'index'])->name('staff-report.index');
        Route::get('/staff-report/export', [StaffReportController::class, 'export'])->name('staff-report.export');
        });

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/leave.php';
