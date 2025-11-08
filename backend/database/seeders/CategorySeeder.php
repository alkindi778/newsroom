<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'سياسة', 'slug' => 'politics'],
            ['name' => 'اقتصاد', 'slug' => 'economy'],
            ['name' => 'رياضة', 'slug' => 'sports'],
            ['name' => 'تكنولوجيا', 'slug' => 'technology'],
            ['name' => 'صحة', 'slug' => 'health'],
            ['name' => 'ثقافة', 'slug' => 'culture'],
            ['name' => 'علوم', 'slug' => 'science'],
            ['name' => 'فن', 'slug' => 'art'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
