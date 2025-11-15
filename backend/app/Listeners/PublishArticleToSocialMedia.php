<?php

namespace App\Listeners;

use App\Events\ArticlePublished;
use App\Services\SocialMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PublishArticleToSocialMedia implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private SocialMediaService $socialMediaService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ArticlePublished $event): void
    {
        // تحقق من إعدادات النشر التلقائي
        if (!config('social-media.global.auto_publish_on_create')) {
            return;
        }

        // نشر المقالة على جميع المنصات المفعّلة
        $this->socialMediaService->publishArticle($event->article);
    }
}
