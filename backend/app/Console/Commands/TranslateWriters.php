<?php

namespace App\Console\Commands;

use App\Models\Writer;
use App\Jobs\TranslateWriterJob;
use Illuminate\Console\Command;

class TranslateWriters extends Command
{
    protected $signature = 'writers:translate {--force : Force translation even if already translated}';
    protected $description = 'Translate all writers to English';

    public function handle()
    {
        $this->info('Starting writers translation...');
        
        $writers = Writer::all();
        $force = $this->option('force');
        
        $this->info("Found {$writers->count()} writers to translate.");
        
        $dispatched = 0;
        
        foreach ($writers as $writer) {
            if (!$force && $writer->name_en && $writer->bio_en) {
                $this->line("Skipping '{$writer->name}' (already translated)");
                continue;
            }
            
            TranslateWriterJob::dispatch($writer->id);
            $dispatched++;
            $this->info("✓ Queued: {$writer->name}");
        }
        
        $this->newLine();
        $this->info("Translation jobs dispatched: {$dispatched}");
        $this->info('Run: php artisan queue:work');
        
        return Command::SUCCESS;
    }
}
