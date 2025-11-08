<?php

namespace App\Repositories\Interfaces;

use App\Models\Advertisement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdvertisementRepositoryInterface
{
    /**
     * Get all advertisements with filters
     */
    public function getAllWithFilters(array $filters = []): LengthAwarePaginator;

    /**
     * Get advertisement by ID
     */
    public function findById(int $id): ?Advertisement;

    /**
     * Get advertisement by slug
     */
    public function findBySlug(string $slug): ?Advertisement;

    /**
     * Create new advertisement
     */
    public function create(array $data): Advertisement;

    /**
     * Update advertisement
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete advertisement
     */
    public function delete(int $id): bool;

    /**
     * Get active advertisements
     */
    public function getActive(): Collection;

    /**
     * Get currently active advertisements (within date range)
     */
    public function getCurrentlyActive(): Collection;

    /**
     * Get advertisements by type
     */
    public function getByType(string $type): Collection;

    /**
     * Get advertisements by position
     */
    public function getByPosition(string $position, bool $activeOnly = true): Collection;

    /**
     * Get advertisements for specific page
     */
    public function getForPage(string $page, string $device = null): Collection;

    /**
     * Get advertisements for specific category
     */
    public function getForCategory(int $categoryId, string $device = null): Collection;

    /**
     * Toggle advertisement status
     */
    public function toggleStatus(int $id): bool;

    /**
     * Update advertisement priority
     */
    public function updatePriority(int $id, int $priority): bool;

    /**
     * Increment views
     */
    public function incrementViews(int $id): bool;

    /**
     * Increment clicks
     */
    public function incrementClicks(int $id): bool;

    /**
     * Get statistics
     */
    public function getStatistics(): array;

    /**
     * Get expired advertisements
     */
    public function getExpired(): Collection;

    /**
     * Get scheduled advertisements
     */
    public function getScheduled(): Collection;

    /**
     * Search advertisements
     */
    public function search(string $query): Collection;

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $ids, bool $status): int;

    /**
     * Bulk delete
     */
    public function bulkDelete(array $ids): int;
}
