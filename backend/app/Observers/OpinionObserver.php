<?php

namespace App\Observers;

use App\Models\Opinion;
use App\Jobs\TranslateOpinionJob;

class OpinionObserver
{
    /**
     * Handle the Opinion "created" event.
     */
    public function created(Opinion $opinion): void
    {
        $this->translateOpinion($opinion);
    }

    /**
     * Handle the Opinion "updated" event.
     */
    public function updated(Opinion $opinion): void
    {
        if ($opinion->wasChanged(['title', 'excerpt', 'content'])) {
            $this->translateOpinion($opinion);
        }
    }

    /**
     * ترجمة المقال
     */
    private function translateOpinion(Opinion $opinion): void
    {
        TranslateOpinionJob::dispatch($opinion->id);
    }

    /**
     * Handle the Opinion "deleted" event.
     */
    public function deleted(Opinion $opinion): void
    {
        //
    }

    /**
     * Handle the Opinion "restored" event.
     */
    public function restored(Opinion $opinion): void
    {
        //
    }

    /**
     * Handle the Opinion "force deleted" event.
     */
    public function forceDeleted(Opinion $opinion): void
    {
        //
    }
}
