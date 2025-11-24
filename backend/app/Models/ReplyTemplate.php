<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'category',
        'is_active',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    /**
     * العلاقة مع المستخدم الذي أنشأ القالب
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope للقوالب النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope حسب التصنيف
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * زيادة عدد مرات الاستخدام
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * استبدال المتغيرات في القالب
     */
    public function parseContent(ContactMessage $message): string
    {
        $replacements = [
            '{name}' => $message->full_name,
            '{subject}' => $message->subject,
            '{email}' => $message->email,
            '{phone}' => $message->phone,
            '{date}' => $message->created_at->format('Y-m-d'),
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $this->content
        );
    }

    /**
     * استبدال المتغيرات في العنوان
     */
    public function parseSubject(ContactMessage $message): string
    {
        $replacements = [
            '{name}' => $message->full_name,
            '{subject}' => $message->subject,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $this->subject ?? ''
        );
    }

    /**
     * الحصول على تسمية التصنيف
     */
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'acknowledgment' => 'تأكيد استلام',
            'followup' => 'متابعة',
            'rejection' => 'اعتذار',
            'approval' => 'موافقة',
            'general' => 'عام',
            default => 'غير محدد',
        };
    }

    /**
     * الحصول على لون التصنيف
     */
    public function getCategoryColorAttribute()
    {
        return match($this->category) {
            'acknowledgment' => 'blue',
            'followup' => 'yellow',
            'rejection' => 'red',
            'approval' => 'green',
            'general' => 'gray',
            default => 'gray',
        };
    }
}
