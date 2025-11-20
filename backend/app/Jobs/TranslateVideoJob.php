<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\GeminiTranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TranslateVideoJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(public int $videoId) {}

    public function handle(GeminiTranslationService $translationService): void
    {
        $video = Video::find($this->videoId);

        if (!$video) {
            Log::warning("Video not found: {$this->videoId}");
            return;
        }

        try {
            // ترجمة Title
            if ($video->title && !$video->title_en) {
                $video->title_en = $translationService->translateText($video->title);
            }

            // ترجمة Description
            if ($video->description && !$video->description_en) {
                $video->description_en = $translationService->translateText($video->description);
            }

            $video->saveQuietly();
            Log::info("Video translated: {$video->title}");

        } catch (\Exception $e) {
            Log::error("Failed to translate video {$this->videoId}: " . $e->getMessage());
            throw $e;
        }
    }
}
