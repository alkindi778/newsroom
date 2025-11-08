<?php

namespace App\Repositories\Interfaces;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ArticleRepositoryInterface
{
    /**
     * Get all articles with optional filters and pagination
     */
    public function getAllWithFilters(Request $request): LengthAwarePaginator;

    /**
     * Get all articles
     */
    public function getAll(): Collection;

    /**
     * Find article by ID
     */
    public function findById(int $id): ?Article;

    /**
     * Find article by slug
     */
    public function findBySlug(string $slug): ?Article;

    /**
     * Create new article
     */
    public function create(array $data): Article;

    /**
     * Update article
     */
    public function update(Article $article, array $data): bool;

    /**
     * Delete article
     */
    public function delete(Article $article): bool;

    /**
     * Get published articles
     */
    public function getPublished(): Collection;

    /**
     * Get recent articles
     */
    public function getRecent(int $limit = 5): Collection;

    /**
     * Get articles by category
     */
    public function getByCategory(int $categoryId): Collection;

    /**
     * Get articles by user
     */
    public function getByUser(int $userId): Collection;

    /**
     * Search articles
     */
    public function search(string $query): Collection;

    /**
     * Get article statistics
     */
    public function getStatistics(): array;

    /**
     * Toggle article status
     */
    public function toggleStatus(Article $article): bool;

    /**
     * Get articles for sitemap
     */
    public function getForSitemap(): Collection;
}
