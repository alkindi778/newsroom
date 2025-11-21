<?php

namespace App\Console\Commands;

use App\Models\Opinion;
use App\Jobs\TranslateOpinionJob;
use Illuminate\Console\Command;

class TranslateOpinions extends Command
{
    protected $signature = 'opinions:translate {--force : Force translation even if already translated} {--limit= : Maximum number of opinions to translate}';
    protected $description = 'Translate opinions to English';

    public function handle()
    {
        $this->info('Starting opinions translation...');
        
        $query = Opinion::query();
        $force = $this->option('force');
        $limit = $this->option('limit');
        
        // إذا لم يكن force، نجلب فقط غير المترجمة
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('title_en')->orWhereNull('content_en');
            });
        }
        
        // إذا كان هناك حد، نطبقه
        if ($limit) {
            $query->limit((int) $limit);
        }
        
        $opinions = $query->get();
        
        $this->info("Found {$opinions->count()} opinions to translate.");
        
        if ($opinions->isEmpty()) {
            $this->info('No opinions need translation.');
            return Command::SUCCESS;
        }
        
        $dispatched = 0;
        
        foreach ($opinions as $opinion) {
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
