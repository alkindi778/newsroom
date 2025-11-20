<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\GeminiTranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TranslateArticleJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 180;

    public function __construct(public int $articleId) {}

    public function handle(GeminiTranslationService $translationService): void
    {
        $article = Article::find($this->articleId);

        if (!$article) {
            Log::warning("Article not found: {$this->articleId}");
            return;
        }

        try {
            // ترجمة Title
            if ($article->title && !$article->title_en) {
                $article->title_en = $translationService->translateText($article->title);
            }

            // ترجمة Content
            if ($article->content && !$article->content_en) {
                $article->content_en = $translationService->translateText($article->content);
            }

            $article->saveQuietly();
            Log::info("Article translated: {$article->title}");

        } catch (\Exception $e) {
            Log::error("Failed to translate article {$this->articleId}: " . $e->getMessage());
            throw $e;
        }
    }
}
