<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    protected ContactMessage $message;
    protected string $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContactMessage $message, string $type = 'new')
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $titles = [
            'new' => 'رسالة جديدة',
            'forwarded' => 'رسالة محولة إليك',
            'approved' => 'تمت الموافقة على الرسالة',
            'rejected' => 'تم رفض الرسالة',
            'replied' => 'تم الرد على الرسالة',
        ];

        $icons = [
            'new' => 'mail',
            'forwarded' => 'arrow-right',
            'approved' => 'check-circle',
            'rejected' => 'x-circle',
            'replied' => 'reply',
        ];

        return [
            'type' => 'contact_message',
            'title' => $titles[$this->type] ?? 'إشعار جديد',
            'message' => "رسالة من: {$this->message->full_name}",
            'subject' => $this->message->subject,
            'icon' => $icons[$this->type] ?? 'bell',
            'url' => route('admin.contact-messages.show', $this->message->id),
            'contact_message_id' => $this->message->id,
        ];
    }
}
