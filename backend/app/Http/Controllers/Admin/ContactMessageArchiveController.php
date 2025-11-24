<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactMessageArchiveController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * عرض قائمة الرسائل المؤرشفة
     */
    public function index(Request $request)
    {
        abort_unless(auth()->user()->canAny(['manage_contact_messages', 'view_contact_messages']), 403);

        $query = ContactMessage::with(['archiver', 'assignedUser'])
            ->archived()
            ->latest('archived_at');

        // فلترة حسب التصنيف
        if ($request->filled('category')) {
            $query->where('archive_category', $request->category);
        }

        // فلترة حسب الوسوم
        if ($request->filled('tag')) {
            $query->whereJsonContains('archive_tags', $request->tag);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('archive_summary', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20);

        // إحصائيات الأرشيف
        $stats = [
            'total' => ContactMessage::archived()->count(),
            'categories' => ContactMessage::archived()
                ->select('archive_category')
                ->selectRaw('count(*) as count')
                ->groupBy('archive_category')
                ->pluck('count', 'archive_category'),
        ];

        // جمع كل الوسوم الفريدة
        $allTags = ContactMessage::archived()
            ->whereNotNull('archive_tags')
            ->pluck('archive_tags')
            ->flatten()
            ->unique()
            ->values();

        return view('admin.contact-messages.archive.index', compact('messages', 'stats', 'allTags'));
    }

    /**
     * أرشفة رسالة مع تصنيف AI
     */
    public function archive(Request $request, $id)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $message = ContactMessage::findOrFail($id);

        if ($message->is_archived) {
            return back()->with('error', 'هذه الرسالة مؤرشفة بالفعل');
        }

        // تحليل وتصنيف بالذكاء الاصطناعي
        $aiAnalysis = $this->analyzeForArchive($message);

        $message->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
            'archive_category' => $aiAnalysis['category'] ?? 'general',
            'archive_summary' => $aiAnalysis['summary'] ?? null,
            'archive_tags' => $aiAnalysis['tags'] ?? [],
            'status' => 'closed',
        ]);

        return back()->with('success', 'تم أرشفة الرسالة بنجاح وتصنيفها تلقائياً');
    }

    /**
     * أرشفة عدة رسائل دفعة واحدة
     */
    public function bulkArchive(Request $request)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:contact_messages,id',
        ]);

        $messages = ContactMessage::whereIn('id', $request->message_ids)
            ->where('is_archived', false)
            ->get();

        $archived = 0;
        foreach ($messages as $message) {
            $aiAnalysis = $this->analyzeForArchive($message);

            $message->update([
                'is_archived' => true,
                'archived_at' => now(),
                'archived_by' => auth()->id(),
                'archive_category' => $aiAnalysis['category'] ?? 'general',
                'archive_summary' => $aiAnalysis['summary'] ?? null,
                'archive_tags' => $aiAnalysis['tags'] ?? [],
                'status' => 'closed',
            ]);
            $archived++;
        }

        return back()->with('success', "تم أرشفة {$archived} رسالة بنجاح");
    }

    /**
     * إلغاء أرشفة رسالة
     */
    public function unarchive($id)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $message = ContactMessage::findOrFail($id);

        if (!$message->is_archived) {
            return back()->with('error', 'هذه الرسالة غير مؤرشفة');
        }

        $message->update([
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'تم إلغاء أرشفة الرسالة');
    }

    /**
     * عرض رسالة مؤرشفة
     */
    public function show($id)
    {
        abort_unless(auth()->user()->canAny(['manage_contact_messages', 'view_contact_messages']), 403);

        $message = ContactMessage::with(['replies.user', 'archiver', 'assignedUser', 'approver'])
            ->archived()
            ->findOrFail($id);

        return view('admin.contact-messages.archive.show', compact('message'));
    }

    /**
     * تحديث تصنيف الأرشيف يدوياً
     */
    public function updateCategory(Request $request, $id)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $request->validate([
            'archive_category' => 'required|string|max:100',
            'archive_tags' => 'nullable|array',
        ]);

        $message = ContactMessage::archived()->findOrFail($id);

        $message->update([
            'archive_category' => $request->archive_category,
            'archive_tags' => $request->archive_tags ?? $message->archive_tags,
        ]);

        return back()->with('success', 'تم تحديث تصنيف الأرشيف');
    }

    /**
     * إعادة تحليل الرسالة بالذكاء الاصطناعي
     */
    public function reanalyze($id)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $message = ContactMessage::archived()->findOrFail($id);

        $aiAnalysis = $this->analyzeForArchive($message);

        $message->update([
            'archive_category' => $aiAnalysis['category'] ?? $message->archive_category,
            'archive_summary' => $aiAnalysis['summary'] ?? $message->archive_summary,
            'archive_tags' => $aiAnalysis['tags'] ?? $message->archive_tags,
        ]);

        return back()->with('success', 'تم إعادة تحليل الرسالة بنجاح');
    }

    /**
     * تحليل الرسالة للأرشفة باستخدام AI
     */
    protected function analyzeForArchive(ContactMessage $message): array
    {
        try {
            $prompt = <<<PROMPT
أنت مساعد متخصص في تصنيف وأرشفة رسائل التواصل.

قم بتحليل الرسالة التالية وأرجع النتيجة بتنسيق JSON فقط بدون أي نص إضافي:

الاسم: {$message->full_name}
البريد: {$message->email}
الموضوع: {$message->subject}
الرسالة: {$message->message}
نوع الرسالة: {$message->meeting_type}

أرجع JSON بالشكل التالي:
{
    "category": "تصنيف واحد من: استفسار_عام، شكوى، اقتراح، طلب_معلومات، تعاون_إعلامي، دعم_فني، أخرى",
    "summary": "ملخص مختصر للرسالة في جملة أو جملتين باللغة العربية",
    "tags": ["وسم1", "وسم2", "وسم3"] // 3-5 وسوم تصف محتوى الرسالة
}

مهم: أرجع JSON فقط بدون أي تفسير أو نص إضافي.
PROMPT;

            $response = $this->geminiService->generateContent($prompt);
            
            // تنظيف الاستجابة واستخراج JSON
            $cleanResponse = preg_replace('/```json\s*|\s*```/', '', $response);
            $cleanResponse = trim($cleanResponse);
            
            $result = json_decode($cleanResponse, true);
            
            if (json_last_error() === JSON_ERROR_NONE && $result) {
                return [
                    'category' => $result['category'] ?? 'أخرى',
                    'summary' => $result['summary'] ?? null,
                    'tags' => $result['tags'] ?? [],
                ];
            }
            
            Log::warning('AI archive analysis returned invalid JSON', ['response' => $response]);
            
        } catch (\Exception $e) {
            Log::error('AI archive analysis failed', ['error' => $e->getMessage()]);
        }

        // قيم افتراضية في حال الفشل
        return [
            'category' => $this->guessCategory($message),
            'summary' => mb_substr($message->message, 0, 150) . '...',
            'tags' => [],
        ];
    }

    /**
     * تخمين التصنيف بناءً على نوع الرسالة
     */
    protected function guessCategory(ContactMessage $message): string
    {
        return match($message->meeting_type) {
            'urgent' => 'شكوى',
            'private' => 'اقتراح',
            'general' => 'استفسار_عام',
            default => 'أخرى',
        };
    }

    /**
     * تصدير الأرشيف
     */
    public function export(Request $request)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $query = ContactMessage::archived()->with(['archiver']);

        if ($request->filled('category')) {
            $query->where('archive_category', $request->category);
        }

        if ($request->filled('from')) {
            $query->where('archived_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('archived_at', '<=', $request->to);
        }

        $messages = $query->get();

        $filename = 'archive_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($messages) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8
            
            fputcsv($file, ['الرقم', 'الاسم', 'البريد', 'الموضوع', 'التصنيف', 'الملخص', 'الوسوم', 'تاريخ الأرشفة']);
            
            foreach ($messages as $msg) {
                fputcsv($file, [
                    $msg->id,
                    $msg->full_name,
                    $msg->email,
                    $msg->subject,
                    $msg->archive_category,
                    $msg->archive_summary,
                    implode(', ', $msg->archive_tags ?? []),
                    $msg->archived_at?->format('Y-m-d H:i'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
