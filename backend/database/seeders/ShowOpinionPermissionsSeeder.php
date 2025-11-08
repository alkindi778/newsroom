<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ShowOpinionPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== الصلاحيات المتعلقة بمقالات الرأي ===\n\n";
        
        $opinionPermissions = Permission::where('name', 'like', '%رأي%')
            ->orWhere('name', 'like', '%كُتاب%')
            ->get();
        
        foreach ($opinionPermissions as $permission) {
            echo "- {$permission->name}\n";
        }
        
        echo "\n=== عدد الصلاحيات: " . $opinionPermissions->count() . " ===\n";
    }
}
