<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create new user with role assignment
     */
    public function createUser(array $data): User
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $data['avatar'] = $this->handleAvatarUpload($data['avatar']);
        }

        // Set default status if not provided
        $data['status'] = $data['status'] ?? true;

        $user = $this->userRepository->create($data);

        // Assign roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->assignRole($data['roles']);
        }

        // Assign permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $user->givePermissionTo($data['permissions']);
        }

        return $user;
    }

    /**
     * Update user with role management
     */
    public function updateUser(User $user, array $data): bool
    {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $this->handleAvatarUpload($data['avatar']);
        }

        $updated = $this->userRepository->update($user, $data);

        if ($updated) {
            // Update roles if provided
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            // Update permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }
        }

        return $updated;
    }

    /**
     * Delete user with safety checks
     */
    public function deleteUser(User $user): array
    {
        // Check if user has articles
        $articlesCount = $user->articles()->count();
        
        if ($articlesCount > 0) {
            return [
                'success' => false,
                'message' => "لا يمكن حذف المستخدم لأنه يملك {$articlesCount} مقال. يرجى حذف أو نقل المقالات أولاً."
            ];
        }

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $deleted = $this->userRepository->delete($user);

        return [
            'success' => $deleted,
            'message' => $deleted ? 'تم حذف المستخدم بنجاح' : 'فشل في حذف المستخدم'
        ];
    }

    /**
     * Handle avatar upload
     */
    private function handleAvatarUpload(UploadedFile $file): string
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('avatars', $filename, 'public');
    }

    /**
     * Get user insights
     */
    public function getUserInsights(User $user): array
    {
        $articlesCount = $user->articles()->count();
        // الآن جدول articles يحتوي على is_published بعد تشغيل Migration
        $publishedArticles = $user->articles()->where('is_published', true)->count();
        $draftArticles = $user->articles()->where('is_published', false)->count();
        $recentArticles = $user->articles()->where('created_at', '>=', now()->subDays(30))->count();

        $roles = $user->getRoleNames()->toArray();
        $permissions = $user->getPermissionNames()->toArray();

        return [
            'articles' => [
                'total' => $articlesCount,
                'published' => $publishedArticles,
                'draft' => $draftArticles,
                'recent' => $recentArticles,
            ],
            'roles' => $roles,
            'permissions' => $permissions,
            'activity' => [
                'last_login' => $user->last_login_at,
                'member_since' => $user->created_at,
                'status' => $user->status ? 'نشط' : 'غير نشط',
            ],
        ];
    }

    /**
     * Bulk action on users
     */
    public function bulkAction(string $action, array $userIds): array
    {
        $affected = 0;
        $message = '';

        switch ($action) {
            case 'activate':
                $affected = $this->userRepository->bulkUpdateStatus($userIds, true);
                $message = "تم تفعيل {$affected} مستخدم";
                break;

            case 'deactivate':
                $affected = $this->userRepository->bulkUpdateStatus($userIds, false);
                $message = "تم إلغاء تفعيل {$affected} مستخدم";
                break;

            case 'delete':
                $users = User::whereIn('id', $userIds)->get();
                $deleted = 0;
                $skipped = 0;

                foreach ($users as $user) {
                    $result = $this->deleteUser($user);
                    if ($result['success']) {
                        $deleted++;
                    } else {
                        $skipped++;
                    }
                }

                $affected = $deleted;
                $message = "تم حذف {$deleted} مستخدم";
                if ($skipped > 0) {
                    $message .= " وتم تخطي {$skipped} مستخدم";
                }
                break;

            default:
                return ['success' => false, 'message' => 'عملية غير صحيحة'];
        }

        return [
            'success' => $affected > 0,
            'message' => $message,
            'affected' => $affected
        ];
    }

    /**
     * Get all roles for form select
     */
    public function getAllRoles(): array
    {
        return Role::all()->pluck('name', 'id')->toArray();
    }

    /**
     * Get all permissions for form select
     */
    public function getAllPermissions(): array
    {
        return Permission::all()->pluck('name', 'id')->toArray();
    }

    /**
     * Create default roles and permissions
     */
    public function createDefaultRolesAndPermissions(): void
    {
        // Create permissions
        $permissions = [
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_users',
            'manage_articles',
            'create_articles',
            'edit_articles',
            'delete_articles',
            'publish_articles',
            'manage_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'manage_roles',
            'manage_permissions',
            'view_dashboard',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $editor = Role::firstOrCreate(['name' => 'Editor']);
        $author = Role::firstOrCreate(['name' => 'Author']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());
        
        $admin->givePermissionTo([
            'manage_users', 'create_users', 'edit_users', 'view_users',
            'manage_articles', 'create_articles', 'edit_articles', 'delete_articles', 'publish_articles',
            'manage_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_dashboard'
        ]);

        $editor->givePermissionTo([
            'manage_articles', 'create_articles', 'edit_articles', 'publish_articles',
            'manage_categories', 'create_categories', 'edit_categories',
            'view_dashboard', 'view_users'
        ]);

        $author->givePermissionTo([
            'create_articles', 'edit_articles',
            'view_dashboard'
        ]);
    }

    /**
     * Assign role to user
     */
    public function assignRole(User $user, string $roleName): bool
    {
        try {
            $user->assignRole($roleName);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(User $user, string $roleName): bool
    {
        try {
            $user->removeRole($roleName);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
