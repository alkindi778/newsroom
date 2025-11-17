<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;

class ApproveOldArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:approve-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve all old migrated articles and opinions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Approving old articles...');

        // Count draft articles
        $draftCount = Article::where('approval_status', 'draft')->count();
        $this->info("📊 Found {$draftCount} draft articles");

        // Update articles to approved
        $articlesCount = Article::where('approval_status', '!=', 'approved')
            ->update([
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]);

        $this->info("✅ Updated {$articlesCount} articles to approved status");

        // Show summary
        $approvedCount = Article::where('approval_status', 'approved')->count();
        $this->info("📊 Total approved articles: {$approvedCount}");

        $this->info('🎉 All articles are now approved!');

        return 0;
    }
}
