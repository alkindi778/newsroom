<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            // User Management
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_users',
            
            // Article Management
            'manage_articles',
            'create_articles',
            'edit_articles',
            'delete_articles',
            'publish_articles',
            'view_articles',
            'edit_own_articles',
            'delete_own_articles',
            
            // Category Management
            'manage_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'view_categories',
            
            // Role & Permission Management
            'manage_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'manage_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
            
            // Trash Management
            'manage_trash',
            'restore_articles',
            'force_delete_articles',
            
            // System
            'view_dashboard',
            'manage_settings',
            'view_reports',
            'manage_media',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin - All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Most permissions except super admin stuff
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'manage_users', 'create_users', 'edit_users', 'view_users',
            'manage_articles', 'create_articles', 'edit_articles', 'delete_articles', 'publish_articles',
            'manage_categories', 'create_categories', 'edit_categories', 'delete_categories', 'view_categories',
            'view_dashboard', 'view_reports', 'manage_media'
        ]);

        // Editor - Article and category management
        $editor = Role::firstOrCreate(['name' => 'Editor']);
        $editor->givePermissionTo([
            'manage_articles', 'create_articles', 'edit_articles', 'publish_articles',
            'manage_categories', 'create_categories', 'edit_categories', 'view_categories',
            'view_dashboard', 'view_users', 'manage_media'
        ]);

        // Author - Own articles only
        $author = Role::firstOrCreate(['name' => 'Author']);
        $author->givePermissionTo([
            'create_articles', 'edit_own_articles', 'delete_own_articles',
            'view_categories', 'view_dashboard'
        ]);

        // Assign Super Admin role to the first user (if exists)
        $firstUser = User::first();
        if ($firstUser && !$firstUser->hasRole('Super Admin')) {
            $firstUser->assignRole('Super Admin');
        }
    }
}
