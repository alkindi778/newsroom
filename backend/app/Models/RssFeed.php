<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RssFeed extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'language',
        'items_count',
        'ttl',
        'copyright',
        'image_url',
        'is_active',
        'last_generated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_generated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($feed) {
            if (empty($feed->slug)) {
                $feed->slug = Str::slug($feed->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getUrlAttribute(): string
    {
        return route('rss.show', $this->slug);
    }
}
