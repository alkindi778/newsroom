<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository implements RoleRepositoryInterface
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    /**
     * Get all roles
     */
    public function getAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * Get all roles with permissions and users count
     */
    public function getAllWithCounts(): Collection
    {
        return $this->model->withCount(['permissions', 'users'])
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get all roles with optional filters and pagination
     */
    public function getAllWithFilters(Request $request): LengthAwarePaginator
    {
        $query = $this->model->withCount(['permissions', 'users']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // Filter by roles with users
        if ($request->filled('has_users')) {
            if ($request->has_users === 'yes') {
                $query->has('users');
            } elseif ($request->has_users === 'no') {
                $query->doesntHave('users');
            }
        }

        // Filter by roles with permissions
        if ($request->filled('has_permissions')) {
            if ($request->has_permissions === 'yes') {
                $query->has('permissions');
            } elseif ($request->has_permissions === 'no') {
                $query->doesntHave('permissions');
            }
        }

        // Sorting options
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        $allowedSortFields = ['name', 'created_at', 'permissions_count', 'users_count'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name');
        }

        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Find role by ID
     */
    public function findById(int $id): ?Role
    {
        return $this->model->find($id);
    }

    /**
     * Find role by name
     */
    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Create new role
     */
    public function create(array $data): Role
    {
        return $this->model->create($data);
    }

    /**
     * Update role
     */
    public function update(Role $role, array $data): bool
    {
        return $role->update($data);
    }

    /**
     * Delete role
     */
    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(Role $role, array $permissions): void
    {
        $role->givePermissionTo($permissions);
    }

    /**
     * Sync permissions to role
     */
    public function syncPermissions(Role $role, array $permissions): void
    {
        $role->syncPermissions($permissions);
    }

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions(int $id): ?Role
    {
        return $this->model->with('permissions')->find($id);
    }

    /**
     * Get role with users
     */
    public function getRoleWithUsers(int $id, int $limit = 10): ?Role
    {
        return $this->model->with(['users' => function($query) use ($limit) {
                                $query->limit($limit);
                            }])
                            ->withCount('users')
                            ->find($id);
    }

    /**
     * Get roles statistics
     */
    public function getStatistics(): array
    {
        $total = $this->model->count();
        $withPermissions = $this->model->has('permissions')->count();
        $withUsers = $this->model->has('users')->count();
        $empty = $this->model->doesntHave('permissions')->doesntHave('users')->count();

        return [
            'total' => $total,
            'with_permissions' => $withPermissions,
            'with_users' => $withUsers,
            'empty' => $empty,
        ];
    }

    /**
     * Check if role has users
     */
    public function hasUsers(Role $role): bool
    {
        return $role->users()->exists();
    }

    /**
     * Get users count for role
     */
    public function getUsersCount(Role $role): int
    {
        return $role->users()->count();
    }

    /**
     * Search roles
     */
    public function search(string $query): Collection
    {
        return $this->model->where('name', 'LIKE', "%{$query}%")
                          ->withCount(['permissions', 'users'])
                          ->get();
    }
}
