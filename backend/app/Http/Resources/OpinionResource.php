<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OpinionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_en' => $this->title_en,
            'slug' => $this->slug,
            'excerpt' => $this->when(isset($this->excerpt), $this->excerpt),
            'excerpt_en' => $this->when(isset($this->excerpt_en), $this->excerpt_en),
            'content' => $this->content,  // Always return content
            'content_en' => $this->content_en,
            'image' => $this->image_url,  // استخدام accessor من Media Library
            'thumbnail' => $this->thumbnail_url,  // إضافة thumbnail
            'is_published' => (bool) $this->is_published,
            'is_featured' => (bool) $this->is_featured,
            'published_at' => optional($this->published_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'views' => (int) ($this->views ?? 0),
            'likes' => (int) ($this->likes ?? 0),
            'shares' => (int) ($this->shares ?? 0),
            'keywords' => $this->when(isset($this->keywords), $this->keywords),
            'meta_description' => $this->when(isset($this->meta_description), $this->meta_description),
            'writer' => new WriterResource($this->whenLoaded('writer') ?: $this->writer),
        ];
    }
}
