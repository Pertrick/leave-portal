<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HolidayController;
use App\Http\Controllers\API\SupervisorController;
use App\Http\Controllers\Api\NotificationController;
use Carbon\Carbon;
use App\Models\Holiday;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/holidays', [HolidayController::class, 'getHolidays'])->name('api.holidays');
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});

Route::get('/supervisors/{supervisor}/users', [SupervisorController::class, 'getSupervisedUsers']);
Route::get('/range/holidays', [HolidayController::class, 'getHolidaysInRange'])->name('api.inrange.holidays');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware', 'inertia')
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Notification routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications/delete-all', [NotificationController::class, 'deleteAll']);
});

require __DIR__.'/auth.php'; 