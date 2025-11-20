<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Jobs\TranslateVideoJob;
use Illuminate\Console\Command;

class TranslateVideos extends Command
{
    protected $signature = 'videos:translate {--force : Force translation even if already translated}';
    protected $description = 'Translate all videos to English';

    public function handle()
    {
        $this->info('Starting videos translation...');
        
        $videos = Video::all();
        $force = $this->option('force');
        
        $this->info("Found {$videos->count()} videos to translate.");
        
        $dispatched = 0;
        
        foreach ($videos as $video) {
            if (!$force && $video->title_en && $video->description_en) {
                $this->line("Skipping '{$video->title}' (already translated)");
                continue;
            }
            
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
