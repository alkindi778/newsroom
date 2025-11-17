<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\Opinion;

class FixOldPhotosSubfolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:fix-old-photos-subfolders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove incorrect subfolders from old_photos paths (e.g., 112025/)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting to fix old_photos subfolder paths...');

        // Fix Articles
        $this->info('📰 Fixing articles...');
        $articles = Article::where('image', 'LIKE', 'media/old_photos/%/%')
            ->orWhere('image', 'LIKE', '/storage/media/old_photos/%/%')
            ->get();

        $articlesBar = $this->output->createProgressBar($articles->count());
        $articlesBar->start();

        $articlesFixed = 0;
        foreach ($articles as $article) {
            try {
                $oldImage = $article->image;
                
                // Remove the date folder (e.g., 112025/)
                // Pattern: media/old_photos/112025/filename.jpg -> media/old_photos/filename.jpg
                $newImage = preg_replace(
                    '#(media/old_photos)/[^/]+/([^/]+)$#',
                    '$1/$2',
                    $oldImage
                );
                
                // Also handle /storage/ prefix
                $newImage = preg_replace(
                    '#(/storage/media/old_photos)/[^/]+/([^/]+)$#',
                    '$1/$2',
                    $newImage
                );
                
                if ($newImage !== $oldImage) {
                    $article->update(['image' => $newImage]);
                    $articlesFixed++;
                }
            } catch (\Exception $e) {
                $this->error("\nFailed to fix article ID {$article->id}: {$e->getMessage()}");
            }
            $articlesBar->advance();
        }
        $articlesBar->finish();
        $this->newLine();

        // Fix Opinions
        $this->info('📝 Fixing opinions...');
        $opinions = Opinion::where('image', 'LIKE', 'media/old_photos/%/%')
            ->orWhere('image', 'LIKE', '/storage/media/old_photos/%/%')
            ->get();

        $opinionsBar = $this->output->createProgressBar($opinions->count());
        $opinionsBar->start();

        $opinionsFixed = 0;
        foreach ($opinions as $opinion) {
            try {
                $oldImage = $opinion->image;
                
                $newImage = preg_replace(
                    '#(media/old_photos)/[^/]+/([^/]+)$#',
                    '$1/$2',
                    $oldImage
                );
                
                $newImage = preg_replace(
                    '#(/storage/media/old_photos)/[^/]+/([^/]+)$#',
                    '$1/$2',
                    $newImage
                );
                
                if ($newImage !== $oldImage) {
                    $opinion->update(['image' => $newImage]);
                    $opinionsFixed++;
                }
            } catch (\Exception $e) {
                $this->error("\nFailed to fix opinion ID {$opinion->id}: {$e->getMessage()}");
            }
            $opinionsBar->advance();
        }
        $opinionsBar->finish();
        $this->newLine(2);
        
        $this->info("✅ Fix completed!");
        $this->info("📊 Statistics:");
        $this->info("   - Articles fixed: {$articlesFixed}");
        $this->info("   - Opinions fixed: {$opinionsFixed}");
        $this->newLine();
        
        return 0;
    }
}
