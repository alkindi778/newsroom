<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
        'preferences',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'preferences' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم (اختياري)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope للاشتراكات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للحصول على اشتراكات مستخدم معين
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * تحديث آخر استخدام
     */
    public function updateLastUsed()
    {
        $this->last_used_at = now();
        $this->save();
    }

    /**
     * تعطيل الاشتراك
     */
    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * تفعيل الاشتراك
     */
    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }
}
