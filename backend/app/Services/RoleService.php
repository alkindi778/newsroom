<?php

namespace App\Services;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RoleService
{
    protected $roleRepository;
    protected $permissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Create new role with permissions
     */
    public function createRole(array $data): Role
    {
        $role = $this->roleRepository->create(['name' => $data['name']]);

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $this->roleRepository->syncPermissions($role, $data['permissions']);
        }

        return $role;
    }

    /**
     * Update role with permissions
     */
    public function updateRole(Role $role, array $data): bool
    {
        $updated = $this->roleRepository->update($role, ['name' => $data['name']]);

        if ($updated) {
            $permissions = $data['permissions'] ?? [];
            $this->roleRepository->syncPermissions($role, $permissions);
        }

        return $updated;
    }

    /**
     * Delete role with safety checks
     */
    public function deleteRole(Role $role): array
    {
        // Check if role has users
        if ($this->roleRepository->hasUsers($role)) {
            $usersCount = $this->roleRepository->getUsersCount($role);
            return [
                'success' => false,
                'message' => "لا يمكن حذف الدور لأنه مرتبط بـ {$usersCount} مستخدم. يرجى إزالة الدور من المستخدمين أولاً."
            ];
        }

        $deleted = $this->roleRepository->delete($role);

        return [
            'success' => $deleted,
            'message' => $deleted ? 'تم حذف الدور بنجاح' : 'فشل في حذف الدور'
        ];
    }

    /**
     * Get role insights
     */
    public function getRoleInsights(Role $role): array
    {
        $roleWithData = $this->roleRepository->getRoleWithUsers($role->id);
        $permissions = $roleWithData->permissions;
        $users = $roleWithData->users;

        return [
            'permissions' => $permissions, // إرسال Collection مباشرة
            'users' => $users, // إرسال Collection مباشرة
            'insights' => [
                'permissions' => [
                    'count' => $permissions->count(),
                    'list' => $permissions->pluck('name')->toArray(),
                    'categories' => $this->groupPermissionsByCategory($permissions),
                ],
                'users' => [
                    'count' => $roleWithData->users_count,
                    'recent' => $users->take(10),
                    'has_more' => $roleWithData->users_count > 10,
                ],
                'statistics' => [
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ],
            ]
        ];
    }

    /**
     * Get all permissions grouped by category for forms
     */
    public function getPermissionsGrouped()
    {
        return $this->permissionRepository->getGroupedByCategory();
    }

    /**
     * Get role permissions as array (for forms)
     */
    public function getRolePermissions(Role $role): array
    {
        return $role->permissions->pluck('name')->toArray();
    }

    /**
     * Group permissions by category
     */
    private function groupPermissionsByCategory($permissions): array
    {
        return $permissions->groupBy(function($permission) {
            return explode('_', $permission->name)[1] ?? 'other';
        })->map(function($group) {
            return $group->pluck('name');
        })->toArray();
    }

    /**
     * Search roles with filters and pagination
     */
    public function searchRoles(Request $request): array
    {
        $roles = $this->roleRepository->getAllWithFilters($request);

        return [
            'roles' => $roles,
            'statistics' => $this->roleRepository->getStatistics(),
        ];
    }

    /**
     * Get role statistics for dashboard
     */
    public function getRoleStatistics(): array
    {
        return $this->roleRepository->getStatistics();
    }
}
