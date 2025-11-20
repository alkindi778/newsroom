<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::orderBy('order', 'asc')->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب الأقسام'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        try {
            $category = Category::where('slug', $slug)->first();
            
            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'القسم غير موجود'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب القسم'
            ], 500);
        }
    }

    /**
     * Get articles by category
     */
    public function articles(Request $request, string $slug)
    {
        try {
            $category = Category::where('slug', $slug)->first();
            
            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'القسم غير موجود'
                ], 404);
            }

            // جلب الأخبار المنشورة فقط مع pagination
            $query = $category->articles()
                ->with(['user:id,name', 'category:id,name,name_en,slug'])
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc');

            // Pagination
            $perPage = min($request->get('per_page', 15), 50); // Max 50 per page
            $articles = $query->paginate($perPage);

            // Transform data using the same format as ArticleController
            $transformedData = $articles->getCollection()->map(function ($article) {
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
            \Log::error('API Category Articles Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب أخبار القسم'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
