<?php

namespace App\Providers;

use App\Services\ApiKeyCreationService;
use App\Services\ApiResultTools;
use App\Services\CustomAuditingService;
use App\Services\PaymentProcessingService;
use App\Services\ToastMessageService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ApiResultTools::class, function ($app) {
            return new ApiResultTools();
        });

        $this->app->singleton(ToastMessageService::class, function ($app) {
            return new ToastMessageService();
        });

        $this->app->singleton(CustomAuditingService::class, function ($app) {
            return new CustomAuditingService();
        });

        $this->app->singleton(ApiKeyCreationService::class, function ($app) {
            return new ApiKeyCreationService();
        });

        $this->app->singleton(PaymentProcessingService::class, function ($app) {
            return new PaymentProcessingService(new CustomAuditingService());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Rate limiting dashboard loading
        RateLimiter::for('dashboard-page-loads', function (Request $request) {
            return [
                Limit::perSecond(12)->by($request->user()->id),
            ];
        });

        //Rate limiting manage lists page loading
        RateLimiter::for('manage-list-page-loads', function (Request $request) {
            return [
                Limit::perSecond(6)->by($request->user()->id),
            ];
        });

        //Rate limiting other page loadings
        RateLimiter::for('other-page-loads', function (Request $request) {
            return [
                Limit::perSecond(2)->by($request->user()->id),
            ];
        });

        //Rate limiting for refreshing API token
        RateLimiter::for('refresh-token-throttling', function (Request $request) {
            return [
                Limit::perMinute(2)->by($request->user()->id),
            ];
        });

        //Rate limiting post and put requests
        RateLimiter::for('post-request-throttling', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->user()->id),
            ];
        });
    }
}
