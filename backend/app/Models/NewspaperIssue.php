<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewspaperIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'newspaper_name',
        'issue_number',
        'slug',
        'description',
        'pdf_url',
        'cover_image',
        'publication_date',
        'views',
        'downloads',
        'is_featured',
        'is_published',
        'user_id',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * العلاقات
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('publication_date', 'desc');
    }

    public function scopeMostViewed($query)
    {
        return $query->orderBy('views', 'desc');
    }

    /**
     * Accessors
     */
    public function getFormattedDateAttribute()
    {
        return $this->publication_date->format('d/m/Y');
    }

    /**
     * Increment views
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Increment downloads
     */
    public function incrementDownloads()
    {
        $this->increment('downloads');
    }
}
