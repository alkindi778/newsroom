<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use App\Support\CustomPathGenerator;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Helpers\MediaHelper;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Opinion extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'writer_id',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
        'keywords',
        'views',
        'likes',
        'shares',
        'is_featured',
        'allow_comments',
        'tags',
        'meta_data'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
        'tags' => 'array',
        'meta_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($opinion) {
            if (empty($opinion->slug)) {
                $opinion->slug = Str::slug($opinion->title);
            }
            
            if ($opinion->is_published && !$opinion->published_at) {
                $opinion->published_at = now();
            }
        });

        static::updating(function ($opinion) {
            if ($opinion->isDirty('title')) {
                $opinion->slug = Str::slug($opinion->title);
            }
            
            if ($opinion->isDirty('is_published') && $opinion->is_published && !$opinion->published_at) {
                $opinion->published_at = now();
            }
        });

        static::created(function ($opinion) {
            $opinion->writer->updateOpinionsCount();
        });

        static::deleted(function ($opinion) {
            $opinion->writer->updateOpinionsCount();
        });

        static::restored(function ($opinion) {
            $opinion->writer->updateOpinionsCount();
        });
    }

    /**
     * العلاقة مع الكاتب
     */
    public function writer(): BelongsTo
    {
        return $this->belongsTo(Writer::class);
    }

    /**
     * البحث في مقالات الرأي
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('excerpt', 'LIKE', "%{$search}%")
              ->orWhere('content', 'LIKE', "%{$search}%")
              ->orWhere('keywords', 'LIKE', "%{$search}%")
              ->orWhereHas('writer', function ($writerQuery) use ($search) {
                  $writerQuery->where('name', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * المقالات المنشورة
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * المقالات المسودة
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * المقالات المميزة
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * ترتيب حسب الأحدث
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * ترتيب حسب تاريخ النشر
     */
    public function scopeByPublishDate($query)
    {
        return $query->orderByDesc('published_at');
    }

    /**
     * ترتيب حسب المشاهدات
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('views');
    }

    /**
     * فلتر حسب الكاتب
     */
    public function scopeByWriter($query, $writerId)
    {
        return $query->where('writer_id', $writerId);
    }

    /**
     * زيادة عدد المشاهدات
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * زيادة عدد الإعجابات
     */
    public function incrementLikes()
    {
        $this->increment('likes');
    }

    /**
     * زيادة عدد المشاركات
     */
    public function incrementShares()
    {
        $this->increment('shares');
    }

    /**
     * الحصول على الصورة مع fallback
     */
    public function getImageUrlAttribute()
    {
        // استخدام Media Library أولاً
        $mediaUrl = MediaHelper::getImageUrl($this, MediaHelper::COLLECTION_OPINIONS);
        if ($mediaUrl) {
            return $mediaUrl;
        }
        
        // fallback للصور القديمة مع دعم WebP
        if (!empty($this->attributes['image'])) {
            $imagePath = $this->attributes['image'];
            // تحقق من وجود نسخة WebP
            $webpPath = $this->getWebPVersion($imagePath);
            $finalPath = $webpPath ?: $imagePath;
            
            if (file_exists(public_path('storage/' . $finalPath))) {
                return asset('storage/' . $finalPath);
            }
        }
        
        // إرجاع null إذا لم توجد صورة (بدلاً من الصورة الافتراضية)
        return null;
    }

    /**
     * الحصول على المقتطف
     */
    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        return Str::limit(strip_tags($this->content), 150);
    }

    /**
     * تحديد ما إذا كان يمكن تعديل المقال
     */
    public function canEdit()
    {
        return true; // يمكن إضافة منطق أكثر تعقيداً هنا
    }

    /**
     * تحديد ما إذا كان يمكن حذف المقال
     */
    public function canDelete()
    {
        return true; // يمكن إضافة منطق أكثر تعقيداً هنا
    }

    /**
     * الحصول على حالة النشر
     */
    public function getStatusAttribute()
    {
        if ($this->trashed()) {
            return 'deleted';
        }
        
        return $this->is_published ? 'published' : 'draft';
    }

    /**
     * الحصول على لون الحالة
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'published':
                return 'green';
            case 'draft':
                return 'yellow';
            case 'deleted':
                return 'red';
            default:
                return 'gray';
        }
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(MediaHelper::SIZE_THUMBNAIL)
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion(MediaHelper::SIZE_MEDIUM)
            ->width(400)
            ->height(300)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion(MediaHelper::SIZE_LARGE)
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion(MediaHelper::SIZE_HERO)
            ->width(1200)
            ->height(600)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaHelper::COLLECTION_OPINIONS)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        // Try Media Library thumbnail
        $thumbnail = MediaHelper::getThumbnailUrl($this, MediaHelper::COLLECTION_OPINIONS);
        if ($thumbnail) {
            return $thumbnail;
        }

        // Fallback to image with WebP support
        return $this->image_url;
    }

    /**
     * Get medium image URL
     */
    public function getMediumImageUrlAttribute(): ?string
    {
        return MediaHelper::getMediumUrl($this, MediaHelper::COLLECTION_OPINIONS);
    }

    /**
     * Get large image URL
     */
    public function getLargeImageUrlAttribute(): ?string
    {
        return MediaHelper::getLargeUrl($this, MediaHelper::COLLECTION_OPINIONS);
    }

    /**
     * Check if opinion has image
     */
    public function getHasImageAttribute(): bool
    {
        return MediaHelper::hasImage($this, MediaHelper::COLLECTION_OPINIONS);
    }

    /**
     * Get image attribute with WebP optimization
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? ($this->getWebPVersion($value) ?: $value) : null
        );
    }

    /**
     * استبدال امتداد الصورة بـ WebP والتحقق من وجودها
     */
    protected function getWebPVersion(string $imagePath): ?string
    {
        // استبدال الامتداد بـ .webp
        $webpPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $imagePath);
        
        // إذا لم يتغير المسار (أصلاً webp أو امتداد آخر)، إرجاع null
        if ($webpPath === $imagePath) {
            return null;
        }

        // التحقق من وجود ملف WebP
        $fullPath = storage_path('app/public/' . $webpPath);
        
        if (file_exists($fullPath)) {
            return $webpPath;
        }

        return null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'is_published'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'تم إضافة مقال رأي جديد',
                'updated' => 'تم تحديث مقال الرأي',
                'deleted' => 'تم حذف مقال الرأي',
                default => $eventName
            });
    }
}
