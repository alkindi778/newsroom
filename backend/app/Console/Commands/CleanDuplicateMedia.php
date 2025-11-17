<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicateMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:clean-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate media records from old_photos collection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting to clean duplicate media records...');

        // Get all old_photos media grouped by file_name
        $duplicates = DB::table('media')
            ->select('file_name', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->where('collection_name', 'old_photos')
            ->groupBy('file_name')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicates found!');
            return 0;
        }

        $this->info("📊 Found {$duplicates->count()} duplicate file names");
        
        $bar = $this->output->createProgressBar($duplicates->count());
        $bar->start();

        $deleted = 0;

        foreach ($duplicates as $duplicate) {
            try {
                // Get all records for this file_name
                $ids = explode(',', $duplicate->ids);
                
                // Keep the first record, delete the rest
                $idsToDelete = array_slice($ids, 1);
                
                DB::table('media')
                    ->whereIn('id', $idsToDelete)
                    ->delete();
                
                $deleted += count($idsToDelete);
                
            } catch (\Exception $e) {
                $this->error("\nFailed to clean duplicates for {$duplicate->file_name}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Cleanup completed!");
        $this->info("📊 Deleted: {$deleted} duplicate records");
        $this->newLine();
        
        return 0;
    }
}
