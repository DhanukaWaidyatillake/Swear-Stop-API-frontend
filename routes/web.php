<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,99
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/manage_list', function () {
    return Inertia::render('ManageLists');
})->middleware(['auth', 'verified'])->name('manage_list');

Route::get('/payments', function () {
    return Inertia::render('ManagePayments');
})->middleware(['auth', 'verified'])->name('payments');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile', [ProfileController::class, 'refresh_token'])->name('profile.refresh-token');
});

Route::get('/google-auth/redirect', [\App\Http\Controllers\GoogleAuthController::class, 'redirect'])->name("google.redirect");
Route::get('/google-auth/callback', [\App\Http\Controllers\GoogleAuthController::class, 'callback'])->name("google.callback");

require __DIR__.'/auth.php';
