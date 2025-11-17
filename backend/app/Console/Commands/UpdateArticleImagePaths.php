<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Article;

class UpdateArticleImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:update-image-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update article image paths from old_photos to media/old_photos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting to update article image paths...');

        // Update articles with old_photos paths
        $articles = Article::where('image', 'LIKE', '%/storage/old_photos/%')
            ->orWhere('image', 'LIKE', 'old_photos/%')
            ->get();

        if ($articles->isEmpty()) {
            $this->info('✅ No articles found with old_photos paths');
            return 0;
        }

        $this->info("📊 Found {$articles->count()} articles to update");
        
        $bar = $this->output->createProgressBar($articles->count());
        $bar->start();

        $updated = 0;

        foreach ($articles as $article) {
            try {
                $oldImage = $article->image;
                
                // Replace old paths with new paths
                $newImage = $oldImage;
                
                // Case 1: /storage/old_photos/ -> /storage/media/old_photos/
                if (str_contains($oldImage, '/storage/old_photos/')) {
                    $newImage = str_replace('/storage/old_photos/', '/storage/media/old_photos/', $oldImage);
                }
                // Case 2: old_photos/ -> media/old_photos/
                elseif (str_starts_with($oldImage, 'old_photos/')) {
                    $newImage = str_replace('old_photos/', 'media/old_photos/', $oldImage);
                }
                
                if ($newImage !== $oldImage) {
                    $article->update(['image' => $newImage]);
                    $updated++;
                }
                
            } catch (\Exception $e) {
                $this->error("\nFailed to update article ID {$article->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Update completed!");
        $this->info("📊 Updated: {$updated} articles");
        $this->newLine();
        
        return 0;
    }
}
