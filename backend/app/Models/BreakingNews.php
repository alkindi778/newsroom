<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakingNews extends Model
{
    use HasFactory;

    protected $table = 'breaking_news';

    protected $fillable = [
        'title',
        'url',
        'article_id',
        'created_by',
        'is_active',
        'priority',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * العلاقة مع المقال
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * العلاقة مع المستخدم المنشئ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope للأخبار النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope للترتيب حسب الأولوية
     */
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('priority')->orderByDesc('created_at');
    }

    /**
     * هل الخبر منتهي الصلاحية؟
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * الحصول على الرابط النهائي
     */
    public function getFinalUrlAttribute(): ?string
    {
        if ($this->url) {
            return $this->url;
        }
        
        if ($this->article) {
            return route('news.show', $this->article->slug);
        }
        
        return null;
    }
}
