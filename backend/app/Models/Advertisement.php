<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Advertisement extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'position',
        'layout',
        'after_section_id',
        'content',
        'image',
        'link',
        'open_new_tab',
        'width',
        'height',
        'is_active',
        'start_date',
        'end_date',
        'target_pages',
        'target_categories',
        'target_devices',
        'views',
        'clicks',
        'priority',
        'client_name',
        'client_email',
        'client_phone',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_new_tab' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'target_pages' => 'array',
        'target_categories' => 'array',
        'target_devices' => 'array',
        'views' => 'integer',
        'clicks' => 'integer',
        'priority' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($advertisement) {
            if (empty($advertisement->slug)) {
                $advertisement->slug = static::generateUniqueSlug($advertisement->title);
            }
        });

        static::updating(function ($advertisement) {
            if ($advertisement->isDirty('title')) {
                $advertisement->slug = static::generateUniqueSlug($advertisement->title, $advertisement->id);
            }
        });
    }

    /**
     * Generate unique slug
     */
    protected static function generateUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('advertisements')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumbnail')
                    ->width(300)
                    ->height(200)
                    ->sharpen(10)
                    ->quality(80);

                $this->addMediaConversion('medium')
                    ->width(800)
                    ->height(600)
                    ->sharpen(10)
                    ->quality(85);

                $this->addMediaConversion('large')
                    ->width(1200)
                    ->height(800)
                    ->sharpen(10)
                    ->quality(90);
            });
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrentlyActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $now);
            });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeForPage($query, $page)
    {
        return $query->where(function ($q) use ($page) {
            $q->whereNull('target_pages')
              ->orWhereJsonContains('target_pages', $page);
        });
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where(function ($q) use ($categoryId) {
            $q->whereNull('target_categories')
              ->orWhereJsonContains('target_categories', $categoryId);
        });
    }

    public function scopeForDevice($query, $device)
    {
        return $query->where(function ($q) use ($device) {
            $q->whereNull('target_devices')
              ->orWhereJsonContains('target_devices', $device);
        });
    }

    public function scopeOrderByPriority($query, $direction = 'desc')
    {
        return $query->orderBy('priority', $direction);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('client_name', 'like', "%{$search}%");
        });
    }

    /**
     * Accessors
     */
    public function getImageUrlAttribute()
    {
        $media = $this->getFirstMedia('advertisements');
        if ($media) {
            return $media->getUrl();
        }
        
        // Fallback للصورة القديمة
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return asset('images/default-ad.jpg');
    }

    public function getThumbnailUrlAttribute()
    {
        $media = $this->getFirstMedia('advertisements');
        if ($media) {
            return $media->getUrl('thumbnail');
        }
        return $this->image_url;
    }

    public function getMediumImageUrlAttribute()
    {
        $media = $this->getFirstMedia('advertisements');
        if ($media) {
            return $media->getUrl('medium');
        }
        return $this->image_url;
    }

    public function getLargeImageUrlAttribute()
    {
        $media = $this->getFirstMedia('advertisements');
        if ($media) {
            return $media->getUrl('large');
        }
        return $this->image_url;
    }

    public function getStatusLabelAttribute()
    {
        if (!$this->is_active) {
            return 'معطل';
        }

        $now = now();
        
        if ($this->start_date && $this->start_date->isFuture()) {
            return 'مجدول';
        }
        
        if ($this->end_date && $this->end_date->isPast()) {
            return 'منتهي';
        }
        
        return 'نشط';
    }

    public function getStatusColorAttribute()
    {
        $status = $this->status_label;
        
        return match($status) {
            'نشط' => 'success',
            'مجدول' => 'info',
            'منتهي' => 'warning',
            'معطل' => 'danger',
            default => 'secondary',
        };
    }

    public function getCtrAttribute()
    {
        if ($this->views == 0) {
            return 0;
        }
        return round(($this->clicks / $this->views) * 100, 2);
    }

    /**
     * Mutators
     */
    public function setTargetPagesAttribute($value)
    {
        $this->attributes['target_pages'] = $value ? json_encode($value) : null;
    }

    public function setTargetCategoriesAttribute($value)
    {
        $this->attributes['target_categories'] = $value ? json_encode($value) : null;
    }

    public function setTargetDevicesAttribute($value)
    {
        $this->attributes['target_devices'] = $value ? json_encode($value) : null;
    }

    /**
     * Helper methods
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    /**
     * Relations
     */
    public function homepageSection()
    {
        return $this->belongsTo(HomepageSection::class, 'after_section_id');
    }

    public function categories()
    {
        if (!$this->target_categories) {
            return collect([]);
        }

        return Category::whereIn('id', $this->target_categories)->get();
    }
}
