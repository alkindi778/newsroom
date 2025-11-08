<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomepageSection;

class HomepageSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'name' => 'السلايدر الرئيسي',
                'slug' => 'main-slider',
                'type' => 'slider',
                'title' => null,
                'subtitle' => null,
                'category_id' => null,
                'order' => 1,
                'items_count' => 5,
                'is_active' => true,
                'settings' => [
                    'autoplay' => true,
                    'interval' => 5000,
                    'show_indicators' => true,
                ],
            ],
            [
                'name' => 'آخر الأخبار',
                'slug' => 'latest-news',
                'type' => 'latest_news',
                'title' => 'آخر الأخبار',
                'subtitle' => null,
                'category_id' => null,
                'order' => 2,
                'items_count' => 8,
                'is_active' => true,
                'settings' => [
                    'layout' => 'grid',
                    'columns' => 4,
                    'show_more_button' => true,
                ],
            ],
            [
                'name' => 'الأكثر قراءة',
                'slug' => 'trending-articles',
                'type' => 'trending',
                'title' => 'الأكثر قراءة',
                'subtitle' => null,
                'category_id' => null,
                'order' => 3,
                'items_count' => 6,
                'is_active' => true,
                'settings' => [
                    'show_tabs' => true,
                    'default_tab' => 'month',
                    'show_numbers' => true,
                ],
            ],
            [
                'name' => 'الفيديوهات',
                'slug' => 'trending-videos',
                'type' => 'videos',
                'title' => 'الفيديوهات',
                'subtitle' => null,
                'category_id' => null,
                'order' => 4,
                'items_count' => 6,
                'is_active' => true,
                'settings' => [
                    'layout' => 'grid',
                    'show_duration' => true,
                ],
            ],
            [
                'name' => 'مقالات الرأي',
                'slug' => 'opinions-section',
                'type' => 'opinions',
                'title' => 'مقالات الرأي',
                'subtitle' => null,
                'category_id' => null,
                'order' => 5,
                'items_count' => 6,
                'is_active' => true,
                'settings' => [
                    'layout' => 'grid',
                    'columns' => 3,
                    'split_with_without_images' => true,
                    'show_more_button' => true,
                ],
            ],
        ];

        foreach ($sections as $section) {
            HomepageSection::updateOrCreate(
                ['slug' => $section['slug']],
                $section
            );
        }
    }
}
