<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Leave\LeaveController;

use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Leave\LeaveApprovalController;

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
    Route::put('/leaves/update/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');
    Route::patch('/leaves/{leave}', [LeaveController::class, 'cancel'])->name('leaves.cancel');


    // Leave Approvals
   
        Route::get('/leave/approvals', [LeaveApprovalController::class, 'index'])->name('leave.approvals.index');
        Route::get('/leave/approvals/{leave}', [LeaveApprovalController::class, 'show'])->name('leave.approvals.show');
        Route::post('/leave/approvals/{leave}/approve', [LeaveApprovalController::class, 'approve'])->name('leave.approvals.approve');
        Route::post('/leave/approvals/{leave}/reject', action: [LeaveApprovalController::class, 'reject'])->name('leave.approvals.reject');
    

    // Leave Types
    Route::middleware(['can:manage-leave'])->group(function () {
        Route::get('/leave/types', [LeaveTypeController::class, 'index'])->name('leave.types.index');
        Route::get('/leave/types/create', [LeaveTypeController::class, 'create'])->name('leave.types.create');
        Route::post('/leave/types', [LeaveTypeController::class, 'store'])->name('leave.types.store');
        Route::get('/leave/types/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('leave.types.edit');
        Route::put('/leave/types/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave.types.update');
        Route::delete('/leave/types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('leave.types.destroy');
    });

    // Leave Balances

}); 