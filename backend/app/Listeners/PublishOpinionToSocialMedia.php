<?php

namespace App\Listeners;

use App\Events\OpinionPublished;
use App\Services\SocialMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PublishOpinionToSocialMedia implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private SocialMediaService $socialMediaService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(OpinionPublished $event): void
    {
        // تحقق من إعدادات النشر التلقائي
        if (!config('social-media.global.auto_publish_on_create')) {
            return;
        }

        // نشر المقال على جميع المنصات المفعّلة
        $this->socialMediaService->publishOpinion($event->opinion);
    }
}
