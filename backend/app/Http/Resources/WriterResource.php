<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WriterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_en' => $this->name_en,
            'slug' => $this->slug,
            'bio' => $this->bio,
            'bio_en' => $this->bio_en,
            'position' => $this->position ?? null,
            'position_en' => $this->position_en ?? null,
            'specialty' => $this->specialization ?? null,
            'specialization_en' => $this->specialization_en ?? null,
            'image' => $this->image_url,  // استخدام accessor من Media Library
            'thumbnail' => $this->thumbnail_url,  // إضافة thumbnail
            'social_links' => [
                'facebook' => $this->facebook_url ?? null,
                'twitter' => $this->twitter_url ?? null,
                'linkedin' => $this->linkedin_url ?? null,
                'website' => $this->website_url ?? null,
            ],
            'is_active' => (bool) $this->is_active,
            'opinions_count' => (int) ($this->opinions_count ?? 0),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
