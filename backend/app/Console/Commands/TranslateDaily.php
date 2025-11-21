<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TranslateDaily extends Command
{
    protected $signature = 'translate:daily {--limit=200}';
    protected $description = 'Translate content daily with API limit control (default 200 requests/day)';

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $this->info("Starting daily translation with limit: {$limit} requests");
        
        // توزيع الترجمات حسب الأولوية
        $distribution = [
            'articles' => 130,  // أولوية عالية - الأخبار
            'opinions' => 30,   // أولوية عالية - مقالات الرأي
            'writers' => 35,    // أولوية عالية - الكتاب
            'videos' => 5,      // أولوية منخفضة
        ];
        
        $totalUsed = 0;
        
        // 1. الأخبار (أولوية عالية)
        if ($totalUsed < $limit) {
            $remaining = min($distribution['articles'], $limit - $totalUsed);
            $this->info("\n▶ Translating Articles (limit: {$remaining})...");
            $this->call('articles:translate', ['--limit' => $remaining]);
            $totalUsed += $remaining;
        }
        
        // 2. مقالات الرأي (أولوية عالية)
        if ($totalUsed < $limit) {
            $remaining = min($distribution['opinions'], $limit - $totalUsed);
            $this->info("\n▶ Translating Opinions (limit: {$remaining})...");
            $this->call('opinions:translate', ['--limit' => $remaining]);
            $totalUsed += $remaining;
        }
        
        // 3. الكتّاب (أولوية عالية)
        if ($totalUsed < $limit) {
            $remaining = min($distribution['writers'], $limit - $totalUsed);
            $this->info("\n▶ Translating Writers (limit: {$remaining})...");
            $this->call('writers:translate', ['--limit' => $remaining]);
            $totalUsed += $remaining;
        }
        
        // 4. الفيديوهات (أولوية منخفضة)
        if ($totalUsed < $limit) {
            $remaining = min($distribution['videos'], $limit - $totalUsed);
            $this->info("\n▶ Translating Videos (limit: {$remaining})...");
            $this->call('videos:translate', ['--limit' => $remaining]);
            $totalUsed += $remaining;
        }
        
        $this->newLine();
        $this->info("✓ Daily translation completed!");
        $this->info("Total API requests used: {$totalUsed}/{$limit}");
        
        // عرض الإحصائيات
        $this->showProgress();
        
        return 0;
    }
    
    private function showProgress()
    {
        $this->newLine();
        $this->info("📊 Translation Progress:");
        $this->table(
            ['Type', 'Translated', 'Total', 'Progress'],
            [
                ['Videos', \App\Models\Video::whereNotNull('title_en')->count(), \App\Models\Video::count(), $this->getProgressBar(\App\Models\Video::class, 'title_en')],
                ['Writers', \App\Models\Writer::whereNotNull('name_en')->count(), \App\Models\Writer::count(), $this->getProgressBar(\App\Models\Writer::class, 'name_en')],
                ['Opinions', \App\Models\Opinion::whereNotNull('title_en')->count(), \App\Models\Opinion::count(), $this->getProgressBar(\App\Models\Opinion::class, 'title_en')],
                ['Articles', \App\Models\Article::whereNotNull('title_en')->count(), \App\Models\Article::count(), $this->getProgressBar(\App\Models\Article::class, 'title_en')],
            ]
        );
    }
    
    private function getProgressBar($model, $field)
    {
        $total = $model::count();
        $translated = $model::whereNotNull($field)->count();
        
        if ($total === 0) return '0%';
        
        $percentage = round(($translated / $total) * 100, 1);
        return "{$percentage}%";
    }
}
