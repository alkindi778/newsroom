<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveOldPhotosToMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:move-old-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move old photos from storage/old_photos to storage/media/old_photos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting to move old photos...');

        $disk = 'public';
        $sourceDirectory = 'old_photos';
        $targetDirectory = 'media/old_photos';
        
        // Get all files from old_photos directory
        $files = Storage::disk($disk)->allFiles($sourceDirectory);
        
        if (empty($files)) {
            $this->warn('⚠️  No photos found in old_photos directory');
            return 0;
        }

        $this->info("📊 Found " . count($files) . " files to move");
        
        // Create target directory if not exists
        if (!Storage::disk($disk)->exists($targetDirectory)) {
            Storage::disk($disk)->makeDirectory($targetDirectory);
        }
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $moved = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($files as $file) {
            try {
                // Get just the filename
                $fileName = basename($file);
                $targetPath = $targetDirectory . '/' . $fileName;
                
                // Check if target already exists
                if (Storage::disk($disk)->exists($targetPath)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Move the file
                Storage::disk($disk)->move($file, $targetPath);
                $moved++;
                
            } catch (\Exception $e) {
                $this->error("\nFailed to move {$file}: {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Move completed!");
        $this->info("📊 Statistics:");
        $this->info("   - Moved: {$moved} files");
        $this->info("   - Skipped: {$skipped} files (already exist)");
        if ($failed > 0) {
            $this->warn("   - Failed: {$failed} files");
        }
        
        // Ask if user wants to delete the now-empty old_photos directory
        if ($moved > 0 && $this->confirm('Do you want to delete the now-empty old_photos directory?', false)) {
            try {
                Storage::disk($disk)->deleteDirectory($sourceDirectory);
                $this->info('✅ Old directory deleted successfully');
            } catch (\Exception $e) {
                $this->error('❌ Failed to delete old directory: ' . $e->getMessage());
            }
        }
        
        $this->newLine();
        
        return 0;
    }
}
