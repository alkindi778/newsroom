<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * إنشاء إشعار جديد
     */
    public function create(array $data): ?Notification
    {
        try {
            return Notification::create([
                'type' => $data['type'],
                'user_id' => $data['user_id'],
                'sender_id' => $data['sender_id'] ?? null,
                'title' => $data['title'],
                'message' => $data['message'],
                'icon' => $data['icon'] ?? null,
                'link' => $data['link'] ?? null,
                'data' => $data['data'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return null;
        }
    }

    /**
     * إنشاء إشعار لعدة مستخدمين
     */
    public function createForMultipleUsers(array $userIds, array $data): int
    {
        $count = 0;
        foreach ($userIds as $userId) {
            $notificationData = array_merge($data, ['user_id' => $userId]);
            if ($this->create($notificationData)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * الحصول على إشعارات المستخدم
     */
    public function getUserNotifications(int $userId, int $limit = 10): Collection
    {
        return Notification::forUser($userId)
            ->with('sender:id,name,avatar')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::forUser($userId)
            ->unread()
            ->count();
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead(int $notificationId): bool
    {
        try {
            $notification = Notification::find($notificationId);
            return $notification ? $notification->markAsRead() : false;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * تحديد جميع إشعارات المستخدم كمقروءة
     */
    public function markAllAsRead(int $userId): int
    {
        try {
            return Notification::forUser($userId)
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * حذف إشعار
     */
    public function delete(int $notificationId): bool
    {
        try {
            $notification = Notification::find($notificationId);
            return $notification ? $notification->delete() : false;
        } catch (\Exception $e) {
            Log::error('Failed to delete notification', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * حذف جميع إشعارات المستخدم
     */
    public function deleteAll(int $userId): int
    {
        try {
            return Notification::forUser($userId)->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete all notifications', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * إشعار عند إنشاء مقال جديد
     */
    public function notifyArticleCreated(int $articleId, string $title, int $authorId): void
    {
        // إشعار المحررين والمديرين
        $editors = User::permission(['publish_articles', 'manage_articles'])->pluck('id')->toArray();
        
        $this->createForMultipleUsers($editors, [
            'type' => Notification::TYPE_ARTICLE_CREATED,
            'sender_id' => $authorId,
            'title' => 'خبر جديد',
            'message' => "تم إضافة خبر جديد: {$title}",
            'icon' => 'newspaper',
            'link' => route('admin.articles.show', $articleId),
            'data' => ['article_id' => $articleId]
        ]);
    }

    /**
     * إشعار عند انتظار موافقة المقال
     */
    public function notifyArticlePending(int $articleId, string $title, int $authorId): void
    {
        // إشعار المحررين الذين لهم صلاحية الموافقة
        $approvers = User::permission(['approve_articles', 'publish_articles'])->pluck('id')->toArray();
        
        $this->createForMultipleUsers($approvers, [
            'type' => Notification::TYPE_ARTICLE_PENDING,
            'sender_id' => $authorId,
            'title' => 'مقال بانتظار الموافقة',
            'message' => "مقال بانتظار الموافقة: {$title}",
            'icon' => 'clock',
            'link' => route('admin.articles.pending'),
            'data' => ['article_id' => $articleId]
        ]);
    }

    /**
     * إشعار عند الموافقة على المقال
     */
    public function notifyArticleApproved(int $articleId, string $title, int $authorId, int $approverId): void
    {
        $this->create([
            'type' => Notification::TYPE_ARTICLE_APPROVED,
            'user_id' => $authorId,
            'sender_id' => $approverId,
            'title' => 'تمت الموافقة على مقالك',
            'message' => "تمت الموافقة على مقالك: {$title}",
            'icon' => 'check-circle',
            'link' => route('admin.articles.show', $articleId),
            'data' => ['article_id' => $articleId]
        ]);
    }

    /**
     * إشعار عند رفض المقال
     */
    public function notifyArticleRejected(int $articleId, string $title, int $authorId, int $rejecterId, string $reason): void
    {
        $this->create([
            'type' => Notification::TYPE_ARTICLE_REJECTED,
            'user_id' => $authorId,
            'sender_id' => $rejecterId,
            'title' => 'تم رفض مقالك',
            'message' => "تم رفض مقالك: {$title}. السبب: {$reason}",
            'icon' => 'x-circle',
            'link' => route('admin.articles.edit', $articleId),
            'data' => [
                'article_id' => $articleId,
                'reason' => $reason
            ]
        ]);
    }

    /**
     * إشعار عند إنشاء مقال رأي
     */
    public function notifyOpinionCreated(int $opinionId, string $title, int $writerId): void
    {
        $editors = User::permission(['إدارة مقالات الرأي', 'نشر مقالات الرأي'])->pluck('id')->toArray();
        
        $this->createForMultipleUsers($editors, [
            'type' => Notification::TYPE_OPINION_CREATED,
            'sender_id' => $writerId,
            'title' => 'مقال رأي جديد',
            'message' => "تم إضافة مقال رأي جديد: {$title}",
            'icon' => 'edit',
            'link' => route('admin.opinions.show', $opinionId),
            'data' => ['opinion_id' => $opinionId]
        ]);
    }

    /**
     * إشعار النظام
     */
    public function notifySystem(int $userId, string $title, string $message, ?string $link = null): void
    {
        $this->create([
            'type' => Notification::TYPE_SYSTEM,
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'icon' => 'bell',
            'link' => $link,
        ]);
    }
}
