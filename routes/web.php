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
use App\Http\Controllers\ContactSupportController;
use App\Http\Controllers\Admin\ContactSupportController as AdminContactSupportController;

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
        Route::resource('holidays', HolidayController::class)->middleware('permission:manage_holidays');
        Route::post('holidays/{holiday}/toggle', [HolidayController::class, 'toggleStatus'])->name('holidays.toggle')->middleware('permission:manage_holidays');

        // Leave Applications
        Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index')->middleware('permission:view_leaves');
        Route::get('/leave-applications/{leave}', [LeaveApplicationController::class, 'show'])->name('leave-applications.show')->middleware('permission:view_leaves');
        Route::get('/leave-applications/export', [LeaveApplicationController::class, 'export'])->name('leave-applications.export')->middleware('permission:export_reports');

        // Leave Reports
        Route::get('/leave/report', [App\Http\Controllers\Admin\LeaveReportController::class, 'index'])->name('leave.report')->middleware('permission:view_reports');
        Route::get('/leave/export', [App\Http\Controllers\Admin\LeaveReportController::class, 'export'])->name('leave.export')->middleware('permission:export_reports');

        // Department Management
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index')->middleware('permission:view_departments');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store')->middleware('permission:create_departments');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update')->middleware('permission:edit_departments');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy')->middleware('permission:delete_departments');
        Route::put('/departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status')->middleware('permission:edit_departments');
        Route::get('/departments/{department}/users', [DepartmentController::class, 'showUsers'])->name('departments.users')->middleware('permission:view_users');
        Route::get('/departments/{department}/users/export', [DepartmentController::class, 'exportUsers'])->name('departments.users.export')->middleware('permission:export_reports');

        // Leave Entitlement Management
        Route::get('/leave-entitlements', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'index'])->name('leave-entitlements.index')->middleware('permission:manage_leave_entitlements');
        Route::post('/leave-entitlements', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'store'])->name('leave-entitlements.store')->middleware('permission:manage_leave_entitlements');
        Route::put('/leave-entitlements/{entitlement}', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'update'])->name('leave-entitlements.update')->middleware('permission:manage_leave_entitlements');
        Route::delete('/leave-entitlements/{entitlement}', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'destroy'])->name('leave-entitlements.destroy')->middleware('permission:manage_leave_entitlements');
        Route::put('/leave-entitlements/{entitlement}/toggle-status', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'toggleStatus'])->name('leave-entitlements.toggle-status')->middleware('permission:manage_leave_entitlements');
        Route::post('/leave-entitlements/bulk-update', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'bulkUpdate'])->name('leave-entitlements.bulk-update')->middleware('permission:manage_leave_entitlements');
        Route::get('/leave-entitlements/user-level/{userLevel}/users', [App\Http\Controllers\Admin\LeaveEntitlementController::class, 'showUsersByLevel'])->name('leave-entitlements.users-by-level')->middleware('permission:view_users');

        // Roles and Permissions Management
        Route::resource('roles', App\Http\Controllers\Admin\RoleController::class)->middleware('permission:manage_roles');
        Route::resource('permissions', App\Http\Controllers\Admin\PermissionController::class)->middleware('permission:manage_permissions');
        Route::resource('user-roles', App\Http\Controllers\Admin\UserRoleController::class)->only(['index', 'show'])->middleware('permission:manage_user_roles');
        Route::put('/user-roles/{user}/roles', [App\Http\Controllers\Admin\UserRoleController::class, 'updateRoles'])->name('user-roles.update-roles')->middleware('permission:manage_user_roles');
        Route::put('/user-roles/{user}/permissions', [App\Http\Controllers\Admin\UserRoleController::class, 'updatePermissions'])->name('user-roles.update-permissions')->middleware('permission:manage_user_permissions');
        Route::post('/user-roles/{user}/assign-role', [App\Http\Controllers\Admin\UserRoleController::class, 'assignRole'])->name('user-roles.assign-role')->middleware('permission:manage_user_roles');
        Route::delete('/user-roles/{user}/remove-role', [App\Http\Controllers\Admin\UserRoleController::class, 'removeRole'])->name('user-roles.remove-role')->middleware('permission:manage_user_roles');
        Route::post('/user-roles/{user}/assign-permission', [App\Http\Controllers\Admin\UserRoleController::class, 'assignPermission'])->name('user-roles.assign-permission')->middleware('permission:manage_user_permissions');
        Route::delete('/user-roles/{user}/remove-permission', [App\Http\Controllers\Admin\UserRoleController::class, 'removePermission'])->name('user-roles.remove-permission')->middleware('permission:manage_user_permissions');

        // Contact Support Management
        Route::get('/contact-support', [AdminContactSupportController::class, 'index'])->name('contact-support.index')->middleware('permission:manage_support_requests');
        Route::get('/contact-support/{contactSupport}', [AdminContactSupportController::class, 'show'])->name('contact-support.show')->middleware('permission:manage_support_requests');
        Route::post('/contact-support/{contactSupport}/respond', [AdminContactSupportController::class, 'respond'])->name('contact-support.respond')->middleware('permission:manage_support_requests');
        Route::patch('/contact-support/{contactSupport}/status', [AdminContactSupportController::class, 'updateStatus'])->name('contact-support.update-status')->middleware('permission:manage_support_requests');
        Route::patch('/contact-support/{contactSupport}/priority', [AdminContactSupportController::class, 'updatePriority'])->name('contact-support.update-priority')->middleware('permission:manage_support_requests');
        Route::post('/contact-support/{contactSupport}/assign', [AdminContactSupportController::class, 'assign'])->name('contact-support.assign')->middleware('permission:manage_support_requests');
        Route::delete('/contact-support/{contactSupport}/close', [AdminContactSupportController::class, 'close'])->name('contact-support.close')->middleware('permission:manage_support_requests');
        Route::patch('/contact-support/{contactSupport}/reopen', [AdminContactSupportController::class, 'reopen'])->name('contact-support.reopen')->middleware('permission:manage_support_requests');
        Route::delete('/contact-support/{contactSupport}', [AdminContactSupportController::class, 'destroy'])->name('contact-support.destroy')->middleware('permission:manage_support_requests');

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


    // Contact Support Routes
    Route::prefix('contact-support')->name('contact-support.')->group(function () {
        Route::get('/', [ContactSupportController::class, 'index'])->name('index');
        Route::get('/create', [ContactSupportController::class, 'create'])->name('create');
        Route::post('/', [ContactSupportController::class, 'store'])->name('store');
        Route::get('/{contactSupport}', [ContactSupportController::class, 'show'])->name('show');
        Route::get('/{contactSupport}/edit', [ContactSupportController::class, 'edit'])->name('edit');
        Route::put('/{contactSupport}', [ContactSupportController::class, 'update'])->name('update');
        Route::delete('/{contactSupport}', [ContactSupportController::class, 'destroy'])->name('destroy');
    });
});



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/leave.php';
