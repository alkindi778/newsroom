<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة صلاحيات سلة المهملات
        $permissions = [
            'إدارة سلة المهملات',
            'استعادة الأخبار', 
            'حذف نهائي للأخبار',
            'manage_trash',
            'restore_articles',
            'force_delete_articles'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // إعطاء الصلاحيات للأدوار الموجودة
        
        // Super Admin - جميع الصلاحيات
        $superAdmin = Role::where('name', 'مدير عام')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Admin - صلاحيات إدارة سلة المهملات
        $admin = Role::where('name', 'مدير')->first();
        if ($admin) {
            $admin->givePermissionTo([
                'إدارة سلة المهملات',
                'استعادة الأخبار', 
                'حذف نهائي للأخبار'
            ]);
        }

        // Editor - صلاحيات محدودة لسلة المهملات
        $editor = Role::where('name', 'محرر')->first();
        if ($editor) {
            $editor->givePermissionTo([
                'إدارة سلة المهملات',
                'استعادة الأخبار'
            ]);
        }

        // إعطاء الصلاحيات للمستخدم الأول (إذا وجد)
        $firstUser = \App\Models\User::first();
        if ($firstUser && $firstUser->can('حذف الأخبار')) {
            $firstUser->givePermissionTo([
                'إدارة سلة المهملات',
                'استعادة الأخبار',
                'حذف نهائي للأخبار'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة صلاحيات سلة المهملات
        $permissions = [
            'إدارة سلة المهملات',
            'استعادة الأخبار', 
            'حذف نهائي للأخبار',
            'manage_trash',
            'restore_articles',
            'force_delete_articles'
        ];

        foreach ($permissions as $permission) {
            $permissionModel = Permission::where('name', $permission)->first();
            if ($permissionModel) {
                $permissionModel->delete();
            }
        }
    }
};
