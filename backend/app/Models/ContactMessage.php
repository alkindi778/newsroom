<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'meeting_type',
        'subject',
        'message',
        'status',
        'priority',
        'admin_notes',
        'internal_notes',
        'assigned_to',
        'forwarding_reason',
        'read_at',
        'first_reply_at',
        'replies_count',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'approval_notes',
        'ai_summary',
        'ai_sentiment',
        'ai_suggested_reply',
        'ai_category',
        // حقول الأرشفة
        'is_archived',
        'archived_at',
        'archived_by',
        'archive_category',
        'archive_summary',
        'archive_tags',
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
        'first_reply_at' => 'datetime',
        'approved_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'replies_count' => 'integer',
        'is_archived' => 'boolean',
        'archive_tags' => 'array',
    ];
    
    /**
     * العلاقة مع المستخدم المكلف
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * العلاقة مع المستخدم الذي وافق/رفض
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * العلاقة مع المستخدم الذي أرشف
     */
    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
    
    /**
     * Scope للرسائل المؤرشفة
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }
    
    /**
     * Scope للرسائل غير المؤرشفة
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }
    
    /**
     * Scope للرسائل الجديدة
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }
    
    /**
     * Scope للرسائل غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
    
    /**
     * تحديد الرسالة كمقروءة
     */
    public function markAsRead()
    {
        $this->update([
            'read_at' => now(),
            'status' => 'read'
        ]);
    }
    
    /**
     * الموافقة على الرسالة
     */
    public function approve($userId, $notes = null)
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'status' => 'in_progress'
        ]);
    }
    
    /**
     * رفض الرسالة
     */
    public function reject($userId, $reason = null)
    {
        $this->update([
            'approval_status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
            'rejection_reason' => $reason,
            'status' => 'closed'
        ]);
    }
    
    /**
     * تحويل الرسالة
     */
    public function forward($userId, $reason = null, $priority = 'normal')
    {
        $this->update([
            'assigned_to' => $userId,
            'approval_status' => 'forwarded',
            'status' => 'in_progress',
            'forwarding_reason' => $reason,
            'priority' => $priority
        ]);
    }
    
    /**
     * Scope للرسائل المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }
    
    /**
     * Scope للرسائل المحولة
     */
    public function scopeForwarded($query)
    {
        return $query->where('approval_status', 'forwarded');
    }
    
    /**
     * Scope للرسائل الموافق عليها
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }
    
    /**
     * Scope للرسائل المرفوضة
     */
    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }
    
    /**
     * Scope للرسائل المكلفة لمستخدم معين
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * العلاقة مع الردود
     */
    public function replies()
    {
        return $this->hasMany(ContactMessageReply::class)->orderBy('created_at', 'desc');
    }

    /**
     * الحصول على آخر رد
     */
    public function latestReply()
    {
        return $this->hasOne(ContactMessageReply::class)->latestOfMany();
    }

    /**
     * الحصول على وقت الاستجابة بالساعات
     */
    public function getResponseTimeHoursAttribute()
    {
        if (!$this->first_reply_at) {
            return null;
        }
        return $this->created_at->diffInHours($this->first_reply_at);
    }

    /**
     * تسمية التصنيف
     */
    public function getAiCategoryLabelAttribute()
    {
        return match($this->ai_category) {
            'complaint' => 'شكوى',
            'inquiry' => 'استفسار',
            'meeting_request' => 'طلب لقاء',
            'suggestion' => 'اقتراح',
            'praise' => 'إشادة',
            'other' => 'أخرى',
            default => 'غير مصنف',
        };
    }

    /**
     * أيقونة التصنيف (اسم الأيقونة)
     */
    public function getAiCategoryIconAttribute()
    {
        return match($this->ai_category) {
            'complaint' => 'exclamation-circle',
            'inquiry' => 'question-mark-circle',
            'meeting_request' => 'users',
            'suggestion' => 'light-bulb',
            'praise' => 'star',
            'other' => 'document-text',
            default => 'mail',
        };
    }

    /**
     * SVG أيقونة التصنيف كاملة
     */
    public function getAiCategoryIconSvgAttribute()
    {
        $icons = [
            'complaint' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'inquiry' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'meeting_request' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
            'suggestion' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>',
            'praise' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>',
            'other' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        ];
        
        return $icons[$this->ai_category] ?? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>';
    }

    /**
     * لون التصنيف
     */
    public function getAiCategoryColorAttribute()
    {
        return match($this->ai_category) {
            'complaint' => 'red',
            'inquiry' => 'blue',
            'meeting_request' => 'purple',
            'suggestion' => 'yellow',
            'praise' => 'green',
            'other' => 'gray',
            default => 'gray',
        };
    }

    /**
     * تسمية المشاعر
     */
    public function getAiSentimentLabelAttribute()
    {
        return match($this->ai_sentiment) {
            'positive' => 'إيجابي',
            'negative' => 'سلبي',
            'neutral' => 'محايد',
            'urgent' => 'عاجل',
            default => 'غير محدد',
        };
    }

    /**
     * لون المشاعر
     */
    public function getAiSentimentColorAttribute()
    {
        return match($this->ai_sentiment) {
            'positive' => 'green',
            'negative' => 'red',
            'neutral' => 'gray',
            'urgent' => 'orange',
            default => 'gray',
        };
    }

    /**
     * SVG أيقونة المشاعر
     */
    public function getAiSentimentIconSvgAttribute()
    {
        $icons = [
            'positive' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'negative' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'neutral' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'urgent' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
        ];
        
        return $icons[$this->ai_sentiment] ?? $icons['neutral'];
    }
}
