<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiOriginCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // السماح فقط للطلبات من الموقع الخاص بنا
        // في التطوير: localhost:3000 (الـ frontend)
        // في الإنتاج: https://nabdaen.duckdns.org
        $allowedOrigins = config('app.env') === 'local' 
            ? [
                'http://localhost:3000',
                'http://127.0.0.1:3000',
            ]
            : [
                'https://nabdaen.duckdns.org',
            ];

        $origin = $request->header('Origin') ?? $request->header('Referer');
        
        // التحقق من الـ Origin
        $isAllowed = false;
        if ($origin) {
            foreach ($allowedOrigins as $allowed) {
                if (strpos($origin, $allowed) === 0) {
                    $isAllowed = true;
                    break;
                }
            }
        }

        // رفض الطلبات بدون Origin header (الوصول المباشر عبر المتصفح)
        if (!$isAllowed) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بالوصول إلى هذا الـ API'
            ], 403);
        }

        return $next($request);
    }
}
