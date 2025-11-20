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

    /**
     * The category instance.
     */
    public Category $category;

    /**
     * Whether to force re-translation even if translation exists.
     */
    public bool $force;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public array $backoff;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout;

    /**
     * Create a new job instance.
     */
    public function __construct(Category $category, bool $force = false)
    {
        $this->category = $category;
        $this->force = $force;
        $this->tries = config('translation.queue.tries', 3);
        $this->backoff = [
            config('translation.queue.backoff', 60),
            config('translation.queue.backoff', 60) * 2,
            config('translation.queue.backoff', 60) * 3,
        ];
        $this->timeout = config('translation.queue.timeout', 120);
        
        // Set queue connection and name from config
        $this->onConnection(config('translation.queue.connection', 'database'));
        $this->onQueue(config('translation.queue.name', 'translations'));
    }

    /**
     * Execute the job.
     */
    public function handle(GeminiTranslationService $translator): void
    {
        try {
            // Skip if translation already exists and not forcing
            if (!$this->force && $this->category->name_en) {
                $this->logInfo("Category already translated, skipping");
                return;
            }

            $this->logInfo("Starting translation");

            // Translate the category name
            $translatedName = $translator->translateText($this->category->name);

            if (!$translatedName) {
                throw new \Exception('Translation returned empty result');
            }

            // Update the category with translation
            $this->category->update([
                'name_en' => $translatedName,
            ]);

            $this->logSuccess("Translation completed successfully");

        } catch (\Exception $e) {
            $this->logError($e);
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Category translation job failed permanently", [
            'category_id' => $this->category->id,
            'category_name' => $this->category->name,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Log info message if logging is enabled.
     */
    private function logInfo(string $message): void
    {
        if (config('translation.logging.enabled', true)) {
            Log::channel(config('translation.logging.channel', 'stack'))
                ->info("[Category Translation] {$message}", [
                    'category_id' => $this->category->id,
                    'category_name' => $this->category->name,
                ]);
        }
    }

    /**
     * Log success message if logging is enabled.
     */
    private function logSuccess(string $message): void
    {
        if (config('translation.logging.enabled', true) && 
            config('translation.logging.log_success', true)) {
            Log::channel(config('translation.logging.channel', 'stack'))
                ->info("[Category Translation] {$message}", [
                    'category_id' => $this->category->id,
                    'category_name' => $this->category->name,
                    'name_en' => $this->category->name_en,
                ]);
        }
    }

    /**
     * Log error message.
     */
    private function logError(\Exception $e): void
    {
        if (config('translation.logging.enabled', true) &&
            config('translation.logging.log_failures', true)) {
            Log::channel(config('translation.logging.channel', 'stack'))
                ->error("[Category Translation] Translation failed", [
                    'category_id' => $this->category->id,
                    'category_name' => $this->category->name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
        }
    }
}
