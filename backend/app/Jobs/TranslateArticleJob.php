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

    public $tries = 10; // محاولات أكثر
    public $timeout = 180;
    public $backoff = [60, 300, 900]; // إعادة المحاولة بعد 1 دقيقة، 5 دقائق، 15 دقيقة

    public function __construct(public int $articleId) {}

    public function handle(GeminiTranslationService $translationService): void
    {
        $article = Article::find($this->articleId);

        if (!$article) {
            Log::warning("Article not found: {$this->articleId}");
            return;
        }

        try {
            // Skip if already translated
            if ($article->title_en && $article->content_en) {
                return;
            }

            // Translate Title + Content together for better quality
            $translation = $translationService->translateContent($article->title, $article->content);
            
            if ($translation) {
                $article->title_en = $translation['title_en'] ?? null;
                $article->content_en = $translation['content_en'] ?? null;
                $article->saveQuietly();
                Log::info("Article translated: {$article->title}");
            } else {
                Log::warning("Translation failed for article: {$article->title}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to translate article {$this->articleId}: " . $e->getMessage());
            throw $e;
        }
    }
}
