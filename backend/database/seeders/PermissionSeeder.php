<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات للـ Newspaper Issues
        $permissions = [
            // View permissions
            'view_newspaper_issues',
            'view_newspaper_issue',
            
            // Create permissions
            'create_newspaper_issue',
            
            // Edit permissions
            'edit_newspaper_issue',
            
            // Delete permissions
            'delete_newspaper_issue',
            
            // Feature permissions
            'feature_newspaper_issue',
            'unfeature_newspaper_issue',
            
            // Publish permissions
            'publish_newspaper_issue',
            'unpublish_newspaper_issue',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // تعيين الصلاحيات للأدوار
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'api']);
        $writerRole = Role::firstOrCreate(['name' => 'writer', 'guard_name' => 'api']);

        // Admin - جميع الصلاحيات
        $adminRole->syncPermissions($permissions);

        // Editor - يمكنه إدارة الإصدارات
        $editorPermissions = [
            'view_newspaper_issues',
            'view_newspaper_issue',
            'create_newspaper_issue',
            'edit_newspaper_issue',
            'delete_newspaper_issue',
            'feature_newspaper_issue',
            'unfeature_newspaper_issue',
            'publish_newspaper_issue',
            'unpublish_newspaper_issue',
        ];
        $editorRole->syncPermissions($editorPermissions);

        // Writer - يمكنه فقط عرض الإصدارات
        $writerPermissions = [
            'view_newspaper_issues',
            'view_newspaper_issue',
        ];
        $writerRole->syncPermissions($writerPermissions);
    }
}
