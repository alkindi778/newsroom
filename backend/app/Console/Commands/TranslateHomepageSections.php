<?php

namespace App\Console\Commands;

use App\Models\HomepageSection;
use App\Jobs\TranslateSectionJob;
use Illuminate\Console\Command;

class TranslateHomepageSections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sections:translate {--force : Force translation even if already translated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate homepage sections titles and subtitles to English';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting homepage sections translation...');
        
        $sections = HomepageSection::all();
        $force = $this->option('force');
        
        $this->info("Found {$sections->count()} sections to translate.");
        
        $dispatched = 0;
        
        foreach ($sections as $section) {
            // Skip if already translated (unless force is used)
            if (!$force && $section->title_en && $section->subtitle_en) {
                $this->line("Skipping '{$section->title}' (already translated)");
                continue;
            }
            
            TranslateSectionJob::dispatch($section->id);
            $dispatched++;
            $this->info("✓ Queued translation for: {$section->title}");
        }
        
        $this->newLine();
        $this->info("Translation jobs dispatched: {$dispatched}");
        $this->info('Make sure the queue worker is running: php artisan queue:work');
        
        return Command::SUCCESS;
    }
}
