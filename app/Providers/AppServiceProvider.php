<?php

namespace App\Providers;

use App\Contracts\PaddlePaymentProvider;
use App\Contracts\PaymentProviderManager;
use App\Services\ApiKeyCreationService;
use App\Services\ApiResultTools;
use App\Services\CustomAuditingService;
use App\Services\PaymentProcessingService;
use App\Services\ToastMessageService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Registering Services
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
            $auditing_service = new CustomAuditingService();
            $toast_service = new ToastMessageService();
            return new ApiKeyCreationService(new PaymentProviderManager($auditing_service, $toast_service));
        });

        $this->app->singleton(PaymentProcessingService::class, function ($app) {
            $auditing_service = new CustomAuditingService();
            $toast_service = new ToastMessageService();
            return new PaymentProcessingService(
                customAuditingService: $auditing_service,
                paymentProviderManager: new PaymentProviderManager($auditing_service, $toast_service)
            );
        });

        //Registering Managers
        $this->app->singleton(PaymentProviderManager::class, function ($app) {
            return new PaymentProviderManager(new CustomAuditingService(), new ToastMessageService());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Allowing only https traffic in production environments
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

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
