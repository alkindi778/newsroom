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
            'title' => $this->cleanText($this->title),
            'title_en' => $this->cleanText($this->title_en),
            'slug' => $this->slug,
            'excerpt' => $this->when(isset($this->excerpt), $this->cleanText($this->excerpt)),
            'excerpt_en' => $this->when(isset($this->excerpt_en), $this->cleanText($this->excerpt_en)),
            'content' => $this->cleanHtml($this->content),  // Always return content
            'content_en' => $this->cleanHtml($this->content_en),
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
            'meta_description' => $this->when(isset($this->meta_description), $this->cleanText($this->meta_description)),
            'writer' => new WriterResource($this->whenLoaded('writer') ?: $this->writer),
        ];
    }

    private function cleanText(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }
        $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (str_contains($decoded, '&')) {
            $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $decoded = str_replace(['&nbsp;', '&NBSP;', "\xC2\xA0"], ' ', $decoded);
        return trim((string) preg_replace('/\s+/u', ' ', $decoded));
    }

    private function cleanHtml(?string $html): ?string
    {
        if (empty($html)) {
            return $html;
        }
        $cleaned = str_replace(['&amp;nbsp;', '&nbsp;', '&NBSP;', "\xC2\xA0"], ' ', $html);
        return preg_replace('/^(?:\s*<p[^>]*>(?:\s|<br\s*\/?>)*<\/p>\s*)+/iu', '', $cleaned);
    }
}
