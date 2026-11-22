<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Debugging: استخراج مسارات الصور من المحتوى
        if ($request->routeIs('api.v1.articles.show') && $this->content) {
            preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $this->content, $matches);
            $imageSrcs = $matches[1] ?? [];
            
            \Log::info('📰 Article Resource - Images Debug', [
                'article_id' => $this->id,
                'article_title' => $this->title,
                'image_path' => $this->image_path,
                'content_images' => $imageSrcs,
                'total_images' => count($imageSrcs),
            ]);
        }
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_en' => $this->title_en,
            'subtitle' => $this->subtitle,
            'source' => $this->source,
            'slug' => $this->slug,
            'content' => $this->content,
            'content_en' => $this->content_en,
            'excerpt' => $this->summary ?? $this->generateExcerpt(),
            'image' => $this->image_path,  // accessor مع fallback للصور القديمة والجديدة
            'thumbnail' => $this->thumbnail_path,  // thumbnail مع fallback
            'meta_description' => $this->when($request->routeIs('api.v1.articles.show'), $this->meta_description),
            'keywords' => $this->when($request->routeIs('api.v1.articles.show'), $this->parseKeywords()),
            'views' => $this->views ?? 0,
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'author' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'name_en' => $this->category->name_en,
                    'slug' => $this->category->slug
                ];
            }),
            'related_articles' => $this->when(
                $request->routeIs('api.v1.articles.show') && isset($this->related_articles),
                $this->related_articles
            )
        ];
    }

    /**
     * Generate excerpt from content
     */
    private function generateExcerpt(): string
    {
        $length = request()->routeIs('api.v1.articles.show') ? 150 : 100;
        $text = html_entity_decode(strip_tags((string) $this->content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(['&nbsp;', '&NBSP;', "\xC2\xA0"], ' ', $text);
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));
        return mb_substr($text, 0, $length, 'UTF-8') . '...';
    }

    /**
     * Parse keywords into array
     */
    private function parseKeywords(): array
    {
        if (!$this->keywords) {
            return [];
        }
        
        return array_filter(
            array_map('trim', explode('،', $this->keywords)),
            fn($keyword) => !empty($keyword)
        );
    }
}
