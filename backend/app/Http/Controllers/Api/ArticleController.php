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
            $query = Article::with(['user:id,name', 'category:id,name,name_en,slug'])
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
                    'title' => $this->decodeHtmlEntities($article->title),
                    'title_en' => $article->title_en,
                    'subtitle' => $this->decodeHtmlEntities($article->subtitle),
                    'source' => $this->decodeHtmlEntities($article->source),
                    'slug' => $article->slug,
                    'content' => $this->fixContentImageUrls($article->content),
                    'content_en' => $article->content_en,
                    'excerpt' => $this->decodeHtmlEntities($article->excerpt) ?? ($article->content ? mb_substr(strip_tags($article->content), 0, 150) . '...' : ''),
                    'excerpt_en' => $article->content_en ? mb_substr(strip_tags($article->content_en), 0, 150) . '...' : null,
                    'image' => $article->image_path,  // استخدام Media Library accessor
                    'thumbnail' => $article->thumbnail_path,  // استخدام Media Library accessor
                    'meta_description' => $this->decodeHtmlEntities($article->meta_description),
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
                        'name_en' => $article->category->name_en,
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
     * Display the specified article by slug or ID
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $query = Article::with(['user:id,name', 'category:id,name,name_en,slug'])
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());

            // Allow accessing articles either by slug or numeric ID (for legacy/ID-based links)
            if (is_numeric($slug)) {
                $query->where('id', (int) $slug);
            } else {
                $query->where('slug', $slug);
            }

            $article = $query->first();
            
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
                'title' => $this->decodeHtmlEntities($article->title),
                'title_en' => $article->title_en,
                'subtitle' => $this->decodeHtmlEntities($article->subtitle),
                'subtitle_en' => $article->subtitle_en,
                'source' => $this->decodeHtmlEntities($article->source),
                'slug' => $article->slug,
                'content' => $this->fixContentImageUrls($article->content),
                'content_en' => $article->content_en,
                'excerpt' => $this->decodeHtmlEntities($article->excerpt) ?? ($article->content ? mb_substr(strip_tags($article->content), 0, 150) . '...' : ''),
                'excerpt_en' => $article->content_en ? mb_substr(strip_tags($article->content_en), 0, 150) . '...' : null,
                'image' => $article->image_path,
                'thumbnail' => $article->thumbnail_path,
                'meta_description' => $this->decodeHtmlEntities($article->meta_description),
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
                    'name_en' => $article->category->name_en,
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

        $relatedArticles = Article::with(['category:id,name,name_en,slug'])
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
                'title' => $this->decodeHtmlEntities($article->title),
                'title_en' => $article->title_en,
                'subtitle' => $this->decodeHtmlEntities($article->subtitle),
                'subtitle_en' => $article->subtitle_en,
                'slug' => $article->slug,
                'image' => $article->image_path,
                'thumbnail' => $article->thumbnail_path,
                'published_at' => $article->published_at?->toISOString(),
                'category' => $article->category ? [
                    'id' => $article->category->id,
                    'name' => $article->category->name,
                    'name_en' => $article->category->name_en,
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
            $sliderArticles = Article::with(['user:id,name', 'category:id,name,name_en,slug'])
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
                
                $additionalArticles = Article::with(['user:id,name', 'category:id,name,name_en,slug'])
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
                    'title_en' => $article->title_en,
                    'subtitle' => $article->subtitle,
                    'subtitle_en' => $article->subtitle_en,
                    'source' => $article->source,
                    'slug' => $article->slug,
                    'content' => $this->fixContentImageUrls($article->content),
                    'content_en' => $article->content_en,
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
                        'name_en' => $article->category->name_en,
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
            
            $articles = Article::with(['category:id,name,name_en,slug'])
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
                    'title_en' => $article->title_en,
                    'subtitle' => $article->subtitle,
                    'subtitle_en' => $article->subtitle_en,
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
                        'name_en' => $article->category->name_en,
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
            
            $query = Article::with(['category:id,name,name_en,slug'])
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
            
            // Filter by period
            if ($period === 'today') {
                // اليوم: آخر 24 ساعة
                $query->where('published_at', '>=', now()->subDay());
            } elseif ($period === 'week') {
                // الأسبوع: آخر 7 أيام
                $query->where('published_at', '>=', now()->subDays(7));
            } elseif ($period === 'month') {
                // الشهر: آخر 30 يوم
                $query->where('published_at', '>=', now()->subDays(30));
            }
            
            $articles = $query->orderBy('views', 'desc')
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            $transformedData = $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'title_en' => $article->title_en,
                    'subtitle' => $article->subtitle,
                    'subtitle_en' => $article->subtitle_en,
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
                        'name_en' => $article->category->name_en,
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

            $query = Article::with(['category:id,name,name_en,slug'])
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
                    'title_en' => $article->title_en,
                    'subtitle' => $article->subtitle,
                    'subtitle_en' => $article->subtitle_en,
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
                        'name_en' => $article->category->name_en,
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
            
            $articles = Article::with(['category:id,name,name_en,slug'])
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
                    'title_en' => $article->title_en,
                    'subtitle' => $article->subtitle,
                    'subtitle_en' => $article->subtitle_en,
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
                        'name_en' => $article->category->name_en,
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
     * Get breaking news articles (يجمع الأخبار العاجلة المستقلة + المقالات المعلمة كعاجلة)
     */
    public function breakingNews(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 5), 10);
            $result = collect();
            
            // 1. الأخبار العاجلة المستقلة (من جدول breaking_news) - أولوية أعلى
            $independentNews = \App\Models\BreakingNews::with('article:id,slug,title,title_en')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->orderByDesc('priority')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->map(function ($news) {
                    // إذا كان مرتبط بمقال، استخدم بيانات المقال
                    if ($news->article) {
                        return [
                            'id' => 'bn_' . $news->id,
                            'title' => $news->title,
                            'title_en' => $news->article->title_en,
                            'slug' => $news->article->slug,
                            'priority' => 100 + $news->priority,
                            'published_at' => $news->created_at->toISOString(),
                            'type' => 'breaking',
                        ];
                    }
                    
                    // خبر عاجل مستقل بدون مقال
                    return [
                        'id' => 'bn_' . $news->id,
                        'title' => $news->title,
                        'title_en' => null,
                        'slug' => $news->url ? null : 'breaking-news',
                        'url' => $news->url,
                        'priority' => 100 + $news->priority,
                        'published_at' => $news->created_at->toISOString(),
                        'type' => 'breaking',
                    ];
                });
            
            // 2. المقالات المعلمة كأخبار عاجلة
            $breakingArticles = Article::with(['category:id,name,name_en,slug'])
                ->where('is_published', true)
                ->where('is_breaking_news', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($article) {
                    return [
                        'id' => $article->id,
                        'title' => $this->decodeHtmlEntities($article->title),
                        'title_en' => $article->title_en,
                        'subtitle' => $this->decodeHtmlEntities($article->subtitle),
                        'source' => $this->decodeHtmlEntities($article->source),
                        'slug' => $article->slug,
                        'excerpt' => $this->decodeHtmlEntities($article->excerpt) ?? mb_substr(strip_tags($article->content), 0, 100) . '...',
                        'image' => $article->image_path,
                        'thumbnail' => $article->thumbnail_path,
                        'views' => $article->views ?? 0,
                        'published_at' => $article->published_at?->toISOString(),
                        'priority' => 50,
                        'type' => 'article',
                        'category' => $article->category ? [
                            'id' => $article->category->id,
                            'name' => $this->decodeHtmlEntities($article->category->name),
                            'name_en' => $article->category->name_en,
                            'slug' => $article->category->slug
                        ] : null
                    ];
                });
            
            // دمج وترتيب حسب الأولوية
            $transformedData = $independentNews->concat($breakingArticles)
                ->sortByDesc('priority')
                ->take($limit)
                ->values();

            return response()->json([
                'status' => 'success',
                'data' => $transformedData,
                'count' => $transformedData->count()
            ], 200, [], JSON_UNESCAPED_UNICODE);

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
    
    /**
     * تحويل مسارات الصور في المحتوى إلى مسارات كاملة وفك ترميز HTML entities
     */
    private function fixContentImageUrls($content)
    {
        if (empty($content)) {
            return $content;
        }
        
        // فك ترميز HTML entities أولاً
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // إذا كان المحتوى لا يحتوي على HTML tags، نحوله إلى فقرات
        if (!preg_match('/<[^>]+>/', $content)) {
            // تقسيم النص على السطور المزدوجة
            $paragraphs = preg_split('/\n\s*\n/', trim($content));
            
            // إذا لم يكن هناك فواصل سطور، نقسم كل 3-4 جمل
            if (count($paragraphs) === 1 && strlen($content) > 500) {
                $sentences = preg_split('/\.\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
                $paragraphs = [];
                $currentPara = '';
                $sentenceCount = 0;
                
                foreach ($sentences as $sentence) {
                    $currentPara .= $sentence . '. ';
                    $sentenceCount++;
                    
                    // كل 3-4 جمل نعمل فقرة جديدة
                    if ($sentenceCount >= 3) {
                        $paragraphs[] = trim($currentPara);
                        $currentPara = '';
                        $sentenceCount = 0;
                    }
                }
                
                // إضافة أي محتوى متبقي
                if (!empty($currentPara)) {
                    $paragraphs[] = trim($currentPara);
                }
            }
            
            $content = '<p>' . implode('</p><p>', array_filter($paragraphs)) . '</p>';
        }
        
        // الحصول على base URL الكامل من config
        $baseUrl = rtrim(config('app.url'), '/');
        
        // استبدال جميع مسارات الصور النسبية بمسارات كاملة
        $content = preg_replace_callback(
            '/<img([^>]*)src=["\']([^"\']+)["\']([^>]*)>/i',
            function($matches) use ($baseUrl) {
                $beforeSrc = $matches[1];
                $srcValue = $matches[2];
                $afterSrc = $matches[3];
                
                // إذا كان المسار يبدأ بـ http:// أو https:// لا نغيره
                if (preg_match('/^https?:\/\//i', $srcValue)) {
                    return $matches[0];
                }
                
                // إزالة أي / من البداية وإزالة ../ 
                $srcValue = ltrim($srcValue, '/');
                // إزالة جميع ../ من المسار
                $srcValue = preg_replace('/\.\.\//', '', $srcValue);
                
                // إذا كان المسار يحتوي على media/articles أو storage
                if (strpos($srcValue, 'media/articles') !== false || strpos($srcValue, 'storage') === 0) {
                    // التأكد من وجود storage/ في البداية
                    if (!str_starts_with($srcValue, 'storage/')) {
                        $srcValue = 'storage/' . $srcValue;
                    }
                    
                    // بناء المسار الكامل
                    $fullUrl = $baseUrl . '/' . $srcValue;
                    
                    return '<img' . $beforeSrc . 'src="' . $fullUrl . '"' . $afterSrc . '>';
                }
                
                // إرجاع الصورة كما هي إذا لم تتطابق مع الشروط
                return $matches[0];
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * فك ترميز HTML entities من النص
     */
    private function decodeHtmlEntities(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }
        
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
