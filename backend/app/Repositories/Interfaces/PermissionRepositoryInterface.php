<?php

namespace App\Repositories\Interfaces;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    /**
     * Get all permissions
     */
    public function getAll(): Collection;

    /**
     * Get all permissions with roles count
     */
    public function getAllWithCount(): Collection;

    /**
     * Get permissions grouped by category
     */
    public function getGroupedByCategory();

    /**
     * Find permission by ID
     */
    public function findById(int $id): ?Permission;

    /**
     * Find permission by name
     */
    public function findByName(string $name): ?Permission;

    /**
     * Create new permission
     */
    public function create(array $data): Permission;

    /**
     * Update permission
     */
    public function update(Permission $permission, array $data): bool;

    /**
     * Delete permission
     */
    public function delete(Permission $permission): bool;

    /**
     * Get permission with roles
     */
    public function getPermissionWithRoles(int $id): ?Permission;

    /**
     * Check if permission has roles
     */
    public function hasRoles(Permission $permission): bool;

    /**
     * Get roles count for permission
     */
    public function getRolesCount(Permission $permission): int;

    /**
     * Get permissions by category
     */
    public function getByCategory(string $category): Collection;

    /**
     * Get available categories
     */
    public function getCategories(): array;

    /**
     * Search permissions
     */
    public function search(string $query): Collection;
}
