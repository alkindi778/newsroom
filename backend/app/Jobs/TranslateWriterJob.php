<?php

namespace App\Jobs;

use App\Models\Writer;
use App\Services\GeminiTranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TranslateWriterJob implements ShouldQueue
{
    use Queueable;

    public $tries = 10;
    public $timeout = 180;
    public $backoff = [60, 300, 900];

    public function __construct(public int $writerId) {}

    public function handle(GeminiTranslationService $translationService): void
    {
        $writer = Writer::find($this->writerId);

        if (!$writer) {
            Log::warning("Writer not found: {$this->writerId}");
            return;
        }

        try {
            // ترجمة Name
            if ($writer->name && !$writer->name_en) {
                $writer->name_en = $translationService->translateText($writer->name);
            }

            // ترجمة Bio
            if ($writer->bio && !$writer->bio_en) {
                $writer->bio_en = $translationService->translateText($writer->bio);
            }

            // ترجمة Position
            if ($writer->position && !$writer->position_en) {
                $writer->position_en = $translationService->translateText($writer->position);
            }

            // ترجمة Specialization
            if ($writer->specialization && !$writer->specialization_en) {
                $writer->specialization_en = $translationService->translateText($writer->specialization);
            }

            $writer->saveQuietly();
            Log::info("Writer translated: {$writer->name}");

        } catch (\Exception $e) {
            Log::error("Failed to translate writer {$this->writerId}: " . $e->getMessage());
            throw $e;
        }
    }
}
