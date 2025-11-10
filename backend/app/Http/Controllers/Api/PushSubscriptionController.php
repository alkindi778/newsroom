<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PushSubscriptionController extends Controller
{
    protected PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * الحصول على مفتاح VAPID العام
     */
    public function getPublicKey()
    {
        return response()->json([
            'public_key' => config('services.vapid.public_key')
        ]);
    }

    /**
     * إنشاء اشتراك جديد
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // التحقق من عدم وجود اشتراك مسبق بنفس الـ endpoint
            $subscription = PushSubscription::where('endpoint', $request->endpoint)->first();

            if ($subscription) {
                // تحديث الاشتراك الموجود
                $subscription->update([
                    'public_key' => $request->input('keys.p256dh'),
                    'auth_token' => $request->input('keys.auth'),
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            } else {
                // إنشاء اشتراك جديد
                $subscription = PushSubscription::create([
                    'user_id' => auth()->id(), // إذا كان المستخدم مسجل دخول
                    'endpoint' => $request->endpoint,
                    'public_key' => $request->input('keys.p256dh'),
                    'auth_token' => $request->input('keys.auth'),
                    'content_encoding' => $request->input('content_encoding', 'aesgcm'),
                    'preferences' => $request->input('preferences', []),
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم الاشتراك في الإشعارات بنجاح',
                'subscription' => $subscription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاشتراك في الإشعارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إلغاء الاشتراك
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subscription = PushSubscription::where('endpoint', $request->endpoint)->first();

            if ($subscription) {
                $subscription->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'تم إلغاء الاشتراك في الإشعارات بنجاح'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'الاشتراك غير موجود'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إلغاء الاشتراك',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إرسال إشعار تجريبي
     */
    public function sendTestNotification(Request $request)
    {
        try {
            $this->pushService->sendCustomNotification(
                'إشعار تجريبي',
                'هذا إشعار تجريبي من موقع الأخبار',
                config('app.url'),
                [
                    'tag' => 'test-' . time(),
                    'data' => [
                        'type' => 'test',
                        'url' => '/'
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الإشعار التجريبي'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إرسال الإشعار التجريبي',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث تفضيلات الإشعارات
     */
    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
            'preferences' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subscription = PushSubscription::where('endpoint', $request->endpoint)->first();

            if ($subscription) {
                $subscription->update([
                    'preferences' => $request->preferences
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث التفضيلات بنجاح',
                    'subscription' => $subscription
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'الاشتراك غير موجود'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث التفضيلات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
