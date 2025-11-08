<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    /**
     * Get all users with pagination
     */
    public function getAllWithPagination(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User;

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create new user
     */
    public function create(array $data): User;

    /**
     * Update user
     */
    public function update(User $user, array $data): bool;

    /**
     * Delete user
     */
    public function delete(User $user): bool;

    /**
     * Get active users
     */
    public function getActive(): Collection;

    /**
     * Get inactive users
     */
    public function getInactive(): Collection;

    /**
     * Search users
     */
    public function search(string $query): Collection;

    /**
     * Get users with specific role
     */
    public function getUsersByRole(string $role): Collection;

    /**
     * Get users with specific permission
     */
    public function getUsersWithPermission(string $permission): Collection;

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user): bool;

    /**
     * Get user statistics
     */
    public function getStatistics(): array;

    /**
     * Get recently registered users
     */
    public function getRecentUsers(int $limit = 10): Collection;

    /**
     * Get users with most articles
     */
    public function getUsersWithMostArticles(int $limit = 10): Collection;

    /**
     * Update last login time
     */
    public function updateLastLogin(User $user): bool;

    /**
     * Get online users (recently active)
     */
    public function getOnlineUsers(int $minutesThreshold = 15): Collection;

    /**
     * Bulk update users status
     */
    public function bulkUpdateStatus(array $userIds, bool $status): int;

    /**
     * Get users by filters
     */
    public function getByFilters(Request $request): LengthAwarePaginator;

    /**
     * Get users count by role
     */
    public function getCountByRole(): array;
}
