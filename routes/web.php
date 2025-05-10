<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\ContactHRController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/contact-hr', [ContactHRController::class, 'show'])->name('contact.hr');
Route::post('/contact-hr', [ContactHRController::class, 'submit'])->name('contact.hr.submit');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
