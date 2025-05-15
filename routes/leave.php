<?php

use App\Http\Controllers\Leave\LeaveController;
use App\Http\Controllers\Leave\LeaveApprovalController;
use App\Http\Controllers\Leave\LeaveTypeController;
use App\Http\Controllers\Leave\LeaveBalanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Leave Applications
    Route::post('/leaves/calculate-duration', [LeaveController::class, 'calculateDuration'])->name('leaves.calculate-duration');
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/drafts', [LeaveController::class, 'drafts'])->name('leaves.drafts');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    Route::post('/leaves/draft/{leave?}', [LeaveController::class, 'saveDraft'])->name('leaves.draft');
    Route::get('/leaves/{leave}/edit', [LeaveController::class, 'edit'])->name('leaves.edit');
    Route::put('/leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');


    // Leave Approvals
    Route::middleware(['can:approve-leaves'])->group(function () {
        Route::get('/leave/approvals', [LeaveApprovalController::class, 'index'])->name('leave.approvals.index');
        Route::get('/leave/approvals/{leave}', [LeaveApprovalController::class, 'show'])->name('leave.approvals.show');
        Route::post('/leave/approvals/{leave}/approve', [LeaveApprovalController::class, 'approve'])->name('leave.approvals.approve');
        Route::post('/leave/approvals/{leave}/reject', [LeaveApprovalController::class, 'reject'])->name('leave.approvals.reject');
    });

    // Leave Types
    Route::middleware(['can:manage-leave-types'])->group(function () {
        Route::get('/leave/types', [LeaveTypeController::class, 'index'])->name('leave.types.index');
        Route::get('/leave/types/create', [LeaveTypeController::class, 'create'])->name('leave.types.create');
        Route::post('/leave/types', [LeaveTypeController::class, 'store'])->name('leave.types.store');
        Route::get('/leave/types/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('leave.types.edit');
        Route::put('/leave/types/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave.types.update');
        Route::delete('/leave/types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('leave.types.destroy');
    });

    // Leave Balances
    Route::middleware(['can:manage-leave-balances'])->group(function () {
        Route::get('/leave/balances', [LeaveBalanceController::class, 'index'])->name('leave.balances.index');
        Route::get('/leave/balances/create', [LeaveBalanceController::class, 'create'])->name('leave.balances.create');
        Route::post('/leave/balances', [LeaveBalanceController::class, 'store'])->name('leave.balances.store');
        Route::get('/leave/balances/{leaveBalance}/edit', [LeaveBalanceController::class, 'edit'])->name('leave.balances.edit');
        Route::put('/leave/balances/{leaveBalance}', [LeaveBalanceController::class, 'update'])->name('leave.balances.update');
        Route::delete('/leave/balances/{leaveBalance}', [LeaveBalanceController::class, 'destroy'])->name('leave.balances.destroy');
    });
}); 