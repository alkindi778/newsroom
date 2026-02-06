<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate Limiting Middleware
 * حماية من هجمات Brute Force و DDoS
 */
class RateLimitRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limiterName = 'global'): Response
    {
        $key = $this->resolveRequestSignature($request, $limiterName);
        
        $maxAttempts = $this->getMaxAttempts($limiterName);
        $decayMinutes = $this->getDecayMinutes($limiterName);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            return response()->json([
                'success' => false,
                'message' => 'عدد الطلبات كثير جداً. يرجى المحاولة بعد ' . $retryAfter . ' ثانية.',
                'retry_after' => $retryAfter
            ], 429)->withHeaders([
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }
        
        RateLimiter::hit($key, $decayMinutes * 60);
        
        $response = $next($request);
        
        // إضافة headers للـ Rate Limiting
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));
        
        return $response;
    }
    
    /**
     * توليد مفتاح فريد للطلب
     */
    protected function resolveRequestSignature(Request $request, string $limiterName): string
    {
        $user = $request->user();
        
        if ($user) {
            return $limiterName . '|' . $user->id;
        }
        
        return $limiterName . '|' . $request->ip();
    }
    
    /**
     * الحصول على الحد الأقصى للطلبات
     */
    protected function getMaxAttempts(string $limiterName): int
    {
        return match($limiterName) {
            'login' => 5,           // 5 محاولات تسجيل دخول
            'api' => 60,            // 60 طلب API في الدقيقة
            'contact' => 3,         // 3 رسائل تواصل في الدقيقة
            'upload' => 10,         // 10 رفع ملفات في الدقيقة
            'sensitive' => 10,      // 10 عمليات حساسة في الدقيقة
            default => 100,         // 100 طلب عام في الدقيقة
        };
    }
    
    /**
     * الحصول على فترة إعادة التعيين بالدقائق
     */
    protected function getDecayMinutes(string $limiterName): int
    {
        return match($limiterName) {
            'login' => 15,          // 15 دقيقة للتسجيل
            'contact' => 5,         // 5 دقائق للتواصل
            default => 1,           // دقيقة واحدة للباقي
        };
    }
}
