<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\PaginationServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies for HTTPS detection
        $middleware->trustProxies(at: '*');
        
        // Security Headers - يضاف لجميع الطلبات
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        
        // Force HTTPS في الإنتاج
        $middleware->append(\App\Http\Middleware\ForceHttps::class);
        
        // تفعيل CORS وحماية وتشفير الـ API لجميع routes
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\ProtectAndEncryptApi::class,
        ]);
        
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'throttle.custom' => \App\Http\Middleware\RateLimitRequests::class,
            'cache.api' => \App\Http\Middleware\CacheApiResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // معالجة أخطاء 404 لتوحيد صفحة 404 مع الفرونت إند وحماية الـ API من التصفح المباشر
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') && \App\Http\Middleware\ProtectAndEncryptApi::isAuthorizedRequest($request)) {
                $payload = json_encode([
                    'status' => 'error',
                    'message' => 'المورد المطلوب غير موجود',
                ], JSON_UNESCAPED_UNICODE);

                return response()->json([
                    '_enc' => 1,
                    'd' => \App\Http\Middleware\ProtectAndEncryptApi::encryptPayload($payload),
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // معالجة أخطاء الصلاحيات 403
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, \Illuminate\Http\Request $request) {
            \Log::error('UnauthorizedException caught', [
                'url' => $request->url(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'ليس لديك صلاحية للوصول إلى هذا المورد',
                    'error' => 'غير مصرح',
                ], 403);
            }
            
            return response()->view('errors.403', [], 403);
        });

        // معالجة أخطاء الصلاحيات العامة
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            \Log::error('AccessDeniedHttpException caught', [
                'url' => $request->url(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'ليس لديك صلاحية للوصول إلى هذا المورد',
                    'error' => 'غير مصرح',
                ], 403);
            }
            
            return response()->view('errors.403', [], 403);
        });
    })->create();
