<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaPost;
use App\Models\Article;
use App\Services\SocialMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function __construct(private SocialMediaService $socialMediaService)
    {
    }

    /**
     * الحصول على حالة المنشورات
     */
    public function getPostStatus(Article $article): JsonResponse
    {
        $posts = SocialMediaPost::where('article_id', $article->id)
            ->get()
            ->groupBy('platform')
            ->map(function ($platformPosts) {
                return [
                    'status' => $platformPosts->first()->status,
                    'published_at' => $platformPosts->first()->published_at,
                    'external_id' => $platformPosts->first()->external_id,
                    'error' => $platformPosts->first()->error_message,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $posts,
        ]);
    }

    /**
     * الحصول على إحصائيات المنشورات
     */
    public function getStatistics(): JsonResponse
    {
        $stats = [
            'total_posts' => SocialMediaPost::count(),
            'published' => SocialMediaPost::where('status', 'published')->count(),
            'failed' => SocialMediaPost::where('status', 'failed')->count(),
            'pending' => SocialMediaPost::where('status', 'pending')->count(),
            'scheduled' => SocialMediaPost::where('status', 'scheduled')->count(),
            'by_platform' => SocialMediaPost::selectRaw('platform, status, count(*) as count')
                ->groupBy('platform', 'status')
                ->get()
                ->groupBy('platform')
                ->map(function ($platformPosts) {
                    return $platformPosts->pluck('count', 'status');
                }),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    /**
     * الحصول على قائمة المنشورات
     */
    public function listPosts(Request $request): JsonResponse
    {
        $query = SocialMediaPost::with('article');

        if ($request->has('platform')) {
            $query->where('platform', $request->input('platform'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $posts = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $posts,
        ]);
    }
}
