<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class YouTubeThumbnailService
{
    /**
     * Get the best available YouTube thumbnail URL
     *
     * @param string $videoId
     * @return string
     */
    public static function getBestThumbnail(string $videoId): string
    {
        $cacheKey = "youtube_thumbnail_{$videoId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($videoId) {
            // Try in order of preference
            $qualities = [
                'maxresdefault', // 1280x720
                'sddefault',     // 640x480  
                'hqdefault',     // 480x360
                'mqdefault',     // 320x180
                'default',       // 120x90
            ];

            foreach ($qualities as $quality) {
                $url = "https://img.youtube.com/vi/{$videoId}/{$quality}.jpg";
                
                if (self::checkThumbnailExists($url)) {
                    return $url;
                }
            }

            // Fallback to default
            return "https://img.youtube.com/vi/{$videoId}/default.jpg";
        });
    }

    /**
     * Check if thumbnail URL exists and returns 200
     *
     * @param string $url
     * @return bool
     */
    private static function checkThumbnailExists(string $url): bool
    {
        try {
            $response = Http::head($url, [
                'timeout' => 5,
                'User-Agent' => 'Mozilla/5.0 (compatible; Newsroom/1.0)'
            ]);

            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get thumbnail srcset with available qualities
     *
     * @param string $videoId
     * @return array
     */
    public static function getThumbnailSrcset(string $videoId): array
    {
        $cacheKey = "youtube_srcset_{$videoId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($videoId) {
            return [
                'small' => "https://img.youtube.com/vi/{$videoId}/default.jpg",    // 120x90
                'medium' => "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg",  // 320x180
                'high' => self::getBestThumbnail($videoId),                        // Best available
                'standard' => self::getBestThumbnail($videoId),                    // Best available
                'maxres' => self::getBestThumbnail($videoId),                       // Best available
            ];
        });
    }
}
