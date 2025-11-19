<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class SmartSummaryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // تسجيل الخدمات فقط بعد boot لضمان وجود قاعدة البيانات
        $this->app->booted(function () {
            try {
                // التحقق من وجود الجدول قبل تسجيل الخدمات
                if (Schema::hasTable('smart_summaries')) {
                    // تسجيل Repository
                    $this->app->bind(\App\Repositories\SmartSummaryRepository::class, function ($app) {
                        return new \App\Repositories\SmartSummaryRepository();
                    });

                    // تسجيل CacheService مع Dependency Injection
                    $this->app->bind(\App\Services\CacheService::class, function ($app) {
                        return new \App\Services\CacheService($app->make(\App\Repositories\SmartSummaryRepository::class));
                    });
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء أثناء التطوير/Migration
                \Log::info('SmartSummaryServiceProvider: Table not ready yet - ' . $e->getMessage());
            }
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
