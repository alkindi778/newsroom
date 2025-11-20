<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\GeminiTranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The article instance
     */
    protected Article $article;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job
     */
    public int $backoff = 60;

    /**
     * The maximum number of seconds the job should be allowed to run
     */
    public int $timeout = 120;

    /**
     * Create a new job instance
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Execute the job
     */
    public function handle(GeminiTranslationService $translationService): void
    {
        try {
            Log::info('Starting translation job', [
                'article_id' => $this->article->id,
                'title' => $this->article->title
            ]);

            // Skip if already translated
            if (!empty($this->article->title_en) && !empty($this->article->content_en)) {
                Log::info('Article already translated, skipping', [
                    'article_id' => $this->article->id
                ]);
                return;
            }

            // Get translation from Gemini
            $translation = $translationService->translateContent(
                $this->article->title,
                $this->article->content
            );

            if (!$translation) {
                Log::error('Translation failed for article', [
                    'article_id' => $this->article->id,
                    'title' => $this->article->title
                ]);
                
                // Job will be retried based on $tries setting
                throw new \Exception('Translation service returned null');
            }

            // Update article with translation
            $this->article->update([
                'title_en' => $translation['title_en'],
                'content_en' => $translation['content_en']
            ]);

            Log::info('Article translated successfully', [
                'article_id' => $this->article->id,
                'title_en' => $translation['title_en']
            ]);

        } catch (\Exception $e) {
            Log::error('Translation job failed', [
                'article_id' => $this->article->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Translation job failed permanently after all retries', [
            'article_id' => $this->article->id,
            'title' => $this->article->title,
            'error' => $exception->getMessage()
        ]);

        // You could send a notification to admins here
        // Or update a status field on the article to mark translation as failed
    }
}
