<?php

namespace App\Observers;

use App\Models\HomepageSection;
use App\Jobs\TranslateSectionJob;

class HomepageSectionObserver
{
    /**
     * Handle the HomepageSection "created" event.
     */
    public function created(HomepageSection $homepageSection): void
    {
        // ترجمة Title و Subtitle تلقائياً
        $this->translateSection($homepageSection);
    }

    /**
     * Handle the HomepageSection "updated" event.
     */
    public function updated(HomepageSection $homepageSection): void
    {
        // إعادة ترجمة إذا تغير Title أو Subtitle
        if ($homepageSection->wasChanged('title') || $homepageSection->wasChanged('subtitle')) {
            $this->translateSection($homepageSection);
        }
    }

    /**
     * ترجمة Title و Subtitle
     */
    private function translateSection(HomepageSection $section): void
    {
        // Dispatch translation job
        TranslateSectionJob::dispatch($section->id);
    }

    /**
     * Handle the HomepageSection "deleted" event.
     */
    public function deleted(HomepageSection $homepageSection): void
    {
        //
    }

    /**
     * Handle the HomepageSection "restored" event.
     */
    public function restored(HomepageSection $homepageSection): void
    {
        //
    }

    /**
     * Handle the HomepageSection "force deleted" event.
     */
    public function forceDeleted(HomepageSection $homepageSection): void
    {
        //
    }
}
