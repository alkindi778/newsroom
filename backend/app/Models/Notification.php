<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'user_id',
        'sender_id',
        'title',
        'message',
        'icon',
        'link',
        'data',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * أنواع الإشعارات
     */
    const TYPE_ARTICLE_CREATED = 'article_created';
    const TYPE_ARTICLE_PENDING = 'article_pending';
    const TYPE_ARTICLE_APPROVED = 'article_approved';
    const TYPE_ARTICLE_REJECTED = 'article_rejected';
    const TYPE_OPINION_CREATED = 'opinion_created';
    const TYPE_COMMENT_NEW = 'comment_new';
    const TYPE_USER_REGISTERED = 'user_registered';
    const TYPE_SYSTEM = 'system';

    /**
     * المستخدم المستلم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * المستخدم المرسل
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Scope للإشعارات غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope للإشعارات المقروءة
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope حسب النوع
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope حسب المستخدم
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = now();
            return $this->save();
        }
        return true;
    }

    /**
     * تحديد الإشعار كغير مقروء
     */
    public function markAsUnread(): bool
    {
        if ($this->is_read) {
            $this->is_read = false;
            $this->read_at = null;
            return $this->save();
        }
        return true;
    }

    /**
     * الحصول على الأيقونة الافتراضية حسب النوع
     */
    public function getDefaultIcon(): string
    {
        return match($this->type) {
            self::TYPE_ARTICLE_CREATED => 'newspaper',
            self::TYPE_ARTICLE_PENDING => 'clock',
            self::TYPE_ARTICLE_APPROVED => 'check-circle',
            self::TYPE_ARTICLE_REJECTED => 'x-circle',
            self::TYPE_OPINION_CREATED => 'edit',
            self::TYPE_COMMENT_NEW => 'message-square',
            self::TYPE_USER_REGISTERED => 'user-plus',
            self::TYPE_SYSTEM => 'bell',
            default => 'bell',
        };
    }

    /**
     * الحصول على اللون حسب النوع
     */
    public function getTypeColor(): string
    {
        return match($this->type) {
            self::TYPE_ARTICLE_CREATED => 'blue',
            self::TYPE_ARTICLE_PENDING => 'yellow',
            self::TYPE_ARTICLE_APPROVED => 'green',
            self::TYPE_ARTICLE_REJECTED => 'red',
            self::TYPE_OPINION_CREATED => 'purple',
            self::TYPE_COMMENT_NEW => 'indigo',
            self::TYPE_USER_REGISTERED => 'teal',
            self::TYPE_SYSTEM => 'gray',
            default => 'gray',
        };
    }

    /**
     * الحصول على الوقت منذ الإنشاء
     */
    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }
}
