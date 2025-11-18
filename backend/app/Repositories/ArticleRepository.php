<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ArticleRepository implements ArticleRepositoryInterface
{
    protected $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    /**
     * Get all articles with optional filters and pagination
     */
    public function getAllWithFilters(Request $request): LengthAwarePaginator
    {
        $query = $this->model->with(['category', 'user']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%')
                  ->orWhere('keywords', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by status  
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by author
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by single date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Sorting options - دائماً من الأحدث للأقدم حسب تاريخ النشر
        $sortField = $request->get('sort', 'published_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['id', 'created_at', 'updated_at', 'title', 'published_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            // الترتيب الافتراضي: من الأحدث للأقدم حسب تاريخ النشر
            // إذا كان published_at فارغ، نستخدم id (الأحدث = ID أكبر)
            $query->orderByRaw('COALESCE(published_at, created_at) DESC');
        }

        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Get all articles
     */
    public function getAll(): Collection
    {
        return $this->model->with(['category', 'user'])->get();
    }

    /**
     * Find article by ID
     */
    public function findById(int $id): ?Article
    {
        return $this->model->with(['category', 'user'])->find($id);
    }

    /**
     * Find article by slug
     */
    public function findBySlug(string $slug): ?Article
    {
        return $this->model->with(['category', 'user'])->where('slug', $slug)->first();
    }

    /**
     * Create new article
     */
    public function create(array $data): Article
    {
        return $this->model->create($data);
    }

    /**
     * Update article
     */
    public function update(Article $article, array $data): bool
    {
        return $article->update($data);
    }

    /**
     * Delete article
     */
    public function delete(Article $article): bool
    {
        return $article->delete();
    }

    /**
     * Get published articles
     */
    public function getPublished(): Collection
    {
        return $this->model->with(['category', 'user'])
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * Get recent articles
     */
    public function getRecent(int $limit = 5): Collection
    {
        return $this->model->with(['category', 'user'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get articles by category
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->model->with(['category', 'user'])
            ->where('category_id', $categoryId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get articles by user
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model->with(['category', 'user'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Search articles
     */
    public function search(string $query): Collection
    {
        return $this->model->with(['category', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('content', 'like', '%' . $query . '%')
                  ->orWhere('keywords', 'like', '%' . $query . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get article statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'published' => $this->model->where('is_published', true)->count(),
            'draft' => $this->model->where('is_published', false)->count(),
            'today' => $this->model->whereDate('created_at', today())->count(),
            'this_week' => $this->model->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => $this->model->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Toggle article status
     */
    public function toggleStatus(Article $article): bool
    {
        $newIsPublished = !$article->is_published;
        
        $updateData = ['is_published' => $newIsPublished];
        
        // If publishing, set published_at if not already set
        if ($newIsPublished && !$article->published_at) {
            $updateData['published_at'] = now();
        } elseif (!$newIsPublished) {
            $updateData['published_at'] = null;
        }
        
        return $article->update($updateData);
    }

    /**
     * Get articles for sitemap
     */
    public function getForSitemap(): Collection
    {
        return $this->model->select(['slug', 'updated_at'])
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Get articles count by status
     */
    public function getCountByStatus(): array
    {
        return [
            'published' => $this->model->where('is_published', true)->count(),
            'draft' => $this->model->where('is_published', false)->count(),
        ];
    }

    /**
     * Get most recent published articles
     */
    public function getRecentPublished(int $limit = 10): Collection
    {
        return $this->model->with(['category', 'user'])
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured articles (you can add a featured column later)
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model->with(['category', 'user'])
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending approval articles
     */
    public function getPendingApproval()
    {
        return $this->model->with(['category', 'user'])
            ->where('approval_status', 'pending_approval')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Get articles by user with filters
     */
    public function getUserArticles(int $userId, Request $request = null)
    {
        $query = $this->model->with(['category'])
            ->where('user_id', $userId);

        if ($request && $request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * Get rejected articles
     */
    public function getRejectedArticles()
    {
        return $this->model->with(['category', 'user', 'rejectedBy'])
            ->where('approval_status', 'rejected')
            ->orderBy('rejected_at', 'desc')
            ->paginate(10);
    }
}
