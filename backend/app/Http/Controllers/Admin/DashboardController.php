<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'articles_count' => Article::count(),
            'categories_count' => Category::count(),
            'users_count' => User::count(),
        ];

        $recentArticles = Article::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // Categories with articles count for distribution chart
        $categoriesStats = Category::withCount('articles')
            ->having('articles_count', '>', 0)
            ->orderBy('articles_count', 'desc')
            ->get();

        $totalArticles = Article::count();

        return view('admin.dashboard.index', compact('stats', 'recentArticles', 'categoriesStats', 'totalArticles'));
    }
}
