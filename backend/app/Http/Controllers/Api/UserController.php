<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get all authors (users with articles)
     */
    public function authors(): JsonResponse
    {
        $authors = User::whereHas('articles', function($query) {
                        $query->where('status', 'published');
                    })
                    ->withCount(['articles' => function($query) {
                        $query->where('status', 'published');
                    }])
                    ->where('status', true)
                    ->select('id', 'name', 'email', 'avatar', 'bio', 'created_at')
                    ->orderBy('articles_count', 'desc')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $authors->map(function($author) {
                return [
                    'id' => $author->id,
                    'name' => $author->name,
                    'email' => $author->email,
                    'avatar' => $author->avatar ? asset('storage/' . $author->avatar) : null,
                    'bio' => $author->bio,
                    'articles_count' => $author->articles_count,
                    'joined_date' => $author->created_at->format('Y-m-d'),
                    'roles' => $author->getRoleNames()
                ];
            })
        ]);
    }

    /**
     * Get specific author details
     */
    public function author(int $id): JsonResponse
    {
        $author = User::with(['articles' => function($query) {
                        $query->where('status', 'published')
                              ->with(['category'])
                              ->select('id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'category_id', 'user_id')
                              ->latest('published_at');
                    }])
                    ->where('status', true)
                    ->find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'المؤلف غير موجود'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $author->id,
                'name' => $author->name,
                'email' => $author->email,
                'avatar' => $author->avatar ? asset('storage/' . $author->avatar) : null,
                'bio' => $author->bio,
                'joined_date' => $author->created_at->format('Y-m-d'),
                'roles' => $author->getRoleNames(),
                'articles_count' => $author->articles->count(),
                'latest_articles' => $author->articles->take(5)->map(function($article) {
                    return [
                        'id' => $article->id,
                        'title' => $article->title,
                        'slug' => $article->slug,
                        'excerpt' => $article->excerpt,
                        'featured_image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
                        'published_at' => $article->published_at?->format('Y-m-d H:i'),
                        'category' => $article->category ? [
                            'id' => $article->category->id,
                            'name' => $article->category->name,
                            'slug' => $article->category->slug,
                            'color' => $article->category->color
                        ] : null
                    ];
                })
            ]
        ]);
    }

    /**
     * Get author's articles
     */
    public function authorArticles(int $id): JsonResponse
    {
        $author = User::where('status', true)->find($id);
        
        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'المؤلف غير موجود'
            ], 404);
        }

        $articles = $author->articles()
                          ->where('status', 'published')
                          ->with(['category'])
                          ->select('id', 'title', 'slug', 'content', 'excerpt', 'featured_image', 'published_at', 'category_id', 'user_id', 'views', 'meta_description', 'keywords')
                          ->latest('published_at')
                          ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $articles->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                    'excerpt' => $article->excerpt,
                    'featured_image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
                    'published_at' => $article->published_at?->format('Y-m-d H:i'),
                    'views' => $article->views,
                    'meta_description' => $article->meta_description,
                    'keywords' => $article->keywords,
                    'category' => $article->category ? [
                        'id' => $article->category->id,
                        'name' => $article->category->name,
                        'slug' => $article->category->slug,
                        'color' => $article->category->color
                    ] : null,
                    'author' => [
                        'id' => $article->user->id,
                        'name' => $article->user->name,
                        'avatar' => $article->user->avatar ? asset('storage/' . $article->user->avatar) : null
                    ]
                ];
            }),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'total_pages' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
            'author' => [
                'id' => $author->id,
                'name' => $author->name,
                'avatar' => $author->avatar ? asset('storage/' . $author->avatar) : null,
                'bio' => $author->bio
            ]
        ]);
    }
}
