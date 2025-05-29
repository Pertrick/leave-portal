<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ContactHRController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\DepartmentHeadController;
use App\Http\Controllers\Admin\DepartmentRelationshipController;
use App\Http\Controllers\StaffController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', function () {
        return Inertia::render('Profile/Index', [
            'user' => auth()->user()->load([
                'department',
                'userLevel',
                'activeSupervisors.supervisor',
                'departmentHead.user',
                'leaveBalances.leaveType'
            ]),
        ]);
    })->name('profile');

    // Route::get('/pass', function(){
    //     dd(User::get());
    //     User::find(2)->update(['password' => bcrypt('12345678')]);
    // });

    Route::middleware(['can:manage-holidays'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('holidays', HolidayController::class);
        Route::post('holidays/{holiday}/toggle', [HolidayController::class, 'toggleStatus'])->name('holidays.toggle');
    });

    // Leave routes
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
});

// Admin Leave Applications
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/leave-applications', [App\Http\Controllers\Admin\LeaveApplicationController::class, 'index'])->name('leave.applications');
    Route::post('/leave-applications/{leave}/approve', [App\Http\Controllers\Admin\LeaveApplicationController::class, 'approve'])->name('leave.approve');
    Route::post('/leave-applications/{leave}/reject', [App\Http\Controllers\Admin\LeaveApplicationController::class, 'reject'])->name('leave.reject');
    Route::get('/leave-applications/export', [App\Http\Controllers\Admin\LeaveApplicationController::class, 'export'])->name('leave.export');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/leave.php';
