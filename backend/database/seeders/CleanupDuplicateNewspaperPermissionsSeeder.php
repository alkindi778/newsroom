<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CleanupDuplicateNewspaperPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الصلاحيات المكررة التي يجب حذفها (صيغة المفرد)
        $duplicatePermissions = [
            'view_newspaper_issue',
            'create_newspaper_issue',
            'edit_newspaper_issue',
            'delete_newspaper_issue',
            'feature_newspaper_issue',
            'unfeature_newspaper_issue',
            'publish_newspaper_issue',
            'unpublish_newspaper_issue',
        ];

        foreach ($duplicatePermissions as $permissionName) {
            // البحث عن جميع نسخ الصلاحية (قد تكون مكررة)
            $permissionIds = \DB::table('permissions')
                ->where('name', $permissionName)
                ->pluck('id');
            
            if ($permissionIds->count() > 0) {
                $this->command->info("Found {$permissionIds->count()} duplicate(s) of: {$permissionName}");
                
                foreach ($permissionIds as $permissionId) {
                    // نقل الأدوار إلى الصلاحية البديلة (بصيغة الجمع)
                    $alternativePermissionName = str_replace('_issue', '_issues', $permissionName);
                    $alternativePermission = \DB::table('permissions')
                        ->where('name', $alternativePermissionName)
                        ->where('guard_name', 'web')
                        ->first();
                    
                    if ($alternativePermission) {
                        // الحصول على أسماء الأدوار مباشرة من جدول الربط
                        $roleIds = \DB::table('role_has_permissions')
                            ->where('permission_id', $permissionId)
                            ->pluck('role_id');
                        
                        foreach ($roleIds as $roleId) {
                            // التحقق إذا كان الدور لديه الصلاحية البديلة
                            $exists = \DB::table('role_has_permissions')
                                ->where('role_id', $roleId)
                                ->where('permission_id', $alternativePermission->id)
                                ->exists();
                            
                            if (!$exists) {
                                \DB::table('role_has_permissions')->insert([
                                    'role_id' => $roleId,
                                    'permission_id' => $alternativePermission->id
                                ]);
                                $this->command->info("  → Migrated role ID {$roleId} to '{$alternativePermissionName}'");
                            }
                        }
                    }
                    
                    // حذف العلاقات من جدول الربط
                    \DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
                    \DB::table('model_has_permissions')->where('permission_id', $permissionId)->delete();
                    
                    // حذف الصلاحية المكررة
                    \DB::table('permissions')->where('id', $permissionId)->delete();
                    $this->command->info("  ✓ Deleted duplicate: {$permissionName}");
                }
            }
        }

        // تنظيف الـ cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ Cleanup completed successfully!');
    }
}
