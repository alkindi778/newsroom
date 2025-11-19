<?php

namespace App\Repositories;

use App\Models\SmartSummary;
use Carbon\Carbon;

class SmartSummaryRepository
{
    /**
     * البحث عن ملخص بواسطة hash
     */
    public function findByHash(string $hash): ?SmartSummary
    {
        return SmartSummary::where('content_hash', $hash)->first();
    }

    /**
     * إنشاء أو تحديث ملخص
     */
    public function createOrUpdate(array $data): SmartSummary
    {
        return SmartSummary::updateOrCreate(
            ['content_hash' => $data['content_hash']],
            array_merge($data, ['last_used_at' => now()])
        );
    }

    /**
     * تسجيل استخدام ملخص
     */
    public function recordUsage(SmartSummary $summary): void
    {
        $summary->increment('usage_count');
        $summary->update(['last_used_at' => now()]);
    }

    /**
     * الحصول على إحصائيات
     */
    public function getStatistics(): array
    {
        return [
            'total_summaries' => SmartSummary::count(),
            'today_summaries' => SmartSummary::whereDate('created_at', today())->count(),
            'total_usage' => SmartSummary::sum('usage_count'),
            'most_popular_type' => $this->getMostPopularType(),
            'cache_efficiency' => $this->calculateCacheEfficiency(),
        ];
    }

    /**
     * تنظيف الملخصات القديمة
     */
    public function cleanup(): array
    {
        $oldSummaries = SmartSummary::where('created_at', '<', Carbon::now()->subDays(30))
            ->where('usage_count', '<', 3);
        
        $deletedOld = $oldSummaries->count();
        $oldSummaries->delete();

        // الاحتفاظ بأحدث 5000 ملخص فقط
        $totalCount = SmartSummary::count();
        $deletedExcess = 0;
        
        if ($totalCount > 5000) {
            $idsToDelete = SmartSummary::orderBy('last_used_at', 'asc')
                ->limit($totalCount - 5000)
                ->pluck('id');
            
            $deletedExcess = SmartSummary::whereIn('id', $idsToDelete)->delete();
        }

        return [
            'deleted_old' => $deletedOld,
            'deleted_excess' => $deletedExcess,
            'remaining' => SmartSummary::count()
        ];
    }

    /**
     * الحصول على الملخصات الحديثة
     */
    public function getRecent(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return SmartSummary::select([
                'id', 'content_hash', 'original_content_sample', 
                'type', 'length', 'word_count', 'usage_count', 
                'created_at', 'last_used_at'
            ])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * النوع الأكثر شعبية
     */
    private function getMostPopularType(): ?string
    {
        return SmartSummary::selectRaw('type, SUM(usage_count) as total_usage')
            ->groupBy('type')
            ->orderBy('total_usage', 'desc')
            ->first()
            ?->type;
    }

    /**
     * حساب كفاءة الـ cache
     */
    private function calculateCacheEfficiency(): float
    {
        $totalUsage = SmartSummary::sum('usage_count');
        $totalSummaries = SmartSummary::count();
        
        return $totalSummaries > 0 ? round(($totalUsage / $totalSummaries), 2) : 0.0;
    }
}
