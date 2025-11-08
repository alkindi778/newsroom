<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
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
                $video->slug = Str::slug($video->title);
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
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        // Generate thumbnail from video type
        if ($this->video_type === 'youtube' && $this->video_id) {
            return "https://img.youtube.com/vi/{$this->video_id}/maxresdefault.jpg";
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
}
