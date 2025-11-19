<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\SmartSummaryRepository;
use App\Services\CacheService;

class SmartSummaryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // تسجيل Repository
        $this->app->bind(SmartSummaryRepository::class, function ($app) {
            return new SmartSummaryRepository();
        });

        // تسجيل CacheService مع Dependency Injection
        $this->app->bind(CacheService::class, function ($app) {
            return new CacheService($app->make(SmartSummaryRepository::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
