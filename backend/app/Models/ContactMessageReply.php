<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessageReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_message_id',
        'user_id',
        'type',
        'content',
        'subject',
        'sent_successfully',
        'sent_at',
        'email_message_id',
    ];

    protected $casts = [
        'sent_successfully' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * العلاقة مع الرسالة الأصلية
     */
    public function contactMessage()
    {
        return $this->belongsTo(ContactMessage::class);
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope للردود عبر البريد
     */
    public function scopeEmails($query)
    {
        return $query->where('type', 'email');
    }

    /**
     * Scope للملاحظات الداخلية
     */
    public function scopeInternalNotes($query)
    {
        return $query->where('type', 'internal_note');
    }

    /**
     * Scope للرسائل المرسلة بنجاح
     */
    public function scopeSentSuccessfully($query)
    {
        return $query->where('sent_successfully', true);
    }

    /**
     * الحصول على اسم أيقونة نوع الرد
     */
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'email' => 'mail',
            'internal_note' => 'pencil',
            'system' => 'cpu',
            default => 'chat',
        };
    }

    /**
     * الحصول على أيقونة SVG لنوع الرد
     */
    public function getTypeIconSvgAttribute()
    {
        $icons = [
            'email' => '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
            'internal_note' => '<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
            'system' => '<svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
        ];
        
        return $icons[$this->type] ?? '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>';
    }

    /**
     * الحصول على تسمية نوع الرد
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'email' => 'بريد إلكتروني',
            'internal_note' => 'ملاحظة داخلية',
            'system' => 'نظام',
            default => 'غير محدد',
        };
    }
}
