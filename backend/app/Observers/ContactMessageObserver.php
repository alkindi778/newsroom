<?php

namespace App\Observers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Support\Facades\Log;

class ContactMessageObserver
{
    /**
     * Handle the ContactMessage "created" event.
     */
    public function created(ContactMessage $contactMessage): void
    {
        try {
            // إشعار المستخدمين الذين لديهم صلاحية إدارة الرسائل
            $users = User::role(['Super Admin', 'Admin', 'سكرتير'])->get();
            
            foreach ($users as $user) {
                $user->notify(new NewContactMessageNotification($contactMessage, 'new'));
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
                $assignedUser = User::find($contactMessage->assigned_to);
                if ($assignedUser) {
                    $assignedUser->notify(new NewContactMessageNotification($contactMessage, 'forwarded'));
                }
            }

            // إشعار عند الموافقة
            if ($contactMessage->isDirty('approval_status')) {
                $creator = User::role(['Super Admin', 'Admin', 'سكرتير'])->first();
                
                if ($contactMessage->approval_status === 'approved' && $creator) {
                    $creator->notify(new NewContactMessageNotification($contactMessage, 'approved'));
                } elseif ($contactMessage->approval_status === 'rejected' && $creator) {
                    $creator->notify(new NewContactMessageNotification($contactMessage, 'rejected'));
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
