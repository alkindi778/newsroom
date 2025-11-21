<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Infographic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'description',
        'description_en',
        'image',
        'slug',
        'category_id',
        'is_featured',
        'is_active',
        'views',
        'order',
        'tags',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'views' => 'integer',
        'order' => 'integer',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع القسم
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope للحصول على الإنفوجرافيكات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للحصول على الإنفوجرافيكات المميزة
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope للحصول على الإنفوجرافيكات مرتبة
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * توليد slug تلقائياً عند الإنشاء
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($infographic) {
            if (empty($infographic->slug)) {
                $infographic->slug = Str::slug($infographic->title);
                
                // التحقق من عدم تكرار الـ slug
                $count = 1;
                $originalSlug = $infographic->slug;
                while (static::where('slug', $infographic->slug)->exists()) {
                    $infographic->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });

        // زيادة عدد المشاهدات تلقائياً
        static::retrieved(function ($infographic) {
            // يمكن تفعيل هذا لاحقاً إذا أردت حساب المشاهدات
            // $infographic->increment('views');
        });
    }

    /**
     * الحصول على رابط الصورة الكامل
     */
    public function getImageUrlAttribute(): string
    {
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        
        return asset('storage/' . $this->image);
    }

    /**
     * زيادة عدد المشاهدات
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
