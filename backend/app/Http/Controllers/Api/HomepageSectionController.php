<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HomepageSectionService;
use Illuminate\Http\JsonResponse;

class HomepageSectionController extends Controller
{
    protected $homepageSectionService;

    public function __construct(HomepageSectionService $homepageSectionService)
    {
        $this->homepageSectionService = $homepageSectionService;
    }

    /**
     * Get all active homepage sections
     */
    public function index(): JsonResponse
    {
        try {
            $sections = $this->homepageSectionService->getActiveSections();

            $transformedSections = $sections->map(function ($section) {
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'slug' => $section->slug,
                    'type' => $section->type,
                    'title' => $section->title,
                    'title_en' => $section->title_en,
                    'subtitle' => $section->subtitle,
                    'subtitle_en' => $section->subtitle_en,
                    'category_id' => $section->category_id,
                    'category' => $section->category ? [
                        'id' => $section->category->id,
                        'name' => $section->category->name,
                        'name_en' => $section->category->name_en,
                        'slug' => $section->category->slug,
                    ] : null,
                    'order' => $section->order,
                    'items_count' => $section->items_count,
                    'is_active' => $section->is_active,
                    'settings' => $section->settings,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedSections
            ]);

        } catch (\Exception $e) {
            \Log::error('API Homepage Sections Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب أقسام الصفحة الرئيسية'
            ], 500);
        }
    }

    /**
     * Get a specific section by slug
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $section = $this->homepageSectionService->getSectionBySlug($slug);

            if (!$section || !$section->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'القسم غير موجود أو غير نشط'
                ], 404);
            }

            $transformedSection = [
                'id' => $section->id,
                'name' => $section->name,
                'slug' => $section->slug,
                'type' => $section->type,
                'title' => $section->title,
                'title_en' => $section->title_en,
                'subtitle' => $section->subtitle,
                'subtitle_en' => $section->subtitle_en,
                'category_id' => $section->category_id,
                'category' => $section->category ? [
                    'id' => $section->category->id,
                    'name' => $section->category->name,
                    'name_en' => $section->category->name_en,
                    'slug' => $section->category->slug,
                ] : null,
                'order' => $section->order,
                'items_count' => $section->items_count,
                'is_active' => $section->is_active,
                'settings' => $section->settings,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $transformedSection
            ]);

        } catch (\Exception $e) {
            \Log::error('API Homepage Section Show Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب القسم'
            ], 500);
        }
    }
}
