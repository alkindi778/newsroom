<?php

namespace App\Jobs;

use App\Models\Category;
use App\Services\GeminiTranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateCategoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;
    public $timeout = 180;
    public $backoff = [60, 300, 900];

    /**
     * Create a new job instance.
     */
    public function __construct(public int $categoryId) {}

    /**
     * Execute the job.
     */
    public function handle(GeminiTranslationService $translator): void
    {
        $category = Category::find($this->categoryId);

        if (!$category) {
            Log::warning("Category not found: {$this->categoryId}");
            return;
        }

        try {
            // Skip if translation already exists
            if ($category->name_en) {
                return;
            }

            // Translate the category name
            $translatedName = $translator->translateText($category->name);

            if ($translatedName) {
                $category->name_en = $translatedName;
                $category->saveQuietly();
                Log::info("Category translated: {$category->name} -> {$translatedName}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to translate category {$this->categoryId}: " . $e->getMessage());
            throw $e;
        }
    }

}
