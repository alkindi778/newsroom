<?php

namespace App\Repositories\Interfaces;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    /**
     * Get all roles
     */
    public function getAll(): Collection;

    /**
     * Get all roles with permissions and users count
     */
    public function getAllWithCounts(): Collection;

    /**
     * Find role by ID
     */
    public function findById(int $id): ?Role;

    /**
     * Find role by name
     */
    public function findByName(string $name): ?Role;

    /**
     * Create new role
     */
    public function create(array $data): Role;

    /**
     * Update role
     */
    public function update(Role $role, array $data): bool;

    /**
     * Delete role
     */
    public function delete(Role $role): bool;

    /**
     * Assign permissions to role
     */
    public function assignPermissions(Role $role, array $permissions): void;

    /**
     * Sync permissions to role
     */
    public function syncPermissions(Role $role, array $permissions): void;

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions(int $id): ?Role;

    /**
     * Get role with users
     */
    public function getRoleWithUsers(int $id, int $limit = 10): ?Role;

    /**
     * Get roles statistics
     */
    public function getStatistics(): array;

    /**
     * Check if role has users
     */
    public function hasUsers(Role $role): bool;

    /**
     * Get users count for role
     */
    public function getUsersCount(Role $role): int;

    /**
     * Search roles
     */
    public function search(string $query): Collection;
}
