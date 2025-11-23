<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use App\Models\SiteSetting;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $settings = SiteSetting::where('group', 'general')->pluck('value', 'key');
        $siteName = $settings['site_title'] ?? config('app.name');
        
        return (new MailMessage)
            ->subject(Lang::get('إشعار إعادة تعيين كلمة المرور - :app', ['app' => $siteName]))
            ->greeting(Lang::get('مرحباً!'))
            ->line(Lang::get('أنت تتلقى هذا البريد الإلكتروني لأننا تلقينا طلب إعادة تعيين كلمة المرور لحسابك.'))
            ->action(Lang::get('إعادة تعيين كلمة المرور'), url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false)))
            ->line(Lang::get('سينتهي رابط إعادة تعيين كلمة المرور هذا خلال 60 دقيقة.'))
            ->line(Lang::get('إذا لم تطلب إعادة تعيين كلمة المرور، فلا يلزم اتخاذ أي إجراء آخر.'))
            ->salutation(Lang::get('مع تحيات،') . "\n" . $siteName);
    }
}
