<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

class Article extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, LogsActivity;
    protected $fillable = [
        'title',
        'title_en',
        'subtitle',
        'source',
        'slug',
        'content',
        'content_en',
        'image',
        'category_id',
        'user_id',
        'is_published',
        'published_at',
        'excerpt',
        'meta_description',
        'keywords',
        'views',
        'featured_image',
        'show_in_slider',
        'is_breaking_news',
        'approval_status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
        'show_in_slider' => 'boolean',
        'is_breaking_news' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title, '-');
            }
        });
    }

    /**
     * Get the category that owns the article
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who wrote the article
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the article
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the article
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the embedding for this article
     */
    public function embedding()
    {
        return $this->hasOne(ArticleEmbedding::class);
    }

    /**
     * Scope for pending approval articles
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending_approval');
    }

    /**
     * Scope for approved articles
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope for rejected articles
     */
    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    /**
     * Scope for user's own articles
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if article is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending_approval';
    }

    /**
     * Check if article is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if article is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Get the status attribute based on is_published
     */
    public function getStatusAttribute()
    {
        return $this->is_published ? 'published' : 'draft';
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
            ->nonQueued();
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaHelper::COLLECTION_ARTICLES)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    /**
     * Get main image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return MediaHelper::getImageUrl($this, MediaHelper::COLLECTION_ARTICLES);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return MediaHelper::getThumbnailUrl($this, MediaHelper::COLLECTION_ARTICLES);
    }

    /**
     * Get medium image URL
     */
    public function getMediumImageUrlAttribute(): ?string
    {
        return MediaHelper::getMediumUrl($this, MediaHelper::COLLECTION_ARTICLES);
    }

    /**
     * Get large image URL
     */
    public function getLargeImageUrlAttribute(): ?string
    {
        return MediaHelper::getLargeUrl($this, MediaHelper::COLLECTION_ARTICLES);
    }

    /**
     * Check if article has image
     */
    public function getHasImageAttribute(): bool
    {
        return MediaHelper::hasImage($this, MediaHelper::COLLECTION_ARTICLES);
    }

    /**
     * Get image path (fallback support for old and new media system)
     * Used by API to get image path consistently
     */
    public function getImagePathAttribute(): ?string
    {
        // Try Media Library first
        $mediaImage = MediaHelper::getImageUrl($this, MediaHelper::COLLECTION_ARTICLES);
        if ($mediaImage) {
            return $mediaImage;
        }

        // Fallback to old image column if exists
        if (!empty($this->attributes['image'])) {
            $imagePath = $this->attributes['image'];
            // تحقق من وجود نسخة WebP
            $webpPath = $this->getWebPVersion($imagePath);
            $finalPath = $webpPath ?: $imagePath;
            
            // إرجاع مسار نسبي بدلاً من URL كامل
            // Frontend سيضيف domain بنفسه
            return '/storage/' . $finalPath;
        }

        return null;
    }

    /**
     * Get thumbnail path (API support)
     */
    public function getThumbnailPathAttribute(): ?string
    {
        // Try Media Library thumbnail
        $thumbnail = MediaHelper::getThumbnailUrl($this, MediaHelper::COLLECTION_ARTICLES);
        if ($thumbnail) {
            return $thumbnail;
        }

        // Fallback to featured_image or image with WebP support
        if (!empty($this->attributes['featured_image'])) {
            $imagePath = $this->attributes['featured_image'];
            $webpPath = $this->getWebPVersion($imagePath);
            $finalPath = $webpPath ?: $imagePath;
            return '/storage/' . $finalPath;
        }

        // Final fallback to main image
        return $this->image_path;
    }

    /**
     * Get featured_image attribute with WebP optimization
     */
    protected function featuredImage(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? ($this->getWebPVersion($value) ?: $value) : null
        );
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
            ->logOnly(['title', 'is_published', 'approval_status', 'is_breaking_news'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'تم إنشاء خبر جديد',
                'updated' => 'تم تحديث الخبر',
                'deleted' => 'تم حذف الخبر',
                default => $eventName
            });
    }
}
