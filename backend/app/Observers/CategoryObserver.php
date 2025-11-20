<?php

namespace App\Observers;

use App\Jobs\TranslateCategoryJob;
use App\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        // Only translate if translation is enabled and auto-translate is on
        if (!config('translation.enabled', true)) {
            return;
        }

        if (!config('translation.auto_translate_on_create', true)) {
            return;
        }

        // Dispatch translation job
        TranslateCategoryJob::dispatch($category);
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        // Only translate if translation is enabled and auto-translate on update is on
        if (!config('translation.enabled', true)) {
            return;
        }

        if (!config('translation.auto_translate_on_update', true)) {
            return;
        }

        // Only re-translate if the Arabic name has changed
        if ($category->wasChanged('name')) {
            TranslateCategoryJob::dispatch($category, force: true);
        }
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        // Re-translate if translation doesn't exist
        if (!$category->name_en && config('translation.enabled', true)) {
            TranslateCategoryJob::dispatch($category);
        }
    }
}
