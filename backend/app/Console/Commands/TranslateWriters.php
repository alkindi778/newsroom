<?php

namespace App\Console\Commands;

use App\Models\Writer;
use App\Jobs\TranslateWriterJob;
use Illuminate\Console\Command;

class TranslateWriters extends Command
{
    protected $signature = 'writers:translate {--force : Force translation even if already translated} {--limit= : Maximum number of writers to translate}';
    protected $description = 'Translate writers to English';

    public function handle()
    {
        $this->info('Starting writers translation...');
        
        $query = Writer::query();
        $force = $this->option('force');
        $limit = $this->option('limit');
        
        // إذا لم يكن force، نجلب فقط غير المترجمة
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('name_en')->orWhereNull('bio_en');
            });
        }
        
        // إذا كان هناك حد، نطبقه
        if ($limit) {
            $query->limit((int) $limit);
        }
        
        $writers = $query->get();
        
        $this->info("Found {$writers->count()} writers to translate.");
        
        if ($writers->isEmpty()) {
            $this->info('No writers need translation.');
            return Command::SUCCESS;
        }
        
        $dispatched = 0;
        
        foreach ($writers as $writer) {
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
