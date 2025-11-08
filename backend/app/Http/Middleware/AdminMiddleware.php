<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/admin/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // التحقق من وجود صلاحية الوصول للوحة التحكم
        $user = auth()->user();
        $canViewDashboard = $user->can('view_dashboard');

        if (!$canViewDashboard) {
            return redirect('/admin/login')->with('error', 'ليس لديك صلاحية للوصول لهذه الصفحة');
        }

        return $next($request);
    }
}
