<?php

use App\Http\Controllers\BlacklistedWordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WhitelistedWordController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//Welcome page
Route::middleware([])->group(function () {
    Route::get('/', [\App\Http\Controllers\WelcomePageController::class, 'loadWelcomePage'])->name('load-welcome-page');
    Route::get('/privacy-policy', [\App\Http\Controllers\WelcomePageController::class, 'loadWelcomePage'])->name('load-welcome-page');
    Route::get('/terms-of-use', [\App\Http\Controllers\WelcomePageController::class, 'loadWelcomePage'])->name('load-welcome-page');
    Route::get('/show-pricing', [\App\Http\Controllers\WelcomePageController::class, 'loadWelcomePage'])->name('load-welcome-page');
    Route::get('/api-tester-get-sentence', [\App\Http\Controllers\WelcomePageController::class, 'getRandomSentence'])->name('get-random-sentence');
    Route::get('/calculate-monthly-cost', [\App\Http\Controllers\WelcomePageController::class, 'getMonthlyCost'])->name('calculate-monthly-cost');
    Route::get('/get_pricing_structure', [App\Http\Controllers\PaymentController::class, 'get_pricing_structure'])->name('get-pricing-structure');
});

//Dashboard
Route::middleware(['auth', 'verified', 'throttle:dashboard-page-loads'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/chart-profanity-frequency', [\App\Http\Controllers\DashboardController::class, 'loadProfanityFrequencyChart'])->name('load-profanity-frequency-chart');
    Route::get('/chart-profanity-category', [\App\Http\Controllers\DashboardController::class, 'loadProfanityCategoryChart'])->name('load-profanity-category-chart');
    Route::get('/load-chart-filters', [\App\Http\Controllers\DashboardController::class, 'loadChartFilters'])->name('load-chart-filters');
    Route::get('/load-profanity-history', [\App\Http\Controllers\DashboardController::class, 'loadProfanityFilterHistory'])->name('load-profanity-history');
    Route::get('/load-request-details-popup', [\App\Http\Controllers\DashboardController::class, 'loadRequestDetailsPopup'])->name('load-request-details-popup');
});

//Manage Lists
Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware(['throttle:manage-list-page-loads'])->group(function () {
        Route::get('/manage_list', function () {
            return Inertia::render('ManageLists');
        })->name('manage_list');
        Route::get('/get_blacklisted_words', [BlacklistedWordController::class, 'index'])->name('get_blacklisted_words');
        Route::get('/get_whitelisted_words', [WhitelistedWordController::class, 'index'])->name('get_whitelisted_words');
    });

    Route::middleware(['throttle:post-request-throttling'])->group(function () {
        Route::post('/add_word_to_blacklist', [BlacklistedWordController::class, 'store'])->name('add_word_to_blacklist');
        Route::post('/add_word_to_whitelist', [WhitelistedWordController::class, 'store'])->name('add_word_to_whitelist');

        Route::put('/change_state_blacklist/{blacklistedWord}', [BlacklistedWordController::class, 'update'])->name('change_state_blacklist');
        Route::put('/change_state_whitelist/{whitelistedWord}', [WhitelistedWordController::class, 'update'])->name('change_state_whitelist');
    });
});

//Payment page
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('throttle:other-page-loads')
        ->get('/payments', [\App\Http\Controllers\PaymentController::class, 'load_manage_payments_page'])->name('payments');

    Route::middleware(['throttle:post-request-throttling'])->group(function () {
        Route::post('/card-saved-successfully', [\App\Http\Controllers\PaymentController::class, 'card_saved_successfully'])->name('card-saved-successfully');
        Route::post('/card-saved-failed', [\App\Http\Controllers\PaymentController::class, 'card_saved_failed'])->name('card-saved-failed');
        Route::post('/remove_payment_method', [\App\Http\Controllers\PaymentController::class, 'remove_payment_method'])->name('remove-payment-method');
    });

    Route::get('/save-card', [\App\Http\Controllers\PaymentController::class, 'load_payment_details_page'])->name('save-card');
    Route::get('/update-card', [\App\Http\Controllers\PaymentController::class, 'load_payment_details_update_page'])->name('update-card');

    Route::get('/show_payment_method_removal_popup', [\App\Http\Controllers\PaymentController::class, 'show_payment_method_removal_popup'])->name('show-payment-method-removal-popup');
});

//Profile
Route::middleware('auth')->group(function () {
    Route::middleware('throttle:other-page-loads')
        ->get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::middleware('throttle:post-request-throttling')->patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('throttle:refresh-token-throttling')->post('/profile', [ProfileController::class, 'refresh_token'])->name('profile.refresh-token');
});

//Google Auth webhooks
Route::get('/google-auth/redirect', [\App\Http\Controllers\GoogleAuthController::class, 'redirect'])->name("google.redirect");
Route::get('/google-auth/callback', [\App\Http\Controllers\GoogleAuthController::class, 'callback'])->name("google.callback");

require __DIR__ . '/auth.php';
