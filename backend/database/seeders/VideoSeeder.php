<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\User;
use Illuminate\Support\Str;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first(); // Get first admin user

        if (!$admin) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        $videos = [
            [
                'title' => 'العربية توثق عودة الفلسطينيين إلى شمال غزة عبر شارع الرشيد',
                'slug' => Str::slug('العربية توثق عودة الفلسطينيين إلى شمال غزة عبر شارع الرشيد'),
                'description' => 'تغطية خاصة من العربية لعودة الفلسطينيين إلى منازلهم في شمال قطاع غزة عبر شارع الرشيد',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'video_type' => 'youtube',
                'video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => null,
                'duration' => '01:22',
                'views' => 15420,
                'likes' => 542,
                'shares' => 123,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(1),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'خريطة جديدة للواقع الميداني في غزة بعد وقف النار',
                'slug' => Str::slug('خريطة جديدة للواقع الميداني في غزة بعد وقف النار'),
                'description' => 'تحليل شامل للوضع الميداني في قطاع غزة مع خريطة تفصيلية للمناطق',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'video_type' => 'youtube',
                'video_id' => '9bZkp7q19f0',
                'thumbnail' => null,
                'duration' => '03:58',
                'views' => 23150,
                'likes' => 876,
                'shares' => 234,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(2),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'إذاعة الشبابيك تفتح صفحة جديدة بين سوريا ولبنان',
                'slug' => Str::slug('إذاعة الشبابيك تفتح صفحة جديدة بين سوريا ولبنان'),
                'description' => 'تقرير خاص عن العلاقات السورية اللبنانية والتطورات الأخيرة',
                'video_url' => 'https://www.youtube.com/watch?v=K8E_zMLCRNg',
                'video_type' => 'youtube',
                'video_id' => 'K8E_zMLCRNg',
                'thumbnail' => null,
                'duration' => '02:47',
                'views' => 18760,
                'likes' => 654,
                'shares' => 187,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(3),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'خريطة جديدة تظهر خطوط تماس الجيش الإسرائيلي بعد وقف إطلاق النار',
                'slug' => Str::slug('خريطة جديدة تظهر خطوط تماس الجيش الإسرائيلي بعد وقف إطلاق النار'),
                'description' => 'تحليل عسكري للوضع الميداني مع خريطة توضيحية لخطوط التماس',
                'video_url' => 'https://www.youtube.com/watch?v=YykjpeuMNEk',
                'video_type' => 'youtube',
                'video_id' => 'YykjpeuMNEk',
                'thumbnail' => null,
                'duration' => '02:24',
                'views' => 31200,
                'likes' => 1123,
                'shares' => 345,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()->subDays(4),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'تقرير خاص: الأوضاع الإنسانية في المنطقة',
                'slug' => Str::slug('تقرير خاص: الأوضاع الإنسانية في المنطقة'),
                'description' => 'تقرير شامل عن الأوضاع الإنسانية في المنطقة',
                'video_url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                'video_type' => 'youtube',
                'video_id' => 'jNQXAC9IVRw',
                'thumbnail' => null,
                'duration' => '04:15',
                'views' => 12450,
                'likes' => 423,
                'shares' => 98,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now()->subDays(5),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'مقابلة حصرية مع محلل سياسي',
                'slug' => Str::slug('مقابلة حصرية مع محلل سياسي'),
                'description' => 'حوار معمق حول التطورات السياسية في المنطقة',
                'video_url' => 'https://www.youtube.com/watch?v=3tmd-ClpJxA',
                'video_type' => 'youtube',
                'video_id' => '3tmd-ClpJxA',
                'thumbnail' => null,
                'duration' => '15:32',
                'views' => 8934,
                'likes' => 312,
                'shares' => 67,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now()->subDays(6),
                'user_id' => $admin->id,
            ],
        ];

        foreach ($videos as $videoData) {
            Video::create($videoData);
        }

        $this->command->info('Videos seeded successfully!');
    }
}
