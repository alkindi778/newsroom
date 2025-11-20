<?php

namespace App\Observers;

use App\Models\Video;
use App\Jobs\TranslateVideoJob;

class VideoObserver
{
    /**
     * Handle the Video "created" event.
     */
    public function created(Video $video): void
    {
        $this->translateVideo($video);
    }

    /**
     * Handle the Video "updated" event.
     */
    public function updated(Video $video): void
    {
        if ($video->wasChanged('title') || $video->wasChanged('description')) {
            $this->translateVideo($video);
        }
    }

    /**
     * ترجمة الفيديو
     */
    private function translateVideo(Video $video): void
    {
        TranslateVideoJob::dispatch($video->id);
    }

    /**
     * Handle the Video "deleted" event.
     */
    public function deleted(Video $video): void
    {
        //
    }

    /**
     * Handle the Video "restored" event.
     */
    public function restored(Video $video): void
    {
        //
    }

    /**
     * Handle the Video "force deleted" event.
     */
    public function forceDeleted(Video $video): void
    {
        //
    }
}
