<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Helpers\MediaHelper;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'avatar',
        'phone',
        'bio',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'status' => 'boolean',
        ];
    }

    /**
     * Get articles written by user
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get avatar URL with fallback
     */
    public function getAvatarUrlAttribute()
    {
        // استخدام Media Library أولاً
        $mediaUrl = MediaHelper::getImageUrl($this, MediaHelper::COLLECTION_USERS);
        if ($mediaUrl) {
            return $mediaUrl;
        }
        
        // fallback للصور القديمة
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        
        // Default avatar
        return asset('assets/images/default-avatar.png');
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
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaHelper::COLLECTION_USERS)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return MediaHelper::getThumbnailUrl($this, MediaHelper::COLLECTION_USERS);
    }

    /**
     * Check if user has avatar
     */
    public function getHasAvatarAttribute(): bool
    {
        return MediaHelper::hasImage($this, MediaHelper::COLLECTION_USERS);
    }
}
