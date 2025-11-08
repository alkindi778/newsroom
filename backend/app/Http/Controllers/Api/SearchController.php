<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search articles
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('query');
            
            if (empty($query)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'يرجى إدخال كلمة للبحث'
                ], 400);
            }

            $articles = Article::with(['user', 'category'])
                ->where('title', 'LIKE', "%{$query}%")
                ->orWhere('content', 'LIKE', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get();

            if ($articles->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'لم يتم العثور على نتائج للبحث',
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
                'message' => 'حدث خطأ في البحث'
            ], 500);
        }
    }
}
