<?php

namespace App\Observers;

use App\Models\ContactMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ContactMessageObserver
{
    /**
     * Handle the ContactMessage "created" event.
     */
    public function created(ContactMessage $contactMessage): void
    {
        try {
            // إشعار المستخدمين الذين لديهم دور إدارة الرسائل
            $users = User::role(['Super Admin', 'Admin', 'سكرتير'])->get();
            
            foreach ($users as $user) {
                Notification::create([
                    'type' => 'contact_message_new',
                    'user_id' => $user->id,
                    'title' => 'رسالة تواصل جديدة',
                    'message' => "رسالة من: {$contactMessage->full_name}",
                    'icon' => 'mail',
                    'link' => "/admin/contact-messages/{$contactMessage->id}",
                    'data' => [
                        'contact_message_id' => $contactMessage->id,
                        'subject' => $contactMessage->subject,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification for new contact message: ' . $e->getMessage());
        }
    }

    /**
     * Handle the ContactMessage "updated" event.
     */
    public function updated(ContactMessage $contactMessage): void
    {
        try {
            // إشعار عند التحويل
            if ($contactMessage->isDirty('assigned_to') && $contactMessage->assigned_to) {
                Notification::create([
                    'type' => 'contact_message_forwarded',
                    'user_id' => $contactMessage->assigned_to,
                    'title' => 'رسالة محولة إليك',
                    'message' => "رسالة من: {$contactMessage->full_name}",
                    'icon' => 'arrow-right',
                    'link' => "/admin/contact-messages/{$contactMessage->id}",
                    'data' => [
                        'contact_message_id' => $contactMessage->id,
                        'subject' => $contactMessage->subject,
                    ],
                ]);
            }

            // إشعار عند الموافقة/الرفض
            if ($contactMessage->isDirty('approval_status')) {
                $admins = User::role(['Super Admin', 'Admin', 'سكرتير'])->get();
                
                $title = $contactMessage->approval_status === 'approved' ? 'تمت الموافقة على الرسالة' : 'تم رفض الرسالة';
                $icon = $contactMessage->approval_status === 'approved' ? 'check-circle' : 'x-circle';
                $type = $contactMessage->approval_status === 'approved' ? 'contact_message_approved' : 'contact_message_rejected';
                
                foreach ($admins as $admin) {
                    Notification::create([
                        'type' => $type,
                        'user_id' => $admin->id,
                        'title' => $title,
                        'message' => "رسالة: {$contactMessage->subject}",
                        'icon' => $icon,
                        'link' => "/admin/contact-messages/{$contactMessage->id}",
                        'data' => [
                            'contact_message_id' => $contactMessage->id,
                        ],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification for updated contact message: ' . $e->getMessage());
        }
    }

    /**
     * Handle the ContactMessage "deleted" event.
     */
    public function deleted(ContactMessage $contactMessage): void
    {
        //
    }

    /**
     * Handle the ContactMessage "restored" event.
     */
    public function restored(ContactMessage $contactMessage): void
    {
        //
    }

    /**
     * Handle the ContactMessage "force deleted" event.
     */
    public function forceDeleted(ContactMessage $contactMessage): void
    {
        //
    }
}
