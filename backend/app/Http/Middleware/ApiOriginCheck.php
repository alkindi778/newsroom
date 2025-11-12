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
        // في بيئة التطوير، السماح بـ localhost
        // في الإنتاج، السماح فقط بالموقع الرسمي
        $allowedOrigins = config('app.env') === 'local' 
            ? [
                'https://nabdaen.duckdns.org',
                'http://localhost:3000',
                'http://localhost',
                'http://127.0.0.1:3000',
                'http://127.0.0.1',
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

        // إذا كان الطلب من نفس الموقع (same-origin) وبدون Origin header
        if (!$origin && config('app.env') === 'local') {
            $isAllowed = true;
        }

        if (!$isAllowed) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بالوصول إلى هذا الـ API'
            ], 403);
        }

        return $next($request);
    }
}
