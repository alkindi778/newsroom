<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'title',
        'title_en',
        'subtitle',
        'subtitle_en',
        'category_id',
        'order',
        'items_count',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'order' => 'integer',
        'items_count' => 'integer',
    ];

    /**
     * العلاقة مع القسم
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope للحصول على الأقسام النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للحصول على الأقسام مرتبة
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * الأنواع المتاحة للأقسام
     */
    public static function getAvailableTypes(): array
    {
        return [
            'slider' => 'السلايدر الرئيسي',
            'breaking_news' => 'الأخبار العاجلة',
            'latest_news' => 'آخر الأخبار',
            'category_news' => 'أخبار قسم معين',
            'trending' => 'الأكثر قراءة',
            'opinions' => 'مقالات الرأي',
            'videos' => 'الفيديوهات',
            'newspaper_issues' => 'إصدارات الصحف',
        ];
    }

    /**
     * الحصول على اسم النوع بالعربية
     */
    public function getTypeNameAttribute(): string
    {
        return self::getAvailableTypes()[$this->type] ?? $this->type;
    }
}
