<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\VectorSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    private VectorSearchService $vectorSearchService;

    public function __construct(VectorSearchService $vectorSearchService)
    {
        $this->vectorSearchService = $vectorSearchService;
    }

    /**
     * Search articles using semantic search with fallback to traditional search
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('query') ?? $request->get('q');
            $limit = (int)($request->get('limit') ?? 10);
            $page = (int)($request->get('page') ?? 1);
            $perPage = (int)($request->get('per_page') ?? 12);
            
            if (empty($query)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يرجى إدخال كلمة للبحث'
                ], 400);
            }

            // Try semantic search first
            $semanticResults = $this->vectorSearchService->search($query, $limit * 2);

            if ($semanticResults->count() > 0) {
                // Use semantic search results
                $articles = $semanticResults->load(['user', 'category']);
            } else {
                // Fallback to traditional keyword search
                $articles = Article::with(['user', 'category'])
                    ->where('is_published', true)
                    ->where('approval_status', 'approved')
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('subtitle', 'LIKE', "%{$query}%")
                          ->orWhere('content', 'LIKE', "%{$query}%");
                    })
                    ->orderBy('published_at', 'desc')
                    ->take($limit * 2)
                    ->get();
            }

            if ($articles->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'لم يتم العثور على نتائج للبحث',
                    'data' => [],
                    'total' => 0,
                    'current_page' => $page,
                    'last_page' => 1
                ]);
            }

            // Paginate results
            $total = $articles->count();
            $lastPage = ceil($total / $perPage);
            $offset = ($page - 1) * $perPage;
            $paginatedArticles = $articles->slice($offset, $perPage)->values();

            return response()->json([
                'status' => 'success',
                'data' => $paginatedArticles,
                'total' => $total,
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage
            ]);
        } catch (\Exception $e) {
            Log::error('Search error', [
                'error' => $e->getMessage(),
                'query' => $request->get('query') ?? $request->get('q')
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في البحث'
            ], 500);
        }
    }

    /**
     * Get similar articles to a specific article
     */
    public function similar(Request $request, Article $article)
    {
        try {
            $limit = (int)($request->get('limit') ?? 5);
            $threshold = (float)($request->get('threshold') ?? 0.3);

            Log::info('Similar articles request', [
                'article_id' => $article->id,
                'limit' => $limit,
                'threshold' => $threshold
            ]);

            $similarArticles = $this->vectorSearchService->findSimilarArticles(
                $article,
                $limit,
                $threshold
            );

            Log::info('Similar articles result', [
                'count' => $similarArticles->count()
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $similarArticles->load(['user', 'category'])
            ]);
        } catch (\Exception $e) {
            Log::error('Similar articles search error', [
                'error' => $e->getMessage(),
                'article_id' => $article->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في البحث عن مقالات مشابهة'
            ], 500);
        }
    }

    /**
     * Check for duplicate articles
     */
    public function checkDuplicates(Request $request, Article $article)
    {
        try {
            $threshold = (float)($request->get('threshold') ?? 0.95);

            $duplicates = $this->vectorSearchService->findDuplicates($article, $threshold);

            return response()->json([
                'status' => 'success',
                'data' => $duplicates->load(['user', 'category']),
                'has_duplicates' => $duplicates->count() > 0
            ]);
        } catch (\Exception $e) {
            Log::error('Duplicate check error', [
                'error' => $e->getMessage(),
                'article_id' => $article->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في فحص المحتوى المكرر'
            ], 500);
        }
    }
}
