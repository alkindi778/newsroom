<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Opinion;
use App\Models\Video;
use App\Models\Writer;
use App\Models\Category;
use App\Jobs\TranslateArticleJob;
use App\Jobs\TranslateOpinionJob;
use Illuminate\Console\Command;

class TranslateArticlesCommand extends Command
{
    protected $signature = 'content:translate {--batch=50 : Number of items to translate per batch} {--type=all : Type to translate: all, articles, opinions, writers, categories, videos}';
    protected $description = 'Automatically translate all content types (articles, opinions, writers, categories, videos)';

    public function handle()
    {
        $batchSize = $this->option('batch');
        $type = $this->option('type');

        $dispatched = 0;

        if ($type === 'all' || $type === 'articles') {
            $dispatched += $this->translateArticles($batchSize);
        }

        if ($type === 'all' || $type === 'opinions') {
            $dispatched += $this->translateOpinions($batchSize);
        }

        if ($type === 'all' || $type === 'categories') {
            $dispatched += $this->translateCategories($batchSize);
        }

        if ($type === 'all' || $type === 'writers') {
            $dispatched += $this->translateWriters($batchSize);
        }

        if ($type === 'all' || $type === 'videos') {
            $dispatched += $this->translateVideos($batchSize);
        }

        if ($dispatched === 0) {
            $this->info('✅ Everything is already translated!');
        } else {
            $this->info("✅ Total dispatched: {$dispatched} translation jobs");
        }
    }

    private function translateArticles($batchSize)
    {
        $articles = Article::where(function($query) {
            $query->whereNull('title_en')
                  ->orWhereNull('content_en');
        })
        ->orderBy('id', 'desc')
        ->limit($batchSize)
        ->get();

        if ($articles->isEmpty()) {
            return 0;
        }

        $this->info("📰 Found {$articles->count()} articles to translate");

        foreach ($articles as $article) {
            TranslateArticleJob::dispatch($article->id);
        }

        return $articles->count();
    }

    private function translateOpinions($batchSize)
    {
        $opinions = Opinion::where(function($query) {
            $query->whereNull('title_en')
                  ->orWhereNull('content_en');
        })
        ->orderBy('id', 'desc')
        ->limit($batchSize)
        ->get();

        if ($opinions->isEmpty()) {
            return 0;
        }

        $this->info("📝 Found {$opinions->count()} opinions to translate");

        foreach ($opinions as $opinion) {
            TranslateOpinionJob::dispatch($opinion->id);
        }

        return $opinions->count();
    }

    private function translateCategories($batchSize)
    {
        $categories = Category::whereNull('name_en')
            ->limit($batchSize)
            ->get();

        if ($categories->isEmpty()) {
            return 0;
        }

        $this->info("📂 Found {$categories->count()} categories to translate");

        foreach ($categories as $category) {
            // ترجمة بسيطة للأسماء فقط
            \App\Jobs\TranslateCategoryJob::dispatch($category->id);
        }

        return $categories->count();
    }

    private function translateWriters($batchSize)
    {
        $writers = Writer::where(function($query) {
            $query->whereNull('name_en')
                  ->orWhereNull('bio_en')
                  ->orWhereNull('position_en')
                  ->orWhereNull('specialization_en');
        })
        ->orderBy('id', 'desc')
        ->limit($batchSize)
        ->get();

        if ($writers->isEmpty()) {
            return 0;
        }

        $this->info("✍️ Found {$writers->count()} writers to translate");

        foreach ($writers as $writer) {
            \App\Jobs\TranslateWriterJob::dispatch($writer->id);
        }

        return $writers->count();
    }

    private function translateVideos($batchSize)
    {
        $videos = Video::where(function($query) {
            $query->whereNull('title_en')
                  ->orWhereNull('description_en');
        })
        ->orderBy('id', 'desc')
        ->limit($batchSize)
        ->get();

        if ($videos->isEmpty()) {
            return 0;
        }

        $this->info("🎥 Found {$videos->count()} videos to translate");

        foreach ($videos as $video) {
            \App\Jobs\TranslateVideoJob::dispatch($video->id);
        }

        return $videos->count();
    }
}
