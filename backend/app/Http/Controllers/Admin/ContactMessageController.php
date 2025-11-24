<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use App\Services\OfficeManagerAiService;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    protected $aiService;

    public function __construct(OfficeManagerAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * عرض قائمة الرسائل
     */
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view_contact_messages'), 403, 'ليس لديك صلاحية لعرض رسائل التواصل');
        
        // استبعاد الرسائل المؤرشفة
        $query = ContactMessage::with(['assignedUser', 'approver'])
            ->notArchived()
            ->latest();

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب نوع اللقاء
        if ($request->filled('meeting_type')) {
            $query->where('meeting_type', $request->meeting_type);
        }

        // فلترة حسب حالة الموافقة
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }
        
        // فلتر "رسائلي" - الرسائل المكلفة للمستخدم الحالي
        if ($request->filled('my_messages') && $request->my_messages == '1') {
            $query->assignedTo(auth()->id());
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20);
        // الإحصائيات تستثني المؤرشفة أيضاً
        $newCount = ContactMessage::notArchived()->where('status', 'new')->count();
        $unreadCount = ContactMessage::notArchived()->whereNull('read_at')->count();
        $myMessagesCount = ContactMessage::notArchived()->assignedTo(auth()->id())->where('approval_status', 'forwarded')->count();

        return view('admin.contact-messages.index', compact('messages', 'newCount', 'unreadCount', 'myMessagesCount'));
    }

    /**
     * عرض رسالة محددة
     */
    public function show($id)
    {
        abort_unless(auth()->user()->can('view_contact_messages'), 403, 'ليس لديك صلاحية لعرض رسائل التواصل');
        
        $message = ContactMessage::with(['assignedUser', 'approver', 'replies.user'])->findOrFail($id);
        
        // تحديد الرسالة كمقروءة
        if (!$message->read_at) {
            $message->markAsRead();
        }

        $users = User::where('status', true)->get();

        return view('admin.contact-messages.show', compact('message', 'users'));
    }

    /**
     * تحديث حالة الرسالة
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403, 'ليس لديك صلاحية لإدارة رسائل التواصل');
        
        $message = ContactMessage::findOrFail($id);

        $validationRules = [
            'status' => 'required|in:new,read,in_progress,closed',
            'admin_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'priority' => 'nullable|in:normal,high,urgent',
        ];

        // فقط المدراء يمكنهم تكليف الرسائل
        if (auth()->user()->can('assign_contact_messages')) {
            $validationRules['assigned_to'] = 'nullable|exists:users,id';
            $validationRules['forwarding_reason'] = 'nullable|string';
        }

        $request->validate($validationRules);

        $dataToUpdate = [
            'status' => $request->status, 
            'admin_notes' => $request->admin_notes,
            'internal_notes' => $request->internal_notes,
            'priority' => $request->priority ?? 'normal'
        ];
        
        // إضافة assigned_to فقط إذا كان المستخدم لديه الصلاحية
        if (auth()->user()->can('assign_contact_messages') && $request->has('assigned_to')) {
            $assignedTo = $request->assigned_to;
            
            // إذا تم التكليف لأول مرة أو تغيير المكلف، قم بتحويل الرسالة
            if ($assignedTo && $message->assigned_to != $assignedTo) {
                $message->forward($assignedTo, $request->forwarding_reason, $request->priority ?? 'normal');
            } else {
                $dataToUpdate['assigned_to'] = $assignedTo;
            }
        }

        $message->update($dataToUpdate);

        return redirect()->route('admin.contact-messages.show', $message->id)
            ->with('success', 'تم تحديث الرسالة بنجاح');
    }

    /**
     * تحليل الرسالة باستخدام الذكاء الاصطناعي
     */
    public function analyze(Request $request, $id)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403, 'ليس لديك صلاحية لاستخدام التحليل الذكي');

        $message = ContactMessage::findOrFail($id);

        try {
            $analysis = $this->aiService->analyzeContactMessage(
                $message->message,
                $message->full_name,
                $message->subject
            );

            if (!isset($analysis['summary']) || !isset($analysis['sentiment']) || !isset($analysis['suggested_reply'])) {
                throw new \Exception('التحليل لم يعد بالحقول المطلوبة');
            }

            $message->update([
                'ai_summary' => $analysis['summary'],
                'ai_sentiment' => $analysis['sentiment'],
                'ai_suggested_reply' => $analysis['suggested_reply'],
                'ai_category' => $analysis['category'] ?? 'other',
            ]);

            $message->refresh();

            return redirect()->route('admin.contact-messages.show', $message->id)
                ->with('success', 'تم تحليل الرسالة بنجاح');

        } catch (\Exception $e) {
            \Log::error('AI analysis failed', ['message_id' => $id, 'error' => $e->getMessage()]);
            
            return redirect()->route('admin.contact-messages.show', $message->id)
                ->with('error', 'فشل تحليل الرسالة: ' . $e->getMessage());
        }
    }

    /**
     * حذف رسالة
     */
    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete_contact_messages'), 403, 'ليس لديك صلاحية لحذف رسائل التواصل');
        
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'تم حذف الرسالة بنجاح');
    }

    /**
     * تحديد رسائل متعددة كمقروءة
     */
    public function markAsRead(Request $request)
    {
        $ids = $request->input('ids', []);
        
        ContactMessage::whereIn('id', $ids)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'status' => 'read'
            ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الرسائل كمقروءة'
        ]);
    }

    /**
     * إحصائيات الرسائل
     */
    public function statistics()
    {
        $stats = [
            'total' => ContactMessage::count(),
            'new' => ContactMessage::where('status', 'new')->count(),
            'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
            'closed' => ContactMessage::where('status', 'closed')->count(),
            'urgent' => ContactMessage::where('meeting_type', 'urgent')->count(),
        ];

        return response()->json($stats);
    }
    
    /**
     * الموافقة على الرسالة
     */
    public function approve(Request $request, $id)
    {
        abort_unless(auth()->user()->can('assign_contact_messages'), 403, 'ليس لديك صلاحية للموافقة على الرسائل');
        
        $message = ContactMessage::findOrFail($id);
        
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);
        
        $message->approve(auth()->id(), $request->approval_notes);
        
        return redirect()->route('admin.contact-messages.show', $message->id)
            ->with('success', 'تمت الموافقة على الرسالة بنجاح');
    }
    
    /**
     * رفض الرسالة
     */
    public function reject(Request $request, $id)
    {
        abort_unless(auth()->user()->can('assign_contact_messages'), 403, 'ليس لديك صلاحية لرفض الرسائل');
        
        $message = ContactMessage::findOrFail($id);
        
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);
        
        $message->reject(auth()->id(), $request->rejection_reason);
        
        return redirect()->route('admin.contact-messages.show', $message->id)
            ->with('success', 'تم رفض الرسالة');
    }
}
