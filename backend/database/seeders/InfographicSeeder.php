<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InfographicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $infographics = [
            [
                'title' => 'إنفوجرافيك الانتقلي السياسي 2024',
                'title_en' => 'Political Transitional Infographic 2024',
                'description' => 'نظرة شاملة على المشهد السياسي خلال الفترة الانتقالية',
                'description_en' => 'Comprehensive overview of the political landscape during the transitional period',
                'image' => 'infographics/sample1.jpg',
                'slug' => 'political-transitional-2024',
                'is_featured' => true,
                'is_active' => true,
                'order' => 1,
                'tags' => ['سياسة', 'انتقالي', 'إحصائيات'],
            ],
            [
                'title' => 'الاقتصاد الوطني في أرقام',
                'title_en' => 'National Economy in Numbers',
                'description' => 'مؤشرات اقتصادية رئيسية للعام الجاري',
                'description_en' => 'Key economic indicators for the current year',
                'image' => 'infographics/sample2.jpg',
                'slug' => 'national-economy-numbers',
                'is_featured' => true,
                'is_active' => true,
                'order' => 2,
                'tags' => ['اقتصاد', 'إحصائيات'],
            ],
            [
                'title' => 'إنجازات الفترة الانتقالية',
                'title_en' => 'Transitional Period Achievements',
                'description' => 'أهم الإنجازات المحققة خلال الفترة الانتقالية',
                'description_en' => 'Major achievements during the transitional period',
                'image' => 'infographics/sample3.jpg',
                'slug' => 'transitional-achievements',
                'is_featured' => false,
                'is_active' => true,
                'order' => 3,
                'tags' => ['إنجازات', 'تطوير'],
            ],
            [
                'title' => 'التعليم والصحة: البنية التحتية',
                'title_en' => 'Education and Health: Infrastructure',
                'description' => 'نظرة على البنية التحتية للتعليم والصحة',
                'description_en' => 'Overview of education and health infrastructure',
                'image' => 'infographics/sample4.jpg',
                'slug' => 'education-health-infrastructure',
                'is_featured' => false,
                'is_active' => true,
                'order' => 4,
                'tags' => ['تعليم', 'صحة', 'بنية تحتية'],
            ],
        ];

        foreach ($infographics as $infographic) {
            \App\Models\Infographic::create($infographic);
        }
    }
}
