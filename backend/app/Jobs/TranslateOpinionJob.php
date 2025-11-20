<?php

namespace App\Jobs;

use App\Models\Opinion;
use App\Services\GeminiTranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TranslateOpinionJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 300; // 5 minutes for content translation

    public function __construct(public int $opinionId) {}

    public function handle(GeminiTranslationService $translationService): void
    {
        $opinion = Opinion::find($this->opinionId);

        if (!$opinion) {
            Log::warning("Opinion not found: {$this->opinionId}");
            return;
        }

        try {
            // استخدام translateContent لترجمة كل شيء مرة واحدة
            if ($opinion->title || $opinion->content) {
                $result = $translationService->translateContent(
                    $opinion->title ?? '',
                    $opinion->content ?? ''
                );

                if ($result) {
                    if (!$opinion->title_en && isset($result['title_en'])) {
                        $opinion->title_en = $result['title_en'];
                    }
                    if (!$opinion->content_en && isset($result['content_en'])) {
                        $opinion->content_en = $result['content_en'];
                    }
                }
            }

            // ترجمة Excerpt منفصلة
            if ($opinion->excerpt && !$opinion->excerpt_en) {
                $opinion->excerpt_en = $translationService->translateText($opinion->excerpt);
            }

            $opinion->saveQuietly();
            Log::info("Opinion translated: {$opinion->title}");

        } catch (\Exception $e) {
            Log::error("Failed to translate opinion {$this->opinionId}: " . $e->getMessage());
            throw $e;
        }
    }
}
