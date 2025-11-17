<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateOldPhotosPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:update-old-photos-path';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update custom_properties path for old photos in media table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting to update old photos paths...');

        // Get all media records with old_photos collection
        $mediaRecords = DB::table('media')
            ->where('collection_name', 'old_photos')
            ->get();

        if ($mediaRecords->isEmpty()) {
            $this->warn('⚠️  No old photos found in media table');
            return 0;
        }

        $this->info("📊 Found {$mediaRecords->count()} records to update");
        
        $bar = $this->output->createProgressBar($mediaRecords->count());
        $bar->start();

        $updated = 0;

        foreach ($mediaRecords as $media) {
            try {
                // Decode custom properties
                $customProperties = json_decode($media->custom_properties, true);
                
                // Update the path if it starts with old_photos/
                if (isset($customProperties['path']) && str_starts_with($customProperties['path'], 'old_photos/')) {
                    $customProperties['path'] = str_replace('old_photos/', 'media/old_photos/', $customProperties['path']);
                    
                    // Update the record
                    DB::table('media')
                        ->where('id', $media->id)
                        ->update([
                            'custom_properties' => json_encode($customProperties),
                            'updated_at' => now(),
                        ]);
                    
                    $updated++;
                }
                
            } catch (\Exception $e) {
                $this->error("\nFailed to update media ID {$media->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Update completed!");
        $this->info("📊 Updated: {$updated} records");
        $this->newLine();
        
        return 0;
    }
}
