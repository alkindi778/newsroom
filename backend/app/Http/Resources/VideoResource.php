<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_en' => $this->title_en,
            'slug' => $this->slug,
            'description' => $this->description,
            'description_en' => $this->description_en,
            'video_url' => $this->video_url,
            'embed_url' => $this->embed_url,
            'watch_url' => $this->watch_url,
            
            // Thumbnail with lazy loading support
            'thumbnail' => $this->thumbnail_url,
            'thumbnail_placeholder' => $this->getThumbnailPlaceholder(),
            'thumbnail_srcset' => $this->getThumbnailSrcset(),
            
            'duration' => $this->duration,
            'video_type' => $this->video_type,
            'video_id' => $this->video_id,
            'views' => $this->views,
            'likes' => $this->likes,
            'shares' => $this->shares,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // SEO
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            
            // User info
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ],
        ];
    }

    /**
     * Get thumbnail placeholder for lazy loading
     */
    protected function getThumbnailPlaceholder(): string
    {
        // Generate low quality placeholder based on video type
        if ($this->video_type === 'youtube' && $this->video_id) {
            return "https://img.youtube.com/vi/{$this->video_id}/default.jpg"; // Low quality (120x90)
        }

        if ($this->video_type === 'vimeo' && $this->video_id) {
            return "https://vumbnail.com/{$this->video_id}_small.jpg";
        }

        // Base64 encoded 1px transparent gif as fallback
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }

    /**
     * Get thumbnail srcset for responsive images
     */
    protected function getThumbnailSrcset(): array
    {
        $srcset = [];

        if ($this->video_type === 'youtube' && $this->video_id) {
            return [
                'small' => "https://img.youtube.com/vi/{$this->video_id}/default.jpg", // 120x90
                'medium' => "https://img.youtube.com/vi/{$this->video_id}/mqdefault.jpg", // 320x180
                'high' => "https://img.youtube.com/vi/{$this->video_id}/hqdefault.jpg", // 480x360
                'standard' => "https://img.youtube.com/vi/{$this->video_id}/sddefault.jpg", // 640x480
                'maxres' => "https://img.youtube.com/vi/{$this->video_id}/maxresdefault.jpg", // 1280x720
            ];
        }

        if ($this->video_type === 'vimeo' && $this->video_id) {
            return [
                'small' => "https://vumbnail.com/{$this->video_id}_small.jpg",
                'medium' => "https://vumbnail.com/{$this->video_id}_medium.jpg",
                'large' => "https://vumbnail.com/{$this->video_id}.jpg",
            ];
        }

        if ($this->thumbnail) {
            return [
                'original' => asset('storage/' . $this->thumbnail),
            ];
        }

        return [];
    }
}
