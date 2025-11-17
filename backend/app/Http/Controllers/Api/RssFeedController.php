<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RssFeed;
use Illuminate\Http\Request;

class RssFeedController extends Controller
{
    public function index(Request $request)
    {
        $feeds = RssFeed::with(['category:id,name,slug'])
            ->when(!$request->boolean('include_inactive'), fn ($query) => $query->where('is_active', true))
            ->orderBy('title')
            ->get()
            ->map(function ($feed) {
                return [
                    'id' => $feed->id,
                    'title' => $feed->title,
                    'slug' => $feed->slug,
                    'description' => $feed->description,
                    'is_active' => (bool) $feed->is_active,
                    'category' => $feed->category?->name,
                    'category_slug' => $feed->category?->slug,
                    'language' => $feed->language,
                    'items_count' => $feed->items_count,
                    'ttl' => $feed->ttl,
                    'copyright' => $feed->copyright,
                    'image_url' => $feed->image_url,
                    'url' => route('rss.show', $feed->slug),
                    'last_generated_at' => optional($feed->last_generated_at)->toIso8601String(),
                ];
            })
            ->values();

        $defaultFeed = $feeds->first();

        return response()->json([
            'success' => true,
            'data' => $feeds,
            'meta' => [
                'total' => $feeds->count(),
                'default_feed' => $defaultFeed['url'] ?? null,
            ],
        ]);
    }
}
