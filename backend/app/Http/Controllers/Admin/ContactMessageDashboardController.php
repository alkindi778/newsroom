<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContactMessageDashboardController extends Controller
{
    /**
     * عرض لوحة الإحصائيات
     */
    public function index()
    {
        abort_unless(auth()->user()->canAny(['view_contact_dashboard', 'manage_contact_messages']), 403);

        // الإحصائيات العامة
        $stats = [
            'total' => ContactMessage::count(),
            'new' => ContactMessage::where('status', 'new')->count(),
            'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
            'closed' => ContactMessage::where('status', 'closed')->count(),
            'pending_approval' => ContactMessage::where('approval_status', 'forwarded')->count(),
            'approved' => ContactMessage::where('approval_status', 'approved')->count(),
            'rejected' => ContactMessage::where('approval_status', 'rejected')->count(),
        ];

        // إحصائيات هذا الشهر
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlyStats = [
            'received' => ContactMessage::where('created_at', '>=', $thisMonth)->count(),
            'responded' => ContactMessage::where('first_reply_at', '>=', $thisMonth)->count(),
            'closed' => ContactMessage::where('status', 'closed')
                ->where('updated_at', '>=', $thisMonth)->count(),
        ];

        // متوسط وقت الاستجابة (بالساعات)
        $avgResponseTime = ContactMessage::whereNotNull('first_reply_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_reply_at)) as avg_hours')
            ->value('avg_hours');

        // التصنيفات
        $categoryCounts = ContactMessage::whereNotNull('ai_category')
            ->select('ai_category', DB::raw('count(*) as count'))
            ->groupBy('ai_category')
            ->pluck('count', 'ai_category')
            ->toArray();

        // المشاعر
        $sentimentCounts = ContactMessage::whereNotNull('ai_sentiment')
            ->select('ai_sentiment', DB::raw('count(*) as count'))
            ->groupBy('ai_sentiment')
            ->pluck('count', 'ai_sentiment')
            ->toArray();

        // الرسائل اليومية (آخر 30 يوم)
        $dailyMessages = ContactMessage::where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // أنواع اللقاءات
        $meetingTypes = ContactMessage::select('meeting_type', DB::raw('count(*) as count'))
            ->groupBy('meeting_type')
            ->pluck('count', 'meeting_type')
            ->toArray();

        // الرسائل العاجلة
        $urgentMessages = ContactMessage::where(function ($q) {
                $q->where('priority', 'urgent')
                    ->orWhere('ai_sentiment', 'urgent')
                    ->orWhere('meeting_type', 'urgent');
            })
            ->where('status', '!=', 'closed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // أحدث الردود
        $recentReplies = ContactMessageReply::with(['contactMessage', 'user'])
            ->where('type', 'email')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // أفضل المستجيبين هذا الشهر
        $topResponders = ContactMessageReply::where('type', 'email')
            ->where('sent_successfully', true)
            ->where('created_at', '>=', $thisMonth)
            ->select('user_id', DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('user')
            ->get();

        return view('admin.contact-messages.dashboard', compact(
            'stats',
            'monthlyStats',
            'avgResponseTime',
            'categoryCounts',
            'sentimentCounts',
            'dailyMessages',
            'meetingTypes',
            'urgentMessages',
            'recentReplies',
            'topResponders'
        ));
    }

    /**
     * بيانات الرسم البياني (API)
     */
    public function chartData(Request $request)
    {
        abort_unless(auth()->user()->can('manage_contact_messages'), 403);

        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);

        $data = ContactMessage::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN status = "closed" THEN 1 ELSE 0 END) as closed'),
                DB::raw('SUM(CASE WHEN first_reply_at IS NOT NULL THEN 1 ELSE 0 END) as responded')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }
}
