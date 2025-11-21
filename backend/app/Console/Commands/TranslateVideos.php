<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Jobs\TranslateVideoJob;
use Illuminate\Console\Command;

class TranslateVideos extends Command
{
    protected $signature = 'videos:translate {--force : Force translation even if already translated} {--limit= : Maximum number of videos to translate}';
    protected $description = 'Translate videos to English';

    public function handle()
    {
        $this->info('Starting videos translation...');
        
        $query = Video::query();
        $force = $this->option('force');
        $limit = $this->option('limit');
        
        // إذا لم يكن force، نجلب فقط غير المترجمة
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('title_en')->orWhereNull('description_en');
            });
        }
        
        // إذا كان هناك حد، نطبقه
        if ($limit) {
            $query->limit((int) $limit);
        }
        
        $videos = $query->get();
        
        $this->info("Found {$videos->count()} videos to translate.");
        
        if ($videos->isEmpty()) {
            $this->info('No videos need translation.');
            return Command::SUCCESS;
        }
        
        $dispatched = 0;
        
        foreach ($videos as $video) {
            TranslateVideoJob::dispatch($video->id);
            $dispatched++;
            $this->info("✓ Queued: {$video->title}");
        }
        
        $this->newLine();
        $this->info("Translation jobs dispatched: {$dispatched}");
        $this->info('Run: php artisan queue:work');
        
        return Command::SUCCESS;
    }
}
