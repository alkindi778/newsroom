<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewspaperIssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // معلومات أساسية
            'id' => $this->id,
            'newspaper_name' => $this->newspaper_name,
            'issue_number' => $this->issue_number,
            'slug' => $this->slug,
            
            // الوصف والمحتوى
            'description' => $this->description,
            'pdf_url' => $this->pdf_url,
            
            // الصورة والغلاف
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'cover_image_path' => $this->cover_image,
            
            // التواريخ
            'publication_date' => $this->publication_date?->toISOString(),
            'formatted_date' => $this->formatted_date,
            'formatted_date_ar' => $this->publication_date?->format('d/m/Y'),
            
            // الإحصائيات
            'stats' => [
                'views' => $this->views,
                'downloads' => $this->downloads,
                'views_formatted' => $this->formatNumber($this->views),
                'downloads_formatted' => $this->formatNumber($this->downloads),
            ],
            
            // الحالة
            'status' => [
                'is_featured' => $this->is_featured,
                'is_published' => $this->is_published,
                'featured_label' => $this->is_featured ? 'مميز' : 'عادي',
                'published_label' => $this->is_published ? 'منشور' : 'مسودة',
            ],
            
            // معلومات المستخدم
            'author' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            
            // الطوابع الزمنية
            'timestamps' => [
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
                'created_at_formatted' => $this->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $this->updated_at?->format('d/m/Y H:i'),
            ],
            
            // روابط سريعة (REST API)
            'links' => [
                'view' => url("/api/v1/newspaper-issues/{$this->slug}"),
                'download' => url("/api/v1/newspaper-issues/{$this->id}/download"),
            ],
        ];
    }

    /**
     * تنسيق الأرقام بصيغة مختصرة
     */
    private function formatNumber($number)
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return $number;
    }
}
