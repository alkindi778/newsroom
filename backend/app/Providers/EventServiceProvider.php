<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded;
use App\Listeners\OptimizeUploadedImage;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // ضغط الصور تلقائياً بعد رفعها
        MediaHasBeenAdded::class => [
            OptimizeUploadedImage::class,
        ],

        // إشعارات المحتوى الجديد
        \App\Events\ArticlePublished::class => [
            \App\Listeners\SendArticleNotification::class,
            \App\Listeners\PublishArticleToSocialMedia::class,
        ],
        \App\Events\VideoPublished::class => [
            \App\Listeners\SendVideoNotification::class,
        ],
        \App\Events\OpinionPublished::class => [
            \App\Listeners\SendOpinionNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
