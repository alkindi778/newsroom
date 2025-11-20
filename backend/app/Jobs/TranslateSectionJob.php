<?php

namespace App\Jobs;

use App\Models\HomepageSection;
use App\Services\GeminiTranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TranslateSectionJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $sectionId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GeminiTranslationService $translationService): void
    {
        $section = HomepageSection::find($this->sectionId);

        if (!$section) {
            Log::warning("Homepage section not found: {$this->sectionId}");
            return;
        }

        try {
            // ترجمة Title
            if ($section->title && !$section->title_en) {
                $section->title_en = $translationService->translateText($section->title);
            }

            // ترجمة Subtitle
            if ($section->subtitle && !$section->subtitle_en) {
                $section->subtitle_en = $translationService->translateText($section->subtitle);
            }

            // حفظ بدون إطلاق Observer مرة أخرى
            $section->saveQuietly();

            Log::info("Homepage section translated successfully: {$section->title} -> {$section->title_en}");

        } catch (\Exception $e) {
            Log::error("Failed to translate homepage section {$this->sectionId}: " . $e->getMessage());
            throw $e;
        }
    }
}
