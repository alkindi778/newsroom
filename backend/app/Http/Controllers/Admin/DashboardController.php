<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Video;
use App\Models\Opinion;
use App\Models\NewspaperIssue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        // إذا كان المستخدم سكرتير، حوّله مباشرة إلى رسائل التواصل
        $user = auth()->user();
        if ($user && $user->hasRole('سكرتير')) {
            return redirect()->route('admin.contact-messages.index');
        }

        // Main statistics
        $stats = [
            'articles_count' => Article::count(),
            'articles_published' => Article::where('is_published', true)->count(),
            'articles_pending' => Article::where('approval_status', 'pending')->count(),
            'videos_count' => Video::count(),
            'opinions_count' => Opinion::count(),
            'newspaper_issues_count' => NewspaperIssue::count(),
            'categories_count' => Category::count(),
            'users_count' => User::count(),
            'breaking_news_count' => Article::where('is_breaking_news', true)->count(),
            'slider_news_count' => Article::where('show_in_slider', true)->count(),
            'total_views' => Article::sum('views'),
        ];

        // Recent articles
        $recentArticles = Article::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // Breaking news
        $breakingNews = Article::where('is_breaking_news', true)
            ->latest()
            ->take(3)
            ->get();

        // Categories with articles count for distribution chart
        $categoriesStats = Category::withCount('articles')
            ->having('articles_count', '>', 0)
            ->orderBy('articles_count', 'desc')
            ->take(10)
            ->get();

        // Last 7 days activity
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->locale('ar')->isoFormat('DD MMM'),
                'articles' => Article::whereDate('created_at', $date)->count(),
                'videos' => Video::whereDate('created_at', $date)->count(),
                'opinions' => Opinion::whereDate('created_at', $date)->count(),
            ];
        }

        // Top viewed articles
        $topArticles = Article::orderBy('views', 'desc')
            ->take(5)
            ->get(['id', 'title', 'views', 'image']);

        // Recent videos
        $recentVideos = Video::latest()
            ->take(3)
            ->get(['id', 'title', 'thumbnail', 'created_at']);

        // Recent opinions
        $recentOpinions = Opinion::with('writer')
            ->latest()
            ->take(3)
            ->get();

        // Recent activities
        $recentActivities = Activity::with('causer')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'recentArticles',
            'breakingNews',
            'categoriesStats',
            'last7Days',
            'topArticles',
            'recentVideos',
            'recentOpinions',
            'recentActivities'
        ));
    }
}
