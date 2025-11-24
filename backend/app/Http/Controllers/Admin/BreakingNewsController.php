<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BreakingNews;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BreakingNewsController extends Controller
{
    /**
     * الحصول على الأخبار العاجلة (AJAX)
     */
    public function index()
    {
        $breakingNews = BreakingNews::with(['creator', 'article'])
            ->ordered()
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'url' => $news->url,
                    'article_id' => $news->article_id,
                    'article_title' => $news->article?->title,
                    'is_active' => $news->is_active,
                    'is_expired' => $news->isExpired(),
                    'priority' => $news->priority,
                    'expires_at' => $news->expires_at?->format('Y-m-d H:i'),
                    'created_by' => $news->creator?->name,
                    'created_at' => $news->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $breakingNews,
        ]);
    }

    /**
     * إضافة خبر عاجل جديد (AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|url|max:500',
            'article_id' => 'nullable|exists:articles,id',
            'priority' => 'nullable|integer|min:0|max:100',
            'expires_at' => 'nullable|date|after:now',
        ], [
            'title.required' => 'عنوان الخبر العاجل مطلوب',
            'url.url' => 'الرابط غير صالح',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون في المستقبل',
        ]);

        try {
            $breakingNews = BreakingNews::create([
                'title' => $request->title,
                'url' => $request->url,
                'article_id' => $request->article_id,
                'created_by' => auth()->id(),
                'priority' => $request->priority ?? 0,
                'expires_at' => $request->expires_at,
                'is_active' => true,
            ]);

            // إرسال إشعار للمستخدمين
            $this->sendNotifications($breakingNews);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الخبر العاجل بنجاح',
                'data' => [
                    'id' => $breakingNews->id,
                    'title' => $breakingNews->title,
                    'is_active' => $breakingNews->is_active,
                    'created_by' => auth()->user()->name,
                    'created_at' => $breakingNews->created_at->diffForHumans(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في إضافة خبر عاجل: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الخبر العاجل',
            ], 500);
        }
    }

    /**
     * تبديل حالة الخبر العاجل (AJAX)
     */
    public function toggle($id)
    {
        try {
            $breakingNews = BreakingNews::findOrFail($id);
            $breakingNews->update(['is_active' => !$breakingNews->is_active]);

            return response()->json([
                'success' => true,
                'message' => $breakingNews->is_active ? 'تم تفعيل الخبر العاجل' : 'تم إيقاف الخبر العاجل',
                'is_active' => $breakingNews->is_active,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ',
            ], 500);
        }
    }

    /**
     * حذف خبر عاجل (AJAX)
     */
    public function destroy($id)
    {
        try {
            $breakingNews = BreakingNews::findOrFail($id);
            $breakingNews->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الخبر العاجل بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف',
            ], 500);
        }
    }

    /**
     * تحديث الأولوية (AJAX)
     */
    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|integer|min:0|max:100',
        ]);

        try {
            $breakingNews = BreakingNews::findOrFail($id);
            $breakingNews->update(['priority' => $request->priority]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الأولوية',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ',
            ], 500);
        }
    }

    /**
     * إرسال إشعارات بالخبر العاجل
     */
    private function sendNotifications(BreakingNews $breakingNews)
    {
        try {
            // إرسال إشعار لجميع المستخدمين الإداريين
            $admins = User::role(['Super Admin', 'Admin', 'Editor'])->get();
            
            foreach ($admins as $admin) {
                if ($admin->id === auth()->id()) continue; // لا ترسل للمنشئ
                
                Notification::create([
                    'type' => 'breaking_news',
                    'user_id' => $admin->id,
                    'title' => 'خبر عاجل جديد',
                    'message' => $breakingNews->title,
                    'icon' => 'zap',
                    'link' => '/admin/articles',
                    'data' => [
                        'breaking_news_id' => $breakingNews->id,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('فشل إرسال إشعارات الخبر العاجل: ' . $e->getMessage());
        }
    }

    /**
     * API للموقع الرئيسي - الأخبار العاجلة النشطة
     */
    public function getActive()
    {
        $breakingNews = BreakingNews::with('article:id,slug,title')
            ->active()
            ->ordered()
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'url' => $news->final_url,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $breakingNews,
        ]);
    }
}
