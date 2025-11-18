<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Helpers\MediaHelper;

class Writer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'bio',
        'image',
        'position',
        'specialization',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'website_url',
        'is_active',
        'opinions_count',
        'last_activity',
        'meta_data'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity' => 'datetime',
        'meta_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($writer) {
            if (empty($writer->slug)) {
                $writer->slug = Str::slug($writer->name);
            }
        });

        static::updating(function ($writer) {
            if ($writer->isDirty('name')) {
                $writer->slug = Str::slug($writer->name);
            }
        });
    }

    /**
     * العلاقة مع مقالات الرأي
     */
    public function opinions(): HasMany
    {
        return $this->hasMany(Opinion::class);
    }

    /**
     * مقالات الرأي المنشورة
     */
    public function publishedOpinions(): HasMany
    {
        return $this->opinions()->where('is_published', true)->latest('published_at');
    }

    /**
     * آخر مقالة رأي
     */
    public function latestOpinion()
    {
        return $this->opinions()->latest()->first();
    }

    /**
     * تحديث عدد المقالات
     */
    public function updateOpinionsCount()
    {
        $this->update([
            'opinions_count' => $this->opinions()->count(),
            'last_activity' => now()
        ]);
    }

    /**
     * البحث في الكُتاب
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('specialization', 'LIKE', "%{$search}%")
              ->orWhere('position', 'LIKE', "%{$search}%");
        });
    }

    /**
     * الكُتاب النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * ترتيب حسب النشاط
     */
    public function scopeByActivity($query)
    {
        return $query->orderByDesc('last_activity');
    }

    /**
     * الحصول على الصورة مع fallback
     */
    public function getImageUrlAttribute()
    {
        // استخدام Media Library أولاً
        $mediaUrl = MediaHelper::getImageUrl($this, MediaHelper::COLLECTION_WRITERS);
        if ($mediaUrl) {
            return $mediaUrl;
        }
        
        // fallback للصور القديمة
        if ($this->image) {
            // جميع الصور في storage/media/old_photos
            // المسار في DB: old_photos/filename.jpg
            // المسار الفعلي: storage/media/old_photos/filename.jpg
            return asset('storage/media/' . $this->image);
        }
        
        // Default avatar
        return asset('assets/images/default-avatar.png');
    }

    /**
     * الحصول على الروابط الاجتماعية
     */
    public function getSocialLinksAttribute()
    {
        $links = [];
        
        if ($this->facebook_url) {
            $links['facebook'] = $this->facebook_url;
        }
        
        if ($this->twitter_url) {
            $links['twitter'] = $this->twitter_url;
        }
        
        if ($this->linkedin_url) {
            $links['linkedin'] = $this->linkedin_url;
        }
        
        if ($this->website_url) {
            $links['website'] = $this->website_url;
        }
        
        return $links;
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(MediaHelper::SIZE_THUMBNAIL)
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion(MediaHelper::SIZE_MEDIUM)
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion(MediaHelper::SIZE_LARGE)
            ->width(600)
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
        $this->addMediaCollection(MediaHelper::COLLECTION_WRITERS)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return MediaHelper::getThumbnailUrl($this, MediaHelper::COLLECTION_WRITERS);
    }

    /**
     * Get medium image URL
     */
    public function getMediumImageUrlAttribute(): ?string
    {
        return MediaHelper::getMediumUrl($this, MediaHelper::COLLECTION_WRITERS);
    }

    /**
     * Check if writer has image
     */
    public function getHasImageAttribute(): bool
    {
        return MediaHelper::hasImage($this, MediaHelper::COLLECTION_WRITERS);
    }
}
