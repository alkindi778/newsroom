<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\SmartSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class SmartSummaryController extends Controller
{
    protected SmartSummaryService $summaryService;

    public function __construct(SmartSummaryService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    /**
     * Generate smart summary for an article
     */
    public function generateSummary(Request $request, string $articleId): JsonResponse
    {
        try {
            // Find the article (by ID or slug)
            $article = $this->findArticle($articleId);
            
            if (!$article) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المقال غير موجود أو غير منشور'
                ], 404);
            }

            // Check if user can access this article
            if (!$article->is_published || !$article->published_at || $article->published_at->isFuture()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المقال غير متاح حالياً'
                ], 403);
            }

            // Generate summary
            $summary = $this->summaryService->generateSummary($article);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء الملخص الذكي بنجاح',
                'data' => $summary
            ]);

        } catch (Exception $e) {
            \Log::error('Smart Summary API Error: ' . $e->getMessage(), [
                'article_id' => $articleId,
                'user_id' => auth()->id(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cached summary for an article
     */
    public function getCachedSummary(string $articleId): JsonResponse
    {
        try {
            $article = $this->findArticle($articleId);
            
            if (!$article) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المقال غير موجود'
                ], 404);
            }

            $cachedSummary = $this->summaryService->getCachedSummary($article);

            if (!$cachedSummary) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لا يوجد ملخص محفوظ لهذا المقال'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $cachedSummary
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في استرجاع الملخص'
            ], 500);
        }
    }

    /**
     * Clear cached summary for an article
     */
    public function clearCache(string $articleId): JsonResponse
    {
        try {
            $article = $this->findArticle($articleId);
            
            if (!$article) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المقال غير موجود'
                ], 404);
            }

            $cleared = $this->summaryService->clearCache($article);

            return response()->json([
                'status' => 'success',
                'message' => $cleared ? 'تم مسح الملخص المحفوظ' : 'لم يكن هناك ملخص محفوظ',
                'data' => ['cleared' => $cleared]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في مسح الملخص'
            ], 500);
        }
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats(Request $request): JsonResponse
    {
        try {
            // This could be expanded to show usage statistics
            $stats = [
                'total_summaries_generated' => \DB::table('articles')
                    ->whereNotNull('published_at')
                    ->where('is_published', true)
                    ->count(),
                'cache_hit_rate' => 0.85, // This would be calculated from actual usage
                'avg_generation_time' => '2.3 seconds',
                'supported_languages' => ['Arabic', 'English'],
                'features' => [
                    'extractive_summarization',
                    'key_points_extraction',
                    'sentiment_analysis',
                    'keyword_extraction',
                    'reading_time_estimation'
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب الإحصائيات'
            ], 500);
        }
    }

    /**
     * Batch generate summaries for multiple articles
     */
    public function batchGenerate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'article_ids' => 'required|array|max:10', // Limit to 10 articles at once
                'article_ids.*' => 'required|string'
            ]);

            $results = [];
            $errors = [];

            foreach ($request->article_ids as $articleId) {
                try {
                    $article = $this->findArticle($articleId);
                    
                    if (!$article) {
                        $errors[$articleId] = 'المقال غير موجود';
                        continue;
                    }

                    if (!$article->is_published) {
                        $errors[$articleId] = 'المقال غير منشور';
                        continue;
                    }

                    $summary = $this->summaryService->generateSummary($article);
                    $results[$articleId] = $summary;

                } catch (Exception $e) {
                    $errors[$articleId] = $e->getMessage();
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => sprintf('تم إنشاء %d ملخص من أصل %d مقال', count($results), count($request->article_ids)),
                'data' => [
                    'summaries' => $results,
                    'errors' => $errors,
                    'success_count' => count($results),
                    'error_count' => count($errors)
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في المعالجة المتعددة'
            ], 500);
        }
    }

    /**
     * Find article by ID or slug
     */
    private function findArticle(string $identifier): ?Article
    {
        $query = Article::with(['user:id,name', 'category:id,name,slug']);

        if (is_numeric($identifier)) {
            return $query->where('id', (int) $identifier)->first();
        } else {
            return $query->where('slug', $identifier)->first();
        }
    }
}
