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
        // الحصول على الصورة من Media Library
        $imageUrl = $article->getFirstMediaUrl('articles');
        
        // إذا لم توجد صورة من Media Library، جرب featured_image
        if (!$imageUrl && $article->featured_image) {
            $imageUrl = $article->featured_image;
        }
        
        // إذا لم توجد، جرب حقل image
        if (!$imageUrl && $article->image) {
            $imageUrl = $article->image;
            // إذا كان المسار يبدأ بـ media/ أضف storage/ قبله
            if (strpos($imageUrl, 'media/') === 0) {
                $imageUrl = 'storage/' . $imageUrl;
            }
        }
        
        // إذا الصورة نسبية (relative path)، اجعلها absolute
        if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $imageUrl = config('app.url') . '/' . ltrim($imageUrl, '/');
        }
        
        // استخدم الأيقونة الافتراضية إذا لم توجد صورة
        if (!$imageUrl) {
            $imageUrl = config('app.url') . '/icon-192x192.png';
        }
        
        // الحصول على اسم الموقع وأيقونته من الإعدادات
        $siteName = \App\Models\SiteSetting::where('key', 'site_name')->value('value');
        $siteIcon = \App\Models\SiteSetting::where('key', 'site_logo')->value('value');
        
        // استخدام أيقونة الموقع أو الأيقونة الافتراضية
        $iconUrl = $siteIcon ? config('app.url') . '/storage/' . ltrim($siteIcon, '/') : config('app.url') . '/icon-192x192.png';
        
        $payload = [
            'title' => ($siteName ? $siteName . ' - ' : '') . 'خبر جديد',
            'body' => $article->title,
            'icon' => $iconUrl,
            'image' => $imageUrl, // إضافة image للصورة الكبيرة
            'badge' => config('app.url') . '/badge-72x72.png',
            'tag' => 'article-' . $article->id,
            'url' => config('app.url') . '/news/' . $article->slug,
            'data' => [
                'type' => 'article',
                'id' => $article->id,
                'url' => '/news/' . $article->slug
            ]
        ];

        $this->sendToAll($payload);
    }

    /**
     * إرسال إشعار فيديو جديد
     */
    public function sendNewVideoNotification($video)
    {
        // الحصول على الصورة
        $imageUrl = $video->getFirstMediaUrl('videos');
        
        if (!$imageUrl && $video->thumbnail) {
            $imageUrl = $video->thumbnail;
        }
        
        if (!$imageUrl && $video->image) {
            $imageUrl = $video->image;
            if (strpos($imageUrl, 'media/') === 0) {
                $imageUrl = 'storage/' . $imageUrl;
            }
        }
        
        if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $imageUrl = config('app.url') . '/' . ltrim($imageUrl, '/');
        }
        
        if (!$imageUrl) {
            $imageUrl = config('app.url') . '/icon-192x192.png';
        }
        
        // الحصول على اسم الموقع وأيقونته من الإعدادات
        $siteName = \App\Models\SiteSetting::where('key', 'site_name')->value('value');
        $siteIcon = \App\Models\SiteSetting::where('key', 'site_logo')->value('value');
        
        // استخدام أيقونة الموقع أو الأيقونة الافتراضية
        $iconUrl = $siteIcon ? config('app.url') . '/storage/' . ltrim($siteIcon, '/') : config('app.url') . '/icon-192x192.png';
        
        $payload = [
            'title' => ($siteName ? $siteName . ' - ' : '') . 'فيديو جديد',
            'body' => $video->title,
            'icon' => $iconUrl,
            'image' => $imageUrl,
            'badge' => config('app.url') . '/badge-72x72.png',
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
        // الحصول على الصورة
        $imageUrl = $opinion->getFirstMediaUrl('opinions');
        
        if (!$imageUrl && $opinion->featured_image) {
            $imageUrl = $opinion->featured_image;
        }
        
        if (!$imageUrl && $opinion->image) {
            $imageUrl = $opinion->image;
            if (strpos($imageUrl, 'media/') === 0) {
                $imageUrl = 'storage/' . $imageUrl;
            }
        }
        
        if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $imageUrl = config('app.url') . '/' . ltrim($imageUrl, '/');
        }
        
        if (!$imageUrl) {
            $imageUrl = config('app.url') . '/icon-192x192.png';
        }
        
        // الحصول على اسم الموقع وأيقونته من الإعدادات
        $siteName = \App\Models\SiteSetting::where('key', 'site_name')->value('value');
        $siteIcon = \App\Models\SiteSetting::where('key', 'site_logo')->value('value');
        
        // استخدام أيقونة الموقع أو الأيقونة الافتراضية
        $iconUrl = $siteIcon ? config('app.url') . '/storage/' . ltrim($siteIcon, '/') : config('app.url') . '/icon-192x192.png';
        
        $payload = [
            'title' => ($siteName ? $siteName . ' - ' : '') . 'رأي جديد',
            'body' => $opinion->title,
            'icon' => $iconUrl,
            'image' => $imageUrl,
            'badge' => config('app.url') . '/badge-72x72.png',
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
     * إرسال إشعار خبر عاجل
     */
    public function sendBreakingNewsNotification($breakingNews)
    {
        // الحصول على اسم الموقع وأيقونته من الإعدادات
        $siteName = \App\Models\SiteSetting::where('key', 'site_name')->value('value') ?? 'عدن الغد';
        $siteIcon = \App\Models\SiteSetting::where('key', 'site_logo')->value('value');
        
        // استخدام أيقونة الموقع أو الأيقونة الافتراضية
        $iconUrl = $siteIcon ? config('app.url') . '/storage/' . ltrim($siteIcon, '/') : config('app.url') . '/icon-192x192.png';
        
        // تحديد الرابط
        $url = $breakingNews->url;
        if (!$url && $breakingNews->article) {
            $url = config('app.url') . '/news/' . $breakingNews->article->slug;
        }
        if (!$url) {
            $url = config('app.url');
        }
        
        $payload = [
            'title' => '🚨 ' . $siteName . ' -  عاجل',
            'body' => $breakingNews->title,
            'icon' => $iconUrl,
            'badge' => config('app.url') . '/badge-72x72.png',
            'tag' => 'breaking-' . $breakingNews->id,
            'url' => $url,
            'requireInteraction' => true, // يبقى الإشعار حتى يتفاعل المستخدم
            'data' => [
                'type' => 'breaking_news',
                'id' => $breakingNews->id,
                'url' => $url
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
