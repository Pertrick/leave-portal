<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ContactHRController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/contact-hr', [ContactHRController::class, 'show'])->name('contact.hr');
Route::post('/contact-hr', [ContactHRController::class, 'submit'])->name('contact.hr.submit');

// Route::get('/pass', function(){
//     User::find(2)->update(['password' => bcrypt('12345678')]);
// });


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
