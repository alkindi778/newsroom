<?php

namespace App\Console\Commands;

use App\Models\Opinion;
use App\Jobs\TranslateOpinionJob;
use Illuminate\Console\Command;

class TranslateOpinions extends Command
{
    protected $signature = 'opinions:translate {--force : Force translation even if already translated}';
    protected $description = 'Translate all opinions to English';

    public function handle()
    {
        $this->info('Starting opinions translation...');
        
        $opinions = Opinion::all();
        $force = $this->option('force');
        
        $this->info("Found {$opinions->count()} opinions to translate.");
        
        $dispatched = 0;
        
        foreach ($opinions as $opinion) {
            if (!$force && $opinion->title_en && $opinion->content_en) {
                $this->line("Skipping '{$opinion->title}' (already translated)");
                continue;
            }
            
            TranslateOpinionJob::dispatch($opinion->id);
            $dispatched++;
            $this->info("✓ Queued: {$opinion->title}");
        }
        
        $this->newLine();
        $this->info("Translation jobs dispatched: {$dispatched}");
        $this->info('Run: php artisan queue:work');
        
        return Command::SUCCESS;
    }
}
