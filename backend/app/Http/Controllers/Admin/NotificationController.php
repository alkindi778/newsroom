<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * الحصول على إشعارات المستخدم
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $notifications = $this->notificationService->getUserNotifications(
            Auth::id(),
            $limit
        );

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon ?? $notification->getDefaultIcon(),
                    'color' => $notification->getTypeColor(),
                    'link' => $notification->link,
                    'is_read' => $notification->is_read,
                    'time_ago' => $notification->getTimeAgo(),
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'sender' => $notification->sender ? [
                        'id' => $notification->sender->id,
                        'name' => $notification->sender->name,
                        'avatar' => $notification->sender->avatar,
                    ] : null,
                ];
            }),
            'unread_count' => $this->notificationService->getUnreadCount(Auth::id())
        ]);
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة
     */
    public function unreadCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount(Auth::id());

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead(int $id): JsonResponse
    {
        $success = $this->notificationService->markAsRead($id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'تم تحديد الإشعار كمقروء' : 'فشل تحديد الإشعار'
        ]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead(Auth::id());

        return response()->json([
            'success' => true,
            'message' => "تم تحديد {$count} إشعار كمقروء",
            'count' => $count
        ]);
    }

    /**
     * حذف إشعار
     */
    public function destroy(int $id): JsonResponse
    {
        $success = $this->notificationService->delete($id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'تم حذف الإشعار' : 'فشل حذف الإشعار'
        ]);
    }

    /**
     * حذف جميع الإشعارات
     */
    public function destroyAll(): JsonResponse
    {
        $count = $this->notificationService->deleteAll(Auth::id());

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$count} إشعار",
            'count' => $count
        ]);
    }
}
