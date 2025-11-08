<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على أول مستخدم
        $user = User::first();
        
        if (!$user) {
            $this->command->warn('لا يوجد مستخدمين في النظام');
            return;
        }

        $notifications = [
            [
                'type' => Notification::TYPE_ARTICLE_PENDING,
                'user_id' => $user->id,
                'sender_id' => null,
                'title' => 'مقال بانتظار الموافقة',
                'message' => 'يوجد مقال جديد بانتظار موافقتك للنشر',
                'icon' => 'clock',
                'link' => '/admin/articles/pending',
                'is_read' => false,
                'created_at' => now()->subMinutes(5),
            ],
            [
                'type' => Notification::TYPE_ARTICLE_CREATED,
                'user_id' => $user->id,
                'sender_id' => null,
                'title' => 'خبر جديد',
                'message' => 'تم إضافة خبر جديد إلى النظام',
                'icon' => 'newspaper',
                'link' => '/admin/articles',
                'is_read' => false,
                'created_at' => now()->subMinutes(15),
            ],
            [
                'type' => Notification::TYPE_OPINION_CREATED,
                'user_id' => $user->id,
                'sender_id' => null,
                'title' => 'مقال رأي جديد',
                'message' => 'تم نشر مقال رأي جديد',
                'icon' => 'edit',
                'link' => '/admin/opinions',
                'is_read' => true,
                'read_at' => now()->subMinutes(30),
                'created_at' => now()->subHour(),
            ],
            [
                'type' => Notification::TYPE_SYSTEM,
                'user_id' => $user->id,
                'sender_id' => null,
                'title' => 'مرحباً بك في نظام الإشعارات',
                'message' => 'نظام الإشعارات يعمل الآن بشكل كامل!',
                'icon' => 'bell',
                'link' => null,
                'is_read' => false,
                'created_at' => now(),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

        $this->command->info('تم إنشاء ' . count($notifications) . ' إشعار تجريبي');
    }
}
