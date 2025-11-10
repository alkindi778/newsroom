<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Jobs\OptimizeMediaImage;
use Illuminate\Support\Facades\Log;
use App\Support\CustomPathGenerator;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // تسجيل Custom Path Generator للصور
        $this->app->bind(PathGenerator::class, CustomPathGenerator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // الضغط يتم الآن في MediaLibraryController مباشرة بعد الرفع
        // لا حاجة لـ Event Listener هنا
    }
}
