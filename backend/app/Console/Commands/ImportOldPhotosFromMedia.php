<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportOldPhotosFromMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:import-old-photos-from-media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import old photos from media/old_photos folder to media library (correct path)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting import of old photos from media/old_photos...');

        $disk = 'public';
        $directory = 'media/old_photos';
        
        // Get all files from media/old_photos directory
        $files = Storage::disk($disk)->allFiles($directory);
        
        if (empty($files)) {
            $this->warn('⚠️  No photos found in media/old_photos directory');
            return 0;
        }

        $this->info("📊 Found " . count($files) . " photos");
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $imported = 0;
        $skipped = 0;

        foreach ($files as $file) {
            try {
                // Skip if not an image
                $mimeType = Storage::disk($disk)->mimeType($file);
                if (!str_starts_with($mimeType, 'image/')) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Get file info
                $fileName = basename($file);
                $fileSize = Storage::disk($disk)->size($file);
                
                // Check if already exists in media table
                $exists = DB::table('media')
                    ->where('file_name', $fileName)
                    ->where('collection_name', 'old_photos')
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Insert into media table
                DB::table('media')->insert([
                    'model_type' => 'App\\Models\\Article',
                    'model_id' => 0,
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'old_photos',
                    'name' => pathinfo($fileName, PATHINFO_FILENAME),
                    'file_name' => $fileName,
                    'mime_type' => $mimeType,
                    'disk' => $disk,
                    'conversions_disk' => $disk,
                    'size' => $fileSize,
                    'manipulations' => json_encode([]),
                    'custom_properties' => json_encode([
                        'path' => $file, // Already in correct path: media/old_photos/...
                        'imported_from' => 'old_database',
                        'imported_at' => now()->toDateTimeString(),
                    ]),
                    'generated_conversions' => json_encode([]),
                    'responsive_images' => json_encode([]),
                    'order_column' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $imported++;
            } catch (\Exception $e) {
                $this->error("\nFailed to import {$file}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Import completed!");
        $this->info("📊 Statistics:");
        $this->info("   - Imported: {$imported} photos");
        $this->info("   - Skipped: {$skipped} photos");
        $this->newLine();
        
        return 0;
    }
}
