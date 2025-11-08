<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionRepository implements PermissionRepositoryInterface
{
    protected $model;

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    /**
     * Get all permissions
     */
    public function getAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * Get all permissions with roles count
     */
    public function getAllWithCount(): Collection
    {
        return $this->model->withCount('roles')
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get all permissions with optional filters and pagination
     */
    public function getAllWithFilters(Request $request): LengthAwarePaginator
    {
        $query = $this->model->withCount('roles');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $category = $request->category;
            $query->where(function ($q) use ($category) {
                $q->where('name', 'LIKE', "%_{$category}%")
                  ->orWhere('name', 'LIKE', "{$category}_%");
            });
        }

        // Filter by permissions with roles
        if ($request->filled('has_roles')) {
            if ($request->has_roles === 'yes') {
                $query->has('roles');
            } elseif ($request->has_roles === 'no') {
                $query->doesntHave('roles');
            }
        }

        // Sorting options
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        $allowedSortFields = ['name', 'created_at', 'roles_count'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name');
        }

        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Get permissions grouped by category
     */
    public function getGroupedByCategory()
    {
        $permissions = $this->model->withCount('roles')->get();
        
        // ترتيب مخصص للمجموعات (متطابق مع الأقسام الفعلية)
        $categoryOrder = [
            'لوحة التحكم' => ['عرض لوحة التحكم', 'إدارة لوحة التحكم', 'view_dashboard'],
            'المستخدمين' => ['عرض المستخدمين', 'إنشاء المستخدمين', 'تعديل المستخدمين', 'حذف المستخدمين', 'إدارة المستخدمين', 'view_users', 'create_users', 'edit_users', 'delete_users', 'manage_users'],
            'الأدوار والصلاحيات' => ['إدارة الأدوار', 'إنشاء الأدوار', 'تعديل الأدوار', 'حذف الأدوار', 'إدارة الصلاحيات', 'إنشاء الصلاحيات', 'تعديل الصلاحيات', 'حذف الصلاحيات', 'manage_roles', 'create_roles', 'edit_roles', 'delete_roles', 'manage_permissions', 'create_permissions', 'edit_permissions', 'delete_permissions'],
            'الأخبار' => ['إدارة الأخبار', 'إنشاء الأخبار', 'تعديل الأخبار', 'نشر الأخبار', 'حذف الأخبار', 'عرض الأخبار', 'manage_articles', 'create_articles', 'edit_articles', 'publish_articles', 'delete_articles', 'view_articles'],
            'سلة المهملات' => ['إدارة سلة المهملات', 'استعادة الأخبار', 'حذف نهائي للأخبار', 'manage_trash', 'restore_articles', 'force_delete_articles'],
            'الأخبار الشخصية' => ['تعديل الأخبار الشخصية', 'حذف الأخبار الشخصية', 'edit_own_articles', 'delete_own_articles'],
            'الأقسام' => ['عرض الأقسام', 'إنشاء الأقسام', 'تعديل الأقسام', 'حذف الأقسام', 'إدارة الأقسام', 'view_categories', 'create_categories', 'edit_categories', 'delete_categories', 'manage_categories'],
            'كُتاب الرأي' => ['عرض كُتاب الرأي', 'إنشاء كُتاب الرأي', 'تعديل كُتاب الرأي', 'حذف كُتاب الرأي', 'إدارة كُتاب الرأي'],
            'مقالات الرأي' => ['عرض مقالات الرأي', 'إنشاء مقالات الرأي', 'تعديل مقالات الرأي', 'حذف مقالات الرأي', 'نشر مقالات الرأي', 'إدارة مقالات الرأي'],
            'النظام' => ['manage_media', 'view_reports', 'manage_settings']
        ];
        
        $groupedPermissions = collect();
        
        // ترتيب الصلاحيات حسب المجموعات المحددة
        foreach ($categoryOrder as $categoryName => $permissionNames) {
            $categoryPermissions = collect();
            
            foreach ($permissionNames as $permissionName) {
                $permission = $permissions->firstWhere('name', $permissionName);
                if ($permission) {
                    $categoryPermissions->push($permission);
                }
            }
            
            if ($categoryPermissions->count() > 0) {
                $groupedPermissions->put($categoryName, $categoryPermissions);
            }
        }
        
        // إضافة أي صلاحيات متبقية في مجموعة "أخرى"
        $usedPermissions = $groupedPermissions->flatten();
        $remainingPermissions = $permissions->reject(function($permission) use ($usedPermissions) {
            return $usedPermissions->contains('id', $permission->id);
        });
        
        if ($remainingPermissions->count() > 0) {
            $groupedPermissions->put('أخرى', $remainingPermissions);
        }
        
        return $groupedPermissions;
    }

    /**
     * Find permission by ID
     */
    public function findById(int $id): ?Permission
    {
        return $this->model->find($id);
    }

    /**
     * Find permission by name
     */
    public function findByName(string $name): ?Permission
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Create new permission
     */
    public function create(array $data): Permission
    {
        return $this->model->create($data);
    }

    /**
     * Update permission
     */
    public function update(Permission $permission, array $data): bool
    {
        return $permission->update($data);
    }

    /**
     * Delete permission
     */
    public function delete(Permission $permission): bool
    {
        return $permission->delete();
    }

    /**
     * Get permission with roles
     */
    public function getPermissionWithRoles(int $id): ?Permission
    {
        return $this->model->with('roles')->find($id);
    }

    /**
     * Check if permission has roles
     */
    public function hasRoles(Permission $permission): bool
    {
        return $permission->roles()->exists();
    }

    /**
     * Get roles count for permission
     */
    public function getRolesCount(Permission $permission): int
    {
        return $permission->roles()->count();
    }

    /**
     * Get permissions by category
     */
    public function getByCategory(string $category): Collection
    {
        return $this->model->where('name', 'LIKE', "%_{$category}")
                          ->orWhere('name', 'LIKE', "{$category}_%")
                          ->withCount('roles')
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get available categories
     */
    public function getCategories(): array
    {
        return $this->model->get()
                          ->map(function($permission) {
                              return explode('_', $permission->name)[1] ?? 'other';
                          })
                          ->unique()
                          ->values()
                          ->toArray();
    }

    /**
     * Search permissions
     */
    public function search(string $query): Collection
    {
        return $this->model->where('name', 'LIKE', "%{$query}%")
                          ->withCount('roles')
                          ->get();
    }
}
