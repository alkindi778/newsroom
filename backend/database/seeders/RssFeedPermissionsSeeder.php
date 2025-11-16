<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RssFeedPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات
        $permissions = [
            'view_rss_feeds',
            'create_rss_feeds',
            'edit_rss_feeds',
            'delete_rss_feeds',
            'manage_rss_feeds',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // منح الصلاحيات للأدوار
        
        // Admin: جميع الصلاحيات
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(array_merge(
                $adminRole->permissions->pluck('name')->toArray(),
                [
                    'view_rss_feeds',
                    'create_rss_feeds',
                    'edit_rss_feeds',
                    'delete_rss_feeds',
                    'manage_rss_feeds',
                ]
            ));
        }

        // Editor: عرض، إنشاء، تعديل
        $editorRole = Role::where('name', 'editor')->where('guard_name', 'web')->first();
        if ($editorRole) {
            $editorRole->syncPermissions(array_merge(
                $editorRole->permissions->pluck('name')->toArray(),
                [
                    'view_rss_feeds',
                    'create_rss_feeds',
                    'edit_rss_feeds',
                ]
            ));
        }

        // Manager: جميع الصلاحيات
        $managerRole = Role::where('name', 'manager')->where('guard_name', 'web')->first();
        if ($managerRole) {
            $managerRole->syncPermissions(array_merge(
                $managerRole->permissions->pluck('name')->toArray(),
                [
                    'view_rss_feeds',
                    'create_rss_feeds',
                    'edit_rss_feeds',
                    'delete_rss_feeds',
                    'manage_rss_feeds',
                ]
            ));
        }

        $this->command->info('✅ RSS Feed permissions created successfully!');
    }
}
