<?php

namespace App\Repositories\Interfaces;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Get all categories
     */
    public function getAll(): Collection;

    /**
     * Get all categories with articles count
     */
    public function getAllWithCount(): Collection;

    /**
     * Find category by ID
     */
    public function findById(int $id): ?Category;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Create new category
     */
    public function create(array $data): Category;

    /**
     * Update category
     */
    public function update(Category $category, array $data): bool;

    /**
     * Delete category
     */
    public function delete(Category $category): bool;

    /**
     * Get active categories
     */
    public function getActive(): Collection;

    /**
     * Get inactive categories
     */
    public function getInactive(): Collection;

    /**
     * Get categories that have articles
     */
    public function getCategoriesWithArticles(): Collection;

    /**
     * Get categories without articles
     */
    public function getWithoutArticles(): Collection;

    /**
     * Get category statistics
     */
    public function getStatistics(): array;

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category): bool;

    /**
     * Check if category has articles
     */
    public function hasArticles(Category $category): bool;

    /**
     * Get articles count for category
     */
    public function getArticlesCount(Category $category): int;

    /**
     * Get popular categories (with most articles)
     */
    public function getPopular(int $limit = 5): Collection;

    /**
     * Search categories
     */
    public function search(string $query): Collection;

    /**
     * Get categories for sitemap
     */
    public function getForSitemap(): Collection;

    /**
     * Get category with its articles
     */
    public function getWithArticles(Category $category, int $limit = 10): Collection;
}
