<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaPost extends Model
{
    protected $fillable = [
        'article_id',
        'video_id',
        'opinion_id',
        'infographic_id',
        'platform',
        'external_id',
        'message',
        'status',
        'error_message',
        'response',
        'published_at',
        'scheduled_for',
        'likes',
        'shares',
        'comments',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime',
    ];

    /**
     * العلاقة مع المقالة
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * العلاقة مع الفيديو
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * العلاقة مع مقال الرأي
     */
    public function opinion(): BelongsTo
    {
        return $this->belongsTo(Opinion::class);
    }

    /**
     * العلاقة مع الإنفوجرافيك
     */
    public function infographic(): BelongsTo
    {
        return $this->belongsTo(Infographic::class);
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_for', '<=', now());
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }
}
