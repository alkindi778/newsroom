<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Jobs\OptimizeMediaImage;
use Illuminate\Support\Facades\Log;
use App\Support\CustomPathGenerator;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use App\Repositories\Interfaces\NewspaperIssueRepositoryInterface;
use App\Repositories\NewspaperIssueRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // تسجيل Custom Path Generator للصور
        $this->app->bind(PathGenerator::class, CustomPathGenerator::class);
        
        // تسجيل Newspaper Issue Repository
        $this->app->bind(NewspaperIssueRepositoryInterface::class, NewspaperIssueRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // فرض HTTPS في Production أو عند تفعيل APP_FORCE_HTTPS
        if ($this->app->environment('production') || config('app.force_https')) {
            URL::forceScheme('https');
        }

        // الضغط يتم الآن في MediaLibraryController مباشرة بعد الرفع
        // لا حاجة لـ Event Listener هنا

        // جدولة نشر المنشورات المجدولة على وسائل التواصل الاجتماعي
        $this->app->booted(function () {
            $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
            $schedule->command('social-media:publish-scheduled')
                ->everyMinute()
                ->withoutOverlapping();
        });
    }
}
