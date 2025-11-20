<?php

namespace App\Console\Commands;

use App\Jobs\TranslateCategoryJob;
use App\Models\Category;
use Illuminate\Console\Command;

class TranslateExistingCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:translate 
                            {--force : Force re-translation even if translation exists}
                            {--chunk= : Number of categories to process in each batch (default: 50)}
                            {--delay= : Delay in seconds between batches (default: 0)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate existing categories using Gemini AI';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if translation is enabled
        if (!config('translation.enabled', true)) {
            $this->error('Translation system is disabled. Enable it in config/translation.php');
            return self::FAILURE;
        }

        $this->info('Starting category translation...');
        $this->newLine();

        $force = $this->option('force');
        $chunkSize = (int) ($this->option('chunk') ?? config('translation.batch.chunk_size', 50));
        $delay = (int) ($this->option('delay') ?? config('translation.batch.delay', 0));

        // Get categories that need translation
        $query = Category::query();
        
        if (!$force) {
            $query->whereNull('name_en');
        }

        $totalCategories = $query->count();

        if ($totalCategories === 0) {
            $this->info('No categories found that need translation.');
            return self::SUCCESS;
        }

        $this->info("Found {$totalCategories} categories to translate.");
        $this->newLine();

        // Confirm before proceeding
        if (!$this->confirm('Do you want to proceed with the translation?', true)) {
            $this->info('Translation cancelled.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($totalCategories);
        $bar->start();

        $processedCount = 0;
        $failedCount = 0;

        // Process categories in chunks
        $query->chunk($chunkSize, function ($categories) use (&$processedCount, &$failedCount, $bar, $force, $delay) {
            foreach ($categories as $category) {
                try {
                    // Dispatch translation job
                    TranslateCategoryJob::dispatch($category, $force);
                    $processedCount++;
                } catch (\Exception $e) {
                    $this->error("\nFailed to dispatch job for category {$category->id}: {$e->getMessage()}");
                    $failedCount++;
                }

                $bar->advance();
            }

            // Delay between batches to avoid overloading
            if ($delay > 0) {
                sleep($delay);
            }
        });

        $bar->finish();
        $this->newLine(2);

        // Show summary
        $this->info("Translation jobs dispatched: {$processedCount}");
        
        if ($failedCount > 0) {
            $this->warn("Failed to dispatch: {$failedCount}");
        }

        $this->newLine();
        $this->info('The translation jobs have been queued.');
        $this->info('Make sure the queue worker is running: php artisan queue:work');

        return self::SUCCESS;
    }
}
