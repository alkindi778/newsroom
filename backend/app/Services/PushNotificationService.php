<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ]
        ]);
    }

    /**
     * إرسال إشعار إلى جميع المشتركين النشطين
     */
    public function sendToAll(array $payload)
    {
        $subscriptions = PushSubscription::active()->get();
        
        foreach ($subscriptions as $subscription) {
            $this->send($subscription, $payload);
        }
    }

    /**
     * إرسال إشعار إلى اشتراك محدد
     */
    public function send(PushSubscription $subscription, array $payload)
    {
        try {
            $webPushSubscription = Subscription::create([
                'endpoint' => $subscription->endpoint,
                'publicKey' => $subscription->public_key,
                'authToken' => $subscription->auth_token,
                'contentEncoding' => $subscription->content_encoding,
            ]);

            $result = $this->webPush->sendOneNotification(
                $webPushSubscription,
                json_encode($payload)
            );

            // تحديث آخر استخدام
            $subscription->updateLastUsed();

            // التحقق من نتيجة الإرسال
            if (!$result->isSuccess()) {
                Log::warning('فشل إرسال الإشعار', [
                    'subscription_id' => $subscription->id,
                    'reason' => $result->getReason()
                ]);

                // إذا كان الاشتراك منتهي أو غير صالح، تعطيله
                if ($result->isSubscriptionExpired()) {
                    $subscription->deactivate();
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال الإشعار', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * إرسال إشعار خبر جديد
     */
    public function sendNewArticleNotification($article)
    {
        $payload = [
            'title' => 'خبر جديد',
            'body' => $article->title,
            'icon' => $article->featured_image ?? '/icon-192x192.png',
            'badge' => '/badge-72x72.png',
            'tag' => 'article-' . $article->id,
            'url' => config('app.url') . '/articles/' . $article->slug,
            'data' => [
                'type' => 'article',
                'id' => $article->id,
                'url' => '/articles/' . $article->slug
            ]
        ];

        $this->sendToAll($payload);
    }

    /**
     * إرسال إشعار فيديو جديد
     */
    public function sendNewVideoNotification($video)
    {
        $payload = [
            'title' => 'فيديو جديد',
            'body' => $video->title,
            'icon' => $video->thumbnail ?? '/icon-192x192.png',
            'badge' => '/badge-72x72.png',
            'tag' => 'video-' . $video->id,
            'url' => config('app.url') . '/videos/' . $video->slug,
            'data' => [
                'type' => 'video',
                'id' => $video->id,
                'url' => '/videos/' . $video->slug
            ]
        ];

        $this->sendToAll($payload);
    }

    /**
     * إرسال إشعار رأي جديد
     */
    public function sendNewOpinionNotification($opinion)
    {
        $payload = [
            'title' => 'رأي جديد',
            'body' => $opinion->title,
            'icon' => $opinion->featured_image ?? '/icon-192x192.png',
            'badge' => '/badge-72x72.png',
            'tag' => 'opinion-' . $opinion->id,
            'url' => config('app.url') . '/opinions/' . $opinion->slug,
            'data' => [
                'type' => 'opinion',
                'id' => $opinion->id,
                'url' => '/opinions/' . $opinion->slug
            ]
        ];

        $this->sendToAll($payload);
    }

    /**
     * إرسال إشعار مخصص
     */
    public function sendCustomNotification(string $title, string $body, ?string $url = null, array $options = [])
    {
        $payload = array_merge([
            'title' => $title,
            'body' => $body,
            'icon' => $options['icon'] ?? '/icon-192x192.png',
            'badge' => $options['badge'] ?? '/badge-72x72.png',
            'tag' => $options['tag'] ?? 'custom-' . time(),
            'url' => $url ?? config('app.url'),
            'data' => $options['data'] ?? []
        ], $options);

        $this->sendToAll($payload);
    }

    /**
     * تنظيف الاشتراكات القديمة وغير النشطة
     */
    public function cleanupOldSubscriptions($daysOld = 90)
    {
        PushSubscription::where('last_used_at', '<', now()->subDays($daysOld))
            ->orWhere(function($query) use ($daysOld) {
                $query->whereNull('last_used_at')
                      ->where('created_at', '<', now()->subDays($daysOld));
            })
            ->delete();
    }
}
