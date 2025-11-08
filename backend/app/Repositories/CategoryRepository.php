<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Article;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * Get all categories
     */
    public function getAll(): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get all categories with optional filters and pagination
     */
    public function getAllWithFilters(Request $request): LengthAwarePaginator
    {
        $query = $this->model->withCount('articles');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // Note: is_active column not implemented yet

        // Sorting options
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['created_at', 'updated_at', 'name', 'articles_count'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Get all categories with articles count
     */
    public function getAllWithCount(): Collection
    {
        return $this->model->withCount('articles')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find category by ID
     */
    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Create new category
     */
    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    /**
     * Update category
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Delete category
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * Get active categories
     */
    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)
                          ->orderBy('name', 'asc')
                          ->get();
    }

    /**
     * Get inactive categories
     */
    public function getInactive(): Collection
    {
        return $this->model->where('is_active', false)
                          ->orderBy('name', 'asc')
                          ->get();
    }

    /**
     * Get categories that have articles
     */
    public function getCategoriesWithArticles(): Collection
    {
        return $this->model->has('articles')
                          ->withCount('articles')
                          ->orderBy('articles_count', 'desc')
                          ->get();
    }

    /**
     * Get categories without articles
     */
    public function getWithoutArticles(): Collection
    {
        return $this->model->doesntHave('articles')
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    /**
     * Get category statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
            'with_articles' => $this->model->has('articles')->count(),
            'without_articles' => $this->model->doesntHave('articles')->count(),
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
     * Toggle category status
     */
    public function toggleStatus(Category $category): bool
    {
        return $category->update(['is_active' => !$category->is_active]);
    }

    /**
     * Check if category has articles
     */
    public function hasArticles(Category $category): bool
    {
        return Article::where('category_id', $category->id)->exists();
    }

    /**
     * Get articles count for category
     */
    public function getArticlesCount(Category $category): int
    {
        return Article::where('category_id', $category->id)->count();
    }

    /**
     * Get popular categories (with most articles)
     */
    public function getPopular(int $limit = 5): Collection
    {
        return $this->model->withCount('articles')
                          ->where('is_active', true)
                          ->orderBy('articles_count', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Search categories
     */
    public function search(string $query): Collection
    {
        return $this->model->where('name', 'like', '%' . $query . '%')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get categories for sitemap
     */
    public function getForSitemap(): Collection
    {
        return $this->model->select(['slug', 'updated_at'])
                          ->where('is_active', true)
                          ->orderBy('updated_at', 'desc')
                          ->get();
    }

    /**
     * Get category with its articles
     */
    public function getWithArticles(Category $category, int $limit = 10): Collection
    {
        return Article::where('category_id', $category->id)
                     ->with('user')
                     ->latest()
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get categories count by status
     */
    public function getCountByStatus(): array
    {
        return $this->model->selectRaw('is_active, COUNT(*) as count')
                          ->groupBy('is_active')
                          ->pluck('count', 'is_active')
                          ->toArray();
    }

    /**
     * Get most active category (with most articles)
     */
    public function getMostActive(): ?Category
    {
        return $this->model->withCount('articles')
                          ->where('is_active', true)
                          ->orderBy('articles_count', 'desc')
                          ->first();
    }

    /**
     * Get categories for dropdown/select
     */
    public function getForSelect(): Collection
    {
        return $this->model->where('is_active', true)
                          ->orderBy('name', 'asc')
                          ->select(['id', 'name', 'color'])
                          ->get();
    }

    /**
     * Get categories with color
     */
    public function getWithColor(): Collection
    {
        return $this->model->whereNotNull('color')
                          ->where('is_active', true)
                          ->orderBy('name', 'asc')
                          ->get();
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $categoryIds, bool $status): int
    {
        return $this->model->whereIn('id', $categoryIds)
                          ->update(['is_active' => $status]);
    }

    /**
     * Get recent categories
     */
    public function getRecent(int $limit = 5): Collection
    {
        return $this->model->latest()
                          ->limit($limit)
                          ->get();
    }
}
