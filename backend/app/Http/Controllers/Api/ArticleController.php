<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Category;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of published articles
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Article::with(['user:id,name', 'category:id,name,slug'])
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());

            // Filter by category_id if provided
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by category (accepts both ID and slug)
            if ($request->has('category') && $request->category) {
                $categoryValue = $request->category;
                
                // Check if it's a number (ID) or string (slug)
                if (is_numeric($categoryValue)) {
                    $query->where('category_id', $categoryValue);
                } else {
                    $query->whereHas('category', function($q) use ($categoryValue) {
                        $q->where('slug', $categoryValue);
                    });
                }
            }

            // Search functionality
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('subtitle', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('source', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Order by published date or created date
            $query->orderBy('published_at', 'desc');

            // Pagination
            $perPage = min($request->get('per_page', 15), 50); // Max 50 per page
            $articles = $query->paginate($perPage);

            // Transform data using ArticleResource format
            $transformedData = $articles->getCollection()->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'subtitle' => $article->subtitle,
                    'source' => $article->source,
                    'slug' => $article->slug,
                    'content' => $article->content,
                    'excerpt' => $article->excerpt ?? ($article->content ? mb_substr(strip_tags($article->content), 0, 150) . '...' : ''),
                    'image' => $article->image_path,  // استخدام Media Library accessor
                    'thumbnail' => $article->thumbnail_path,  // استخدام Media Library accessor
                    'meta_description' => $article->meta_description,
                    'keywords' => $article->keywords ? array_map('trim', explode(',', str_replace('،', ',', $article->keywords))) : [],
                    'views' => $article->views ?? 0,
                    'show_in_slider' => $article->show_in_slider ?? false,
                    'is_breaking_news' => $article->is_breaking_news ?? false,
                    'published_at' => $article->published_at?->toISOString(),
                    'created_at' => $article->created_at->toISOString(),
                    'updated_at' => $article->updated_at->toISOString(),
                    'author' => $article->user ? [
                        'id' => $article->user->id,
                        'name' => $article->user->name
                    ] : null,
                    'category' => $article->category ? [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug
                    ] : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedData,
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('API Articles Index Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب الأخبار'
            ], 500);
        }
    }

    /**
     * Display the specified article by slug
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $article = Article::with(['user:id,name', 'category:id,name,slug'])
                ->where('slug', $slug)
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->first();
            
            if (!$article) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'الخبر غير موجود أو غير منشور'
                ], 404);
            }

            // Increment views count
            $article->increment('views');
            
            // Transform data to include all new fields
            $transformedArticle = [
                'id' => $article->id,
                'title' => $article->title,
                'subtitle' => $article->subtitle,
                'source' => $article->source,
                'slug' => $article->slug,
                'content' => $article->content,
                'excerpt' => $article->excerpt ?? ($article->content ? mb_substr(strip_tags($article->content), 0, 150) . '...' : ''),
                'image' => $article->image_path,
                'thumbnail' => $article->thumbnail_path,
                'meta_description' => $article->meta_description,
                'keywords' => $article->keywords ? array_map('trim', explode(',', str_replace('،', ',', $article->keywords))) : [],
                'views' => $article->views,
                'show_in_slider' => $article->show_in_slider ?? false,
                'is_breaking_news' => $article->is_breaking_news ?? false,
                'published_at' => $article->published_at?->toISOString(),
                'created_at' => $article->created_at->toISOString(),
                'updated_at' => $article->updated_at->toISOString(),
                'author' => $article->user ? [
                    'id' => $article->user->id,
                    'name' => $article->user->name
                ] : null,
                'category' => $article->category ? [
                    'id' => $article->category->id,
                    'name' => $article->category->name,
                    'slug' => $article->category->slug
                ] : null,
                // Related articles from same category
                'related_articles' => $this->getRelatedArticles($article)
            ];
            
            return response()->json([
                'status' => 'success',
                'data' => $transformedArticle
            ]);

        } catch (\Exception $e) {
            \Log::error('API Articles Show Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب الخبر'
            ], 500);
        }
    }

    /**
     * Get related articles from the same category
     */
    private function getRelatedArticles(Article $article, int $limit = 5): array
    {
        if (!$article->category_id) {
            return [];
        }

        $relatedArticles = Article::with(['category:id,name,slug'])
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return $relatedArticles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'subtitle' => $article->subtitle,
                'slug' => $article->slug,
                'image' => $article->image_path,
                'thumbnail' => $article->thumbnail_path,
                'published_at' => $article->published_at?->toISOString(),
                'category' => $article->category ? [
                    'id' => $article->category->id,
                    'name' => $article->category->name,
                    'slug' => $article->category->slug
                ] : null
            ];
        })->toArray();
    }

    /**
     * Get featured articles
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 10), 20); // Max 20 articles
            
            // أولاً: جلب الأخبار المحددة للسلايدر
            $sliderArticles = Article::with(['user:id,name', 'category:id,name,slug'])
                ->where('is_published', true)
                ->where('show_in_slider', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();
            
            // إذا لم يكن هناك ما يكفي، أضف أحدث الأخبار
            if ($sliderArticles->count() < $limit) {
                $remainingCount = $limit - $sliderArticles->count();
                $sliderIds = $sliderArticles->pluck('id')->toArray();
                
                $additionalArticles = Article::with(['user:id,name', 'category:id,name,slug'])
                    ->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->whereNotIn('id', $sliderIds)
                    ->orderBy('published_at', 'desc')
                    ->limit($remainingCount)
                    ->get();
                
                $articles = $sliderArticles->merge($additionalArticles);
            } else {
                $articles = $sliderArticles;
            }

            $transformedData = $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'subtitle' => $article->subtitle,
                    'source' => $article->source,
                    'slug' => $article->slug,
                    'content' => $article->content,
                    'excerpt' => $article->excerpt ?? ($article->content ? mb_substr(strip_tags($article->content), 0, 150) . '...' : ''),
                    'image' => $article->image_path,
                    'thumbnail' => $article->thumbnail_path,
                    'meta_description' => $article->meta_description,
                    'keywords' => $article->keywords ? array_map('trim', explode(',', str_replace('،', ',', $article->keywords))) : [],
                    'views' => $article->views ?? 0,
                    'show_in_slider' => $article->show_in_slider ?? false,
                    'is_breaking_news' => $article->is_breaking_news ?? false,
                    'published_at' => $article->published_at?->toISOString(),
                    'created_at' => $article->created_at->toISOString(),
                    'updated_at' => $article->updated_at->toISOString(),
                    'author' => $article->user ? [
                        'id' => $article->user->id,
                        'name' => $article->user->name
                    ] : null,
                    'category' => $article->category ? [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug
                    ] : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch featured articles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest articles
     */
    public function latest(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 10), 20); // Max 100 articles
            
            $articles = Article::with(['category:id,name,slug'])
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            $transformedData = $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'subtitle' => $article->subtitle,
                    'source' => $article->source,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt ?? mb_substr(strip_tags($article->content ?? ''), 0, 100, 'UTF-8') . '...',
                    'image' => $article->image_path,
                    'thumbnail' => $article->thumbnail_path,
                    'views' => $article->views ?? 0,
                    'show_in_slider' => $article->show_in_slider ?? false,
                    'is_breaking_news' => $article->is_breaking_news ?? false,
                    'published_at' => $article->published_at?->toISOString(),
                    'category' => $article->category ? [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug
                    ] : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedData
            ]);

        } catch (\Exception $e) {
            \Log::error('API Latest Articles Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب آخر الأخبار'
            ], 500);
        }
    }

    /**
     * Get featured/popular articles by views
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 10), 20);
            $period = $request->get('period', 'month'); // today, week, month
            
            $query = Article::with(['category:id,name,slug'])
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
            
            // Filter by period
            if ($period === 'today') {
                $query->whereDate('published_at', today());
            } elseif ($period === 'week') {
                $query->where('published_at', '>=', now()->subWeek());
            } elseif ($period === 'month') {
                $query->where('published_at', '>=', now()->subMonth());
            }
            
            $articles = $query->orderBy('views', 'desc')
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            $transformedData = $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'subtitle' => $article->subtitle,
                    'source' => $article->source,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt ?? mb_substr(strip_tags($article->content ?? ''), 0, 100, 'UTF-8') . '...',
                    'image' => $article->image_path,
                    'thumbnail' => $article->thumbnail_path,
                    'views' => $article->views ?? 0,
                    'show_in_slider' => $article->show_in_slider ?? false,
                    'is_breaking_news' => $article->is_breaking_news ?? false,
                    'published_at' => $article->published_at?->toISOString(),
                    'category' => $article->category ? [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug
                    ] : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedData
            ]);

        } catch (\Exception $e) {
            \Log::error('API Popular Articles Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب الأخبار الأكثر مشاهدة'
            ], 500);
        }
    }

    /**
     * Search articles
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->get('q', '');
            
            if (empty($searchTerm)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يرجى إدخال كلمة البحث'
                ], 400);
            }

            $query = Article::with(['category:id,name,slug'])
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where(function($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('subtitle', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('source', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('keywords', 'LIKE', "%{$searchTerm}%");
                });

            $perPage = min($request->get('per_page', 15), 50);
            $articles = $query->orderBy('published_at', 'desc')->paginate($perPage);

            $transformedData = $articles->getCollection()->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'subtitle' => $article->subtitle,
                    'source' => $article->source,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt ?? ($article->content ? mb_substr(strip_tags($article->content), 0, 150) . '...' : ''),
                    'image' => $article->image_path,
                    'thumbnail' => $article->thumbnail_path,
                    'views' => $article->views ?? 0,
                    'show_in_slider' => $article->show_in_slider ?? false,
                    'is_breaking_news' => $article->is_breaking_news ?? false,
                    'published_at' => $article->published_at?->toISOString(),
                    'category' => $article->category ? [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug
                    ] : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedData,
                'search_query' => $searchTerm,
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('API Search Articles Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في البحث'
            ], 500);
        }
    }

    /**
     * Get slider articles (articles marked to show in slider)
     */
    public function slider(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 5), 10); // Max 10 slider articles
            
            $articles = Article::with(['category:id,name,slug'])
                ->where('is_published', true)
                ->where('show_in_slider', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            $transformedData = $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'subtitle' => $article->subtitle,
                    'source' => $article->source,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt ?? substr(strip_tags($article->content), 0, 150) . '...',
                    'image' => $article->image_path,
                    'thumbnail' => $article->thumbnail_path,
                    'views' => $article->views ?? 0,
                    'is_breaking_news' => $article->is_breaking_news ?? false,
                    'published_at' => $article->published_at?->toISOString(),
                    'category' => $article->category ? [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug
                    ] : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedData,
                'count' => $transformedData->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('API Slider Articles Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب أخبار السلايدر'
            ], 500);
        }
    }

    /**
     * Get breaking news articles
     */
    public function breakingNews(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 5), 10); // Max 10 breaking news
            
            $articles = Article::with(['category:id,name,slug'])
                ->where('is_published', true)
                ->where('is_breaking_news', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            $transformedData = $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'subtitle' => $article->subtitle,
                    'source' => $article->source,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt ?? substr(strip_tags($article->content), 0, 100) . '...',
                    'image' => $article->image_path,
                    'thumbnail' => $article->thumbnail_path,
                    'views' => $article->views ?? 0,
                    'published_at' => $article->published_at?->toISOString(),
                    'category' => $article->category ? [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug
                    ] : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedData,
                'count' => $transformedData->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('API Breaking News Articles Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب الأخبار العاجلة'
            ], 500);
        }
    }

    /**
     * Increment article views count
     */
    public function incrementView($id): JsonResponse
    {
        try {
            $article = Article::find($id);
            
            if (!$article) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المقال غير موجود'
                ], 404);
            }
            
            // Increment views
            $article->increment('views');
            
            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل المشاهدة',
                'views' => $article->views
            ]);
            
        } catch (\Exception $e) {
            \Log::error('API Increment Views Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في تسجيل المشاهدة'
            ], 500);
        }
    }
}
