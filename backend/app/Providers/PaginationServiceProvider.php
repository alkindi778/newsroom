<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class PaginationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // استخدام التصميم المخصص للـ pagination
        Paginator::defaultView('components.admin.pagination');
        Paginator::defaultSimpleView('components.admin.simple-pagination');
    }
}
