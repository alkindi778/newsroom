<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Jobs\TranslateContentJob;
use Illuminate\Console\Command;

class TranslateExistingArticles extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'articles:translate
                          {--limit= : Maximum number of articles to translate}
                          {--force : Re-translate articles that already have translations}';

    /**
     * The console command description.
     */
    protected $description = 'Translate existing articles to English using Gemini AI';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting batch translation process...');
        
        $query = Article::query();
        
        // If not forcing, only select untranslated articles
        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('title_en')
                  ->orWhereNull('content_en');
            });
        }
        
        // Apply limit if specified
        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }
        
        $totalArticles = $query->count();
        
        if ($totalArticles === 0) {
            $this->info('✅ No articles found for translation.');
            return self::SUCCESS;
        }
        
        $this->info("📊 Found {$totalArticles} articles to translate.");
        
        if (!$this->confirm('Do you want to proceed?', true)) {
            $this->info('❌ Translation cancelled.');
            return self::FAILURE;
        }
        
        $progressBar = $this->output->createProgressBar($totalArticles);
        $progressBar->start();
        
        $dispatched = 0;
        
        // Process in chunks to avoid memory issues
        $query->chunk(50, function ($articles) use (&$dispatched, $progressBar) {
            foreach ($articles as $article) {
                // Ensure article has content to translate
                if (empty($article->title) || empty($article->content)) {
                    $progressBar->advance();
                    continue;
                }
                
                // Dispatch translation job
                TranslateContentJob::dispatch($article);
                $dispatched++;
                
                $progressBar->advance();
            }
        });
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("✅ Successfully dispatched {$dispatched} translation jobs!");
        $this->info("⏳ Translations will be processed in the background.");
        $this->info("💡 Monitor progress with: php artisan queue:work");
        
        return self::SUCCESS;
    }
}
