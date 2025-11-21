<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Jobs\TranslateArticleJob;
use Illuminate\Console\Command;

class TranslateArticles extends Command
{
    protected $signature = 'articles:translate 
                            {--force : Force translation even if already translated}
                            {--limit= : Limit number of articles to translate}
                            {--chunk=100 : Process articles in chunks}';
    
    protected $description = 'Translate all articles to English';

    public function handle()
    {
        $this->info('Starting articles translation...');
        
        $force = $this->option('force');
        $limit = $this->option('limit');
        $chunkSize = (int) $this->option('chunk');
        
        // بناء الاستعلام
        $query = Article::query();
        
        if (!$force) {
            // فقط المقالات غير المترجمة
            $query->where(function($q) {
                $q->whereNull('title_en')
                  ->orWhereNull('content_en');
            });
        }
        
        if ($limit) {
            $query->limit((int) $limit);
        }
        
        $total = $query->count();
        $this->info("Found {$total} articles to translate.");
        
        if ($total === 0) {
            $this->info('No articles to translate!');
            return Command::SUCCESS;
        }
        
        $dispatched = 0;
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        // معالجة على دفعات
        $query->chunk($chunkSize, function ($articles) use (&$dispatched, $bar, $force) {
            foreach ($articles as $article) {
                if (!$force && $article->title_en && $article->content_en) {
                    $bar->advance();
                    continue;
                }
                
                TranslateArticleJob::dispatch($article->id);
                $dispatched++;
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Translation jobs dispatched: {$dispatched}");
        $this->newLine();
        $this->info('Run queue worker: php artisan queue:work');
        $this->info('Or process in background: php artisan queue:work --daemon');
        
        return Command::SUCCESS;
    }
}
