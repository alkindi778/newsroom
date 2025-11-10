<?php

namespace App\Listeners;

use App\Events\OpinionPublished;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOpinionNotification implements ShouldQueue
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
    public function handle(OpinionPublished $event): void
    {
        $this->pushService->sendNewOpinionNotification($event->opinion);
    }

    /**
     * Handle a job failure.
     */
    public function failed(OpinionPublished $event, \Throwable $exception): void
    {
        \Log::error('Failed to send opinion notification', [
            'opinion_id' => $event->opinion->id,
            'error' => $exception->getMessage()
        ]);
    }
}
