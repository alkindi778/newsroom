<?php

/**
 * أمثلة على استخدام نظام الترجمة في API Controllers
 * 
 * هذا الملف يحتوي على أمثلة عملية لكيفية استخدام نظام الترجمة
 * في Controllers الخاصة بك
 */

namespace App\Examples;

use App\Models\Article;
use App\Jobs\TranslateContentJob;
use App\Services\GeminiTranslationService;
use Illuminate\Http\Request;

class TranslationExamples
{
    /**
     * مثال 1: إنشاء مقال جديد - الترجمة التلقائية
     * 
     * الترجمة ستحدث تلقائياً بفضل ArticleObserver
     * لا حاجة لعمل أي شيء إضافي!
     */
    public function createArticleWithAutoTranslation(Request $request)
    {
        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
        ]);

        // ✅ TranslateContentJob تم dispatch تلقائياً!
        
        return response()->json([
            'success' => true,
            'message' => 'Article created. Translation in progress.',
            'article' => $article,
        ], 201);
    }

    /**
     * مثال 2: الحصول على مقال مع الترجمة
     */
    public function getArticleWithTranslation($id)
    {
        $article = Article::findOrFail($id);

        return response()->json([
            'success' => true,
            'article' => [
                'id' => $article->id,
                
                // العربي
                'title' => $article->title,
                'content' => $article->content,
                
                // الإنجليزي
                'title_en' => $article->title_en,
                'content_en' => $article->content_en,
                
                // حالة الترجمة
                'is_translated' => !empty($article->title_en) && !empty($article->content_en),
                
                // المزيد من الحقول
                'category' => $article->category,
                'user' => $article->user,
                'created_at' => $article->created_at,
            ]
        ]);
    }

    /**
     * مثال 3: إعادة ترجمة مقال يدوياً
     */
    public function retranslateArticle($id)
    {
        $article = Article::findOrFail($id);

        // التحقق من الصلاحيات
        if (!auth()->user()->can('edit articles')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // إطلاق الترجمة
        TranslateContentJob::dispatch($article);

        return response()->json([
            'success' => true,
            'message' => 'Re-translation job dispatched successfully'
        ]);
    }

    /**
     * مثال 4: الحصول على جميع المقالات مع الترجمات
     */
    public function getAllArticlesWithTranslations(Request $request)
    {
        $articles = Article::with(['category', 'user'])
            ->when($request->translated, function($query) use ($request) {
                // فقط المقالات المترجمة
                if ($request->translated === 'true') {
                    $query->whereNotNull('title_en')
                          ->whereNotNull('content_en');
                }
                // فقط المقالات غير المترجمة
                elseif ($request->translated === 'false') {
                    $query->where(function($q) {
                        $q->whereNull('title_en')
                          ->orWhereNull('content_en');
                    });
                }
            })
            ->paginate(20);

        return response()->json([
            'success' => true,
            'articles' => $articles->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'title_en' => $article->title_en,
                    'is_translated' => !empty($article->title_en) && !empty($article->content_en),
                    'category' => $article->category->name ?? null,
                    'created_at' => $article->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'total' => $articles->total(),
            ]
        ]);
    }

    /**
     * مثال 5: ترجمة فورية (Synchronous) - استخدام محدود
     * 
     * ⚠️ لا يُنصح باستخدامه في Production - يؤثر على الأداء
     * استخدمه فقط للاختبار أو الحالات الخاصة جداً
     */
    public function translateImmediately(Request $request)
    {
        $service = app(GeminiTranslationService::class);

        $translation = $service->translateContent(
            $request->title,
            $request->content
        );

        if (!$translation) {
            return response()->json([
                'success' => false,
                'message' => 'Translation failed'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'translation' => $translation
        ]);
    }

    /**
     * مثال 6: إحصائيات الترجمة
     */
    public function getTranslationStats()
    {
        $total = Article::count();
        $translated = Article::whereNotNull('title_en')
            ->whereNotNull('content_en')
            ->count();
        $pending = $total - $translated;
        
        $percentage = $total > 0 ? round(($translated / $total) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'stats' => [
                'total_articles' => $total,
                'translated' => $translated,
                'pending' => $pending,
                'percentage' => $percentage . '%',
                
                // آخر 5 مقالات مترجمة
                'recently_translated' => Article::whereNotNull('title_en')
                    ->whereNotNull('content_en')
                    ->latest('updated_at')
                    ->limit(5)
                    ->get(['id', 'title', 'title_en', 'updated_at']),
            ]
        ]);
    }

    /**
     * مثال 7: تحديث مقال مع إعادة ترجمة ذكية
     * 
     * إذا تغير المحتوى العربي، ستحدث إعادة ترجمة تلقائياً
     */
    public function updateArticle(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $article->update($request->only([
            'title',
            'content',
            'category_id',
            'is_published',
        ]));

        // ✅ إذا تغير title أو content، سيتم dispatch job تلقائياً!

        return response()->json([
            'success' => true,
            'message' => 'Article updated. Re-translation will occur if content changed.',
            'article' => $article->fresh(),
        ]);
    }

    /**
     * مثال 8: الحصول على حالة الترجمة لمقال
     */
    public function getTranslationStatus($id)
    {
        $article = Article::findOrFail($id);

        $hasTranslation = !empty($article->title_en) && !empty($article->content_en);
        $isPartiallyTranslated = (!empty($article->title_en) && empty($article->content_en)) 
                              || (empty($article->title_en) && !empty($article->content_en));

        return response()->json([
            'success' => true,
            'article_id' => $article->id,
            'translation_status' => [
                'has_translation' => $hasTranslation,
                'is_partially_translated' => $isPartiallyTranslated,
                'needs_translation' => !$hasTranslation,
                
                'fields' => [
                    'title_en' => [
                        'exists' => !empty($article->title_en),
                        'preview' => $article->title_en ? substr($article->title_en, 0, 50) . '...' : null,
                    ],
                    'content_en' => [
                        'exists' => !empty($article->content_en),
                        'length' => $article->content_en ? strlen($article->content_en) : 0,
                    ]
                ]
            ]
        ]);
    }

    /**
     * مثال 9: Webhook لإشعار Frontend عند اكتمال الترجمة
     * 
     * يمكن استدعاء هذا من TranslateContentJob::handle() بعد النجاح
     */
    public function notifyTranslationComplete($articleId)
    {
        $article = Article::find($articleId);

        if ($article && $article->title_en && $article->content_en) {
            // يمكن إرسال notification للـ Frontend
            // أو broadcast event عبر WebSockets
            // أو إرسال email للمحررين
            
            event(new \App\Events\ArticleTranslated($article));

            return response()->json([
                'success' => true,
                'message' => 'Translation completed and notified'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Article not found or translation incomplete'
        ], 404);
    }

    /**
     * مثال 10: Bulk translation endpoint
     */
    public function bulkTranslate(Request $request)
    {
        $articleIds = $request->article_ids; // [1, 2, 3, ...]

        $articles = Article::whereIn('id', $articleIds)->get();
        
        $dispatched = 0;
        foreach ($articles as $article) {
            TranslateContentJob::dispatch($article);
            $dispatched++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$dispatched} translation jobs dispatched",
            'dispatched_count' => $dispatched,
        ]);
    }
}

/**
 * ملاحظات مهمة:
 * 
 * 1. الترجمة التلقائية تحدث بفضل ArticleObserver - لا حاجة لكود إضافي
 * 
 * 2. للحصول على الترجمة: ببساطة استخدم $article->title_en و $article->content_en
 * 
 * 3. للترجمة اليدوية: TranslateContentJob::dispatch($article)
 * 
 * 4. تجنب الترجمة الفورية (Synchronous) في Production - استخدم Queue دائماً
 * 
 * 5. يمكن تعطيل الترجمة التلقائية عبر:
 *    TRANSLATION_AUTO_ON_CREATE=false
 *    TRANSLATION_AUTO_ON_UPDATE=false
 * 
 * 6. راقب Queue Worker دائماً: php artisan queue:work
 * 
 * 7. راجع Logs للتحقق من نجاح الترجمة: storage/logs/laravel.log
 */
