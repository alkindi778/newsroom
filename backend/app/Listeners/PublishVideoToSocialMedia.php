<?php

namespace App\Listeners;

use App\Events\VideoPublished;
use App\Services\SocialMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PublishVideoToSocialMedia implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private SocialMediaService $socialMediaService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(VideoPublished $event): void
    {
        // تحقق من إعدادات النشر التلقائي
        if (!config('social-media.global.auto_publish_on_create')) {
            return;
        }

        // نشر الفيديو على جميع المنصات المفعّلة
        $this->socialMediaService->publishVideo($event->video);
    }
}
