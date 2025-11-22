<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;

class FixVideoThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:fix-thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix video thumbnails to use YouTube images instead of local paths';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing video thumbnails...');

        // الحصول على جميع الفيديوهات التي لديها thumbnail محلي
        $videos = Video::whereNotNull('thumbnail')
            ->where('video_type', 'youtube')
            ->whereNotNull('video_id')
            ->get();

        if ($videos->isEmpty()) {
            $this->info('No videos need thumbnail fix.');
            return 0;
        }

        $this->info("Found {$videos->count()} videos to fix");

        $fixed = 0;

        foreach ($videos as $video) {
            try {
                // إزالة thumbnail المحلي
                $video->update(['thumbnail' => null]);
                
                $this->info("✓ Fixed: {$video->title}");
                $this->line("  YouTube thumbnail: {$video->thumbnail_url}");
                $fixed++;

            } catch (\Exception $e) {
                $this->error("Error fixing video: {$video->title}");
                $this->error($e->getMessage());
            }
        }

        $this->info("\n=== Fix Complete ===");
        $this->info("Fixed: {$fixed} videos");
        $this->info("All videos now use YouTube thumbnails automatically!");

        return 0;
    }
}
