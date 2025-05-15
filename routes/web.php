<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ContactHRController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\LeaveController;

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
            'user' => auth()->user()
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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/leave.php';
