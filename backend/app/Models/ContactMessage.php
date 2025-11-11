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
        'admin_notes',
        'assigned_to',
        'read_at',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'approval_notes',
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
    public function forward($userId)
    {
        $this->update([
            'assigned_to' => $userId,
            'approval_status' => 'forwarded',
            'status' => 'in_progress'
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
}
