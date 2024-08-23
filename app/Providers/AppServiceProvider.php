<?php

namespace App\Providers;

use App\Services\ApiResultTools;
use App\Services\CostAndUsageCalculationService;
use App\Services\CustomAuditingService;
use App\Services\ToastMessageService;
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

        $this->app->singleton(CostAndUsageCalculationService::class, function ($app) {
            return new CostAndUsageCalculationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
