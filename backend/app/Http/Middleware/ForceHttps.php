<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force HTTPS Middleware
 * فرض استخدام HTTPS في الإنتاج
 */
class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // تخطي في بيئة التطوير المحلية
        if (app()->environment('local', 'testing')) {
            return $next($request);
        }

        // فرض HTTPS في الإنتاج
        if (!$request->isSecure() && config('app.force_https')) {
            // إعادة التوجيه إلى HTTPS
            return redirect()->secure($request->getRequestUri(), 301);
        }

        $response = $next($request);

        // إضافة HSTS Header لفرض HTTPS الصارم
        if ($request->isSecure() || config('app.force_https')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }
}
