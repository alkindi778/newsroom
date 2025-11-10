<?php

namespace App\Listeners;

use App\Events\VideoPublished;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVideoNotification implements ShouldQueue
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
    public function handle(VideoPublished $event): void
    {
        $this->pushService->sendNewVideoNotification($event->video);
    }

    /**
     * Handle a job failure.
     */
    public function failed(VideoPublished $event, \Throwable $exception): void
    {
        \Log::error('Failed to send video notification', [
            'video_id' => $event->video->id,
            'error' => $exception->getMessage()
        ]);
    }
}
