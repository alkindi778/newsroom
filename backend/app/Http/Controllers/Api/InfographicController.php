<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Infographic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InfographicController extends Controller
{
    /**
     * عرض قائمة الإنفوجرافيكات
     */
    public function index(Request $request): JsonResponse
    {
        $query = Infographic::with('category')
            ->active()
            ->ordered();

        // تصفية حسب القسم
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // تصفية حسب المميزة
        if ($request->has('featured')) {
            $query->featured();
        }

        // البحث
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('title_en', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('description_en', 'like', "%{$searchTerm}%");
            });
        }

        // الحد الأقصى للعناصر
        $limit = $request->input('limit', 12);
        $limit = min($limit, 50); // حد أقصى 50

        $infographics = $query->paginate($limit);

        return response()->json([
            'data' => $infographics->items(),
            'meta' => [
                'current_page' => $infographics->currentPage(),
                'last_page' => $infographics->lastPage(),
                'per_page' => $infographics->perPage(),
                'total' => $infographics->total(),
            ]
        ]);
    }

    /**
     * عرض تفاصيل إنفوجرافيك واحد
     */
    public function show($slug): JsonResponse
    {
        $infographic = Infographic::with('category')
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // زيادة عدد المشاهدات
        $infographic->incrementViews();

        return response()->json([
            'data' => $infographic
        ]);
    }

    /**
     * الحصول على الإنفوجرافيكات ذات الصلة
     */
    public function related($slug): JsonResponse
    {
        $infographic = Infographic::where('slug', $slug)->firstOrFail();

        $relatedInfographics = Infographic::with('category')
            ->active()
            ->where('id', '!=', $infographic->id)
            ->where(function ($query) use ($infographic) {
                if ($infographic->category_id) {
                    $query->where('category_id', $infographic->category_id);
                }
            })
            ->ordered()
            ->limit(6)
            ->get();

        return response()->json([
            'data' => $relatedInfographics
        ]);
    }

    /**
     * الحصول على الإنفوجرافيكات المميزة
     */
    public function featured(): JsonResponse
    {
        $infographics = Infographic::with('category')
            ->active()
            ->featured()
            ->ordered()
            ->limit(6)
            ->get();

        return response()->json([
            'data' => $infographics
        ]);
    }

    /**
     * الحصول على آخر الإنفوجرافيكات
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 6);
        $limit = min($limit, 20);

        $infographics = Infographic::with('category')
            ->active()
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $infographics
        ]);
    }

    /**
     * الحصول على الإنفوجرافيكات الأكثر مشاهدة
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 6);
        $limit = min($limit, 20);

        $infographics = Infographic::with('category')
            ->active()
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $infographics
        ]);
    }
}
