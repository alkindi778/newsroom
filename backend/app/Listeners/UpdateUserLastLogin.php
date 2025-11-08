<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class UpdateUserLastLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            $user = $event->user;
            
            if ($user) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip(),
                ]);
                
                Log::info('تم تحديث آخر تسجيل دخول', [
                    'user_id' => $user->id,
                    'ip' => request()->ip(),
                    'time' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('فشل تحديث آخر تسجيل دخول', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
