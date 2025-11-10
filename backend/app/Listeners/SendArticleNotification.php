<?php

namespace App\Listeners;

use App\Events\ArticlePublished;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendArticleNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected PushNotificationService $pushService;

    /**
     * Create the event listener.
     */
    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Handle the event.
     */
    public function handle(ArticlePublished $event): void
    {
        $this->pushService->sendNewArticleNotification($event->article);
    }

    /**
     * Handle a job failure.
     */
    public function failed(ArticlePublished $event, \Throwable $exception): void
    {
        \Log::error('Failed to send article notification', [
            'article_id' => $event->article->id,
            'error' => $exception->getMessage()
        ]);
    }
}
