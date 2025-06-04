<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HolidayController;
use App\Http\Controllers\API\SupervisorController;
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

require __DIR__.'/auth.php'; 