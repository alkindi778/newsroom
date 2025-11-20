<?php

namespace App\Observers;

use App\Models\Article;
use App\Jobs\TranslateContentJob;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{
    /**
     * Handle the Article "created" event
     */
    public function created(Article $article): void
    {
        // Check if auto-translation is enabled
        if (!config('translation.enabled', true) || !config('translation.auto_translate_on_create', true)) {
            return;
        }

        // Only trigger translation if we have both title and content
        if (empty($article->title) || empty($article->content)) {
            Log::info('Skipping translation - missing title or content', [
                'article_id' => $article->id
            ]);
            return;
        }

        // Dispatch the translation job to the queue
        TranslateContentJob::dispatch($article);

        Log::info('Translation job dispatched', [
            'article_id' => $article->id,
            'title' => $article->title
        ]);
    }

    /**
     * Handle the Article "updated" event
     * Optional: Re-translate if Arabic content changes
     */
    public function updated(Article $article): void
    {
        // Check if auto-translation on update is enabled
        if (!config('translation.enabled', true) || !config('translation.auto_translate_on_update', true)) {
            return;
        }

        // Check if title or content was changed
        $titleChanged = $article->wasChanged('title');
        $contentChanged = $article->wasChanged('content');

        // Only re-translate if Arabic content actually changed
        if ($titleChanged || $contentChanged) {
            Log::info('Article content changed, re-translating', [
                'article_id' => $article->id,
                'title_changed' => $titleChanged,
                'content_changed' => $contentChanged
            ]);

            TranslateContentJob::dispatch($article);
        }
    }

    /**
     * Handle the Article "deleted" event
     */
    public function deleted(Article $article): void
    {
        // Optional: Add any cleanup logic here
    }

    /**
     * Handle the Article "restored" event
     */
    public function restored(Article $article): void
    {
        // Optional: Re-translate when article is restored
        if (empty($article->title_en) || empty($article->content_en)) {
            TranslateContentJob::dispatch($article);
        }
    }

    /**
     * Handle the Article "force deleted" event
     */
    public function forceDeleted(Article $article): void
    {
        // Optional: Add any cleanup logic here
    }
}
