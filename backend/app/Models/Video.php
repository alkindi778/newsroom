<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use App\Support\CustomPathGenerator;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Video extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'video_url',
        'thumbnail',
        'duration',
        'video_type',
        'video_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views',
        'likes',
        'shares',
        'is_published',
        'is_featured',
        'published_at',
        'user_id',
        'section_title',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
        'likes' => 'integer',
        'shares' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (empty($video->slug)) {
                $slug = Str::slug($video->title);
                $originalSlug = $slug;
                $count = 1;
                
                // تحقق من وجود slug (بما في ذلك المحذوفة soft deleted)
                while (static::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $video->slug = $slug;
            }
        });
    }

    /**
     * Get the user that created the video
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Published videos
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope: Featured videos
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Recent videos
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Scope: Most viewed videos
     */
    public function scopeMostViewed($query)
    {
        return $query->orderBy('views', 'desc');
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        // دعم WebP للصور المحلية
        if (!empty($this->attributes['thumbnail'])) {
            $thumbnailPath = $this->attributes['thumbnail'];
            // تحقق من وجود نسخة WebP
            $webpPath = $this->getWebPVersion($thumbnailPath);
            $finalPath = $webpPath ?: $thumbnailPath;
            return asset('storage/' . $finalPath);
        }

        // Generate thumbnail from video type
        if ($this->video_type === 'youtube' && $this->video_id) {
            return \App\Services\YouTubeThumbnailService::getBestThumbnail($this->video_id);
        }

        if ($this->video_type === 'vimeo' && $this->video_id) {
            return "https://vumbnail.com/{$this->video_id}.jpg";
        }

        return asset('images/default-video-thumbnail.jpg');
    }

    /**
     * Get embed URL
     */
    public function getEmbedUrlAttribute()
    {
        if ($this->video_type === 'youtube' && $this->video_id) {
            // Simple YouTube embed URL - works best for compatibility
            return "https://www.youtube.com/embed/{$this->video_id}";
        }

        if ($this->video_type === 'vimeo' && $this->video_id) {
            return "https://player.vimeo.com/video/{$this->video_id}";
        }

        return $this->video_url;
    }

    /**
     * Get watch URL (direct link to video)
     */
    public function getWatchUrlAttribute()
    {
        if ($this->video_type === 'youtube' && $this->video_id) {
            return "https://www.youtube.com/watch?v={$this->video_id}";
        }

        if ($this->video_type === 'vimeo' && $this->video_id) {
            return "https://vimeo.com/{$this->video_id}";
        }

        return $this->video_url;
    }

    /**
     * Get thumbnail attribute with WebP optimization
     */
    protected function thumbnail(): Attribute
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
            ->logOnly(['title', 'video_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'تم إضافة فيديو جديد',
                'updated' => 'تم تحديث الفيديو',
                'deleted' => 'تم حذف الفيديو',
                default => $eventName
            });
    }
}
