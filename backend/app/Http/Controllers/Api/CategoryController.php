<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();
            
            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب الأقسام'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        try {
            $category = Category::where('slug', $slug)->first();
            
            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'القسم غير موجود'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب القسم'
            ], 500);
        }
    }

    /**
     * Get articles by category
     */
    public function articles(string $slug)
    {
        try {
            $category = Category::where('slug', $slug)->first();
            
            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'القسم غير موجود'
                ], 404);
            }

            $articles = $category->articles()
                ->with(['user', 'category'])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($articles->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'لا توجد أخبار في هذا القسم حالياً',
                    'data' => []
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $articles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ في جلب أخبار القسم'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
