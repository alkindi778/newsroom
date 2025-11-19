<?php

namespace App\Services;

use App\Repositories\SmartSummaryRepository;
use App\Models\SmartSummary;

class CacheService
{
    private SmartSummaryRepository $repository;

    public function __construct(SmartSummaryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * استرجاع ملخص من الـ cache
     */
    public function getSummary(string $hash): ?array
    {
        $summary = $this->repository->findByHash($hash);
        
        if (!$summary) {
            return null;
        }

        // تسجيل الاستخدام
        $this->repository->recordUsage($summary);

        return [
            'summary' => $summary->summary,
            'type' => $summary->type,
            'length' => $summary->length,
            'word_count' => $summary->word_count,
            'compression_ratio' => $summary->compression_ratio,
            'original_length' => $summary->original_length,
            'usage_count' => $summary->usage_count + 1, // العدد الجديد
            'cached' => true,
        ];
    }

    /**
     * حفظ ملخص في الـ cache
     */
    public function storeSummary(array $data): SmartSummary
    {
        return $this->repository->createOrUpdate($data);
    }

    /**
     * إحصائيات الـ cache
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }

    /**
     * تنظيف الـ cache
     */
    public function cleanup(): array
    {
        return $this->repository->cleanup();
    }

    /**
     * الملخصات الحديثة
     */
    public function getRecentSummaries(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getRecent($limit);
    }

    /**
     * التحقق من وجود ملخص
     */
    public function exists(string $hash): bool
    {
        return $this->repository->findByHash($hash) !== null;
    }
}
