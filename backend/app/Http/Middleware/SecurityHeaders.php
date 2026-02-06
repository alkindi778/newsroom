<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 * يضيف headers أمان مهمة لجميع الاستجابات
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // منع MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // منع الموقع من التحميل في iframe (حماية من Clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // تفعيل XSS Filter في المتصفح
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // التحكم في معلومات Referrer المرسلة
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // منع تحميل الموارد من مصادر غير موثوقة
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        
        // في بيئة الإنتاج، أضف HSTS
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
