<?php

namespace App\Services;

use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionService
{
    protected $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Create new permission
     */
    public function createPermission(array $data): Permission
    {
        // Validate permission name format
        $name = $this->validatePermissionName($data['name']);
        
        return $this->permissionRepository->create(['name' => $name]);
    }

    /**
     * Update permission
     */
    public function updatePermission(Permission $permission, array $data): bool
    {
        // Validate permission name format
        $name = $this->validatePermissionName($data['name']);
        
        return $this->permissionRepository->update($permission, ['name' => $name]);
    }

    /**
     * Delete permission with safety checks
     */
    public function deletePermission(Permission $permission): array
    {
        // Check if permission has roles
        if ($this->permissionRepository->hasRoles($permission)) {
            $rolesCount = $this->permissionRepository->getRolesCount($permission);
            return [
                'success' => false,
                'message' => "لا يمكن حذف الصلاحية لأنها مرتبطة بـ {$rolesCount} دور. يرجى إزالة الصلاحية من الأدوار أولاً."
            ];
        }

        $deleted = $this->permissionRepository->delete($permission);

        return [
            'success' => $deleted,
            'message' => $deleted ? 'تم حذف الصلاحية بنجاح' : 'فشل في حذف الصلاحية'
        ];
    }

    /**
     * Get permission insights
     */
    public function getPermissionInsights(Permission $permission): array
    {
        $permissionWithRoles = $this->permissionRepository->getPermissionWithRoles($permission->id);
        
        return [
            'roles' => [
                'count' => $permissionWithRoles->roles->count(),
                'list' => $permissionWithRoles->roles->pluck('name')->toArray(),
                'details' => $permissionWithRoles->roles,
            ],
            'category' => $this->getPermissionCategory($permission->name),
            'statistics' => [
                'created_at' => $permission->created_at,
                'updated_at' => $permission->updated_at,
            ],
        ];
    }

    /**
     * Get permissions grouped by category
     */
    public function getPermissionsGrouped()
    {
        return $this->permissionRepository->getGroupedByCategory();
    }

    /**
     * Get available permission categories
     */
    public function getPermissionCategories(): array
    {
        return $this->permissionRepository->getCategories();
    }

    /**
     * Search permissions with filters and pagination
     */
    public function searchPermissions(Request $request): array
    {
        $permissions = $this->permissionRepository->getAllWithFilters($request);

        return [
            'permissions' => $permissions,
            'categories' => $this->getPermissionCategories(),
            'grouped_permissions' => $permissions->groupBy(function($permission) {
                return explode('_', $permission->name)[1] ?? 'other';
            })
        ];
    }

    /**
     * Validate permission name format
     */
    private function validatePermissionName(string $name): string
    {
        // Convert to lowercase and replace spaces with underscores
        $name = strtolower(str_replace(' ', '_', trim($name)));
        
        // Validate format (action_resource or resource_action)
        if (!preg_match('/^[a-z_]+$/', $name)) {
            throw new \InvalidArgumentException('اسم الصلاحية يجب أن يحتوي على أحرف وشرطات سفلية فقط');
        }
        
        return $name;
    }

    /**
     * Get permission category from name
     */
    private function getPermissionCategory(string $permissionName): string
    {
        $parts = explode('_', $permissionName);
        return $parts[1] ?? 'other';
    }

    /**
     * Generate suggested permissions for a resource
     */
    public function generateResourcePermissions(string $resource): array
    {
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];
        $permissions = [];
        
        foreach ($actions as $action) {
            $permissions[] = $action . '_' . strtolower($resource);
        }
        
        return $permissions;
    }

    /**
     * Get permission statistics
     */
    public function getPermissionStatistics(): array
    {
        $all = $this->permissionRepository->getAllWithCount();
        $categories = $this->getPermissionCategories();
        
        return [
            'total' => $all->count(),
            'categories_count' => count($categories),
            'with_roles' => $all->where('roles_count', '>', 0)->count(),
            'without_roles' => $all->where('roles_count', 0)->count(),
            'by_category' => $all->groupBy(function($permission) {
                return explode('_', $permission->name)[1] ?? 'other';
            })->map->count()->toArray(),
        ];
    }
}
