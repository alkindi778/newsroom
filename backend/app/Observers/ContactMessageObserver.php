<?php

namespace App\Observers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;

class ContactMessageObserver
{
    /**
     * Handle the ContactMessage "created" event.
     */
    public function created(ContactMessage $contactMessage): void
    {
        // إشعار المستخدمين الذين لديهم صلاحية إدارة الرسائل
        $users = User::permission('manage_contact_messages')->get();
        
        foreach ($users as $user) {
            $user->notify(new NewContactMessageNotification($contactMessage, 'new'));
        }
    }

    /**
     * Handle the ContactMessage "updated" event.
     */
    public function updated(ContactMessage $contactMessage): void
    {
        // إشعار عند التحويل
        if ($contactMessage->isDirty('assigned_to') && $contactMessage->assigned_to) {
            $assignedUser = User::find($contactMessage->assigned_to);
            if ($assignedUser) {
                $assignedUser->notify(new NewContactMessageNotification($contactMessage, 'forwarded'));
            }
        }

        // إشعار عند الموافقة
        if ($contactMessage->isDirty('approval_status')) {
            $originalAssignedTo = $contactMessage->getOriginal('assigned_to');
            $creator = User::permission('manage_contact_messages')->first();
            
            if ($contactMessage->approval_status === 'approved' && $creator) {
                $creator->notify(new NewContactMessageNotification($contactMessage, 'approved'));
            } elseif ($contactMessage->approval_status === 'rejected' && $creator) {
                $creator->notify(new NewContactMessageNotification($contactMessage, 'rejected'));
            }
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
