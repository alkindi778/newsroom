<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SmartSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_hash',
        'original_content_sample',
        'summary',
        'type',
        'length',
        'word_count',
        'compression_ratio',
        'original_length',
        'usage_count',
        'last_used_at'
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العثور على ملخص بواسطة hash
     */
    public static function findByHash(string $hash): ?self
    {
        return self::where('content_hash', $hash)->first();
    }

    /**
     * إنشاء ملخص جديد أو تحديث الموجود
     */
    public static function createOrUpdate(array $data): self
    {
        $summary = self::updateOrCreate(
            ['content_hash' => $data['content_hash']],
            $data
        );
        
        return $summary;
    }

    /**
     * تسجيل استخدام الملخص
     */
    public function recordUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * تنظيف الملخصات القديمة
     */
    public static function cleanupOld(): void
    {
        // حذف الملخصات الأقدم من 30 يوم والتي لم تستخدم كثيراً
        self::where('created_at', '<', Carbon::now()->subDays(30))
            ->where('usage_count', '<', 3)
            ->delete();
        
        // الاحتفاظ بأحدث 5000 ملخص فقط
        $totalCount = self::count();
        if ($totalCount > 5000) {
            $idsToDelete = self::orderBy('last_used_at', 'asc')
                ->limit($totalCount - 5000)
                ->pluck('id');
            
            self::whereIn('id', $idsToDelete)->delete();
        }
    }

    /**
     * إحصائيات الملخصات
     */
    public static function getStats(): array
    {
        return [
            'total_summaries' => self::count(),
            'today_summaries' => self::whereDate('created_at', today())->count(),
            'total_usage' => self::sum('usage_count'),
            'popular_types' => self::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }
}
