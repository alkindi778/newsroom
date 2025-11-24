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
use App\Models\Article;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\Video;
use App\Models\Writer;
use App\Models\Opinion;
use App\Models\ContactMessage;
use App\Observers\ArticleObserver;
use App\Observers\CategoryObserver;
use App\Observers\HomepageSectionObserver;
use App\Observers\VideoObserver;
use App\Observers\WriterObserver;
use App\Observers\OpinionObserver;
use App\Observers\ContactMessageObserver;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

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
        // تحديث اسم التطبيق من الإعدادات
        if (Schema::hasTable('site_settings')) {
            try {
                $siteTitle = SiteSetting::where('key', 'site_title')->value('value');
                if ($siteTitle) {
                    Config::set('app.name', $siteTitle);
                    Config::set('mail.from.name', $siteTitle);
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء في حالة عدم اكتمال التثبيت
            }
        }

        // تسجيل Article Observer للترجمة التلقائية
        Article::observe(ArticleObserver::class);
        
        // تسجيل Category Observer للترجمة التلقائية
        Category::observe(CategoryObserver::class);
        
        // تسجيل HomepageSection Observer للترجمة التلقائية
        HomepageSection::observe(HomepageSectionObserver::class);
        
        // تسجيل Video Observer للترجمة التلقائية
        Video::observe(VideoObserver::class);
        
        // تسجيل Writer Observer للترجمة التلقائية
        Writer::observe(WriterObserver::class);
        
        // تسجيل Opinion Observer للترجمة التلقائية
        Opinion::observe(OpinionObserver::class);
        
        // تسجيل ContactMessage Observer للإشعارات
        ContactMessage::observe(ContactMessageObserver::class);

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
