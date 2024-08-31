<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlacklistedWordController;
use \App\Http\Controllers\WhitelistedWordController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION, 99
    ]);
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/chart-profanity-frequency', [\App\Http\Controllers\DashboardController::class, 'loadProfanityFrequencyChart'])->name('load-profanity-frequency-chart');
    Route::get('/chart-profanity-category', [\App\Http\Controllers\DashboardController::class, 'loadProfanityCategoryChart'])->name('load-profanity-category-chart');
    Route::get('/load-chart-filters', [\App\Http\Controllers\DashboardController::class, 'loadChartFilters'])->name('load-chart-filters');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/manage_list', function () {
        return Inertia::render('ManageLists');
    })->name('manage_list');

    Route::post('/add_word_to_blacklist', [BlacklistedWordController::class, 'store'])->name('add_word_to_blacklist');
    Route::get('/get_blacklisted_words', [BlacklistedWordController::class, 'index'])->name('get_blacklisted_words');
    Route::put('/change_state_blacklist/{blacklistedWord}', [BlacklistedWordController::class, 'update'])->name('change_state_blacklist');


    Route::post('/add_word_to_whitelist', [WhitelistedWordController::class, 'store'])->name('add_word_to_whitelist');
    Route::get('/get_whitelisted_words', [WhitelistedWordController::class, 'index'])->name('get_whitelisted_words');
    Route::put('/change_state_whitelist/{whitelistedWord}', [WhitelistedWordController::class, 'update'])->name('change_state_whitelist');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/get_pricing_structure', [App\Http\Controllers\PaymentController::class, 'get_pricing_structure'])->name('get-pricing-structure');

    Route::get('/payments', [\App\Http\Controllers\PaymentController::class, 'load_manage_payments_page'])->name('payments');

    Route::post('/card-saved-successfully', [\App\Http\Controllers\PaymentController::class, 'card_saved_successfully'])->name('card-saved-successfully');
    Route::post('/card-saved-failed', [\App\Http\Controllers\PaymentController::class, 'card_saved_failed'])->name('card-saved-failed');

    Route::get('/load-payment-details-page', [\App\Http\Controllers\PaymentController::class, 'load_payment_details_page'])->name('load-payment-details-page');
    Route::get('/load-payment-details-update-page', [\App\Http\Controllers\PaymentController::class, 'load_payment_details_update_page'])->name('load-payment-details-update-page');

    Route::get('/show_payment_method_removal_popup', [\App\Http\Controllers\PaymentController::class, 'show_payment_method_removal_popup'])->name('show-payment-method-removal-popup');
    Route::post('/remove_payment_method', [\App\Http\Controllers\PaymentController::class, 'remove_payment_method'])->name('remove-payment-method');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile', [ProfileController::class, 'refresh_token'])->name('profile.refresh-token');
});

Route::get('/google-auth/redirect', [\App\Http\Controllers\GoogleAuthController::class, 'redirect'])->name("google.redirect");
Route::get('/google-auth/callback', [\App\Http\Controllers\GoogleAuthController::class, 'callback'])->name("google.callback");

require __DIR__ . '/auth.php';
