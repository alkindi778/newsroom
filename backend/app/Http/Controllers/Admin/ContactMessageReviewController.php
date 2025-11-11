<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageReviewController extends Controller
{
    /**
     * صفحة مراجعة الرسالة للمدراء (للموافقة أو الرفض)
     */
    public function show($id)
    {
        abort_unless(auth()->user()->can('assign_contact_messages'), 403, 'ليس لديك صلاحية لمراجعة الرسائل');
        
        $message = ContactMessage::with(['assignedUser', 'approver'])->findOrFail($id);
        
        // التأكد أن الرسالة محولة للمستخدم الحالي
        if ($message->assigned_to != auth()->id()) {
            abort(403, 'هذه الرسالة غير محولة لك');
        }
        
        // تحديد الرسالة كمقروءة
        if (!$message->read_at) {
            $message->markAsRead();
        }
        
        return view('admin.contact-messages.review', compact('message'));
    }
    
    /**
     * الموافقة على الرسالة
     */
    public function approve(Request $request, $id)
    {
        abort_unless(auth()->user()->can('assign_contact_messages'), 403, 'ليس لديك صلاحية للموافقة على الرسائل');
        
        $message = ContactMessage::findOrFail($id);
        
        // التأكد أن الرسالة محولة للمستخدم الحالي
        if ($message->assigned_to != auth()->id()) {
            abort(403, 'هذه الرسالة غير محولة لك');
        }
        
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);
        
        $message->approve(auth()->id(), $request->approval_notes);
        
        return redirect()->route('admin.contact-messages.review.index')
            ->with('success', 'تمت الموافقة على الرسالة بنجاح');
    }
    
    /**
     * رفض الرسالة
     */
    public function reject(Request $request, $id)
    {
        abort_unless(auth()->user()->can('assign_contact_messages'), 403, 'ليس لديك صلاحية لرفض الرسائل');
        
        $message = ContactMessage::findOrFail($id);
        
        // التأكد أن الرسالة محولة للمستخدم الحالي
        if ($message->assigned_to != auth()->id()) {
            abort(403, 'هذه الرسالة غير محولة لك');
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);
        
        $message->reject(auth()->id(), $request->rejection_reason);
        
        return redirect()->route('admin.contact-messages.review.index')
            ->with('success', 'تم رفض الرسالة');
    }
    
    /**
     * قائمة الرسائل المحولة للمستخدم الحالي (للمراجعة)
     */
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('assign_contact_messages'), 403, 'ليس لديك صلاحية لمراجعة الرسائل');
        
        $query = ContactMessage::with(['assignedUser', 'approver'])
            ->assignedTo(auth()->id())
            ->latest();
        
        // فلترة حسب حالة الموافقة
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }
        
        $messages = $query->paginate(20);
        $pendingCount = ContactMessage::assignedTo(auth()->id())->where('approval_status', 'forwarded')->count();
        
        return view('admin.contact-messages.my-reviews', compact('messages', 'pendingCount'));
    }
}