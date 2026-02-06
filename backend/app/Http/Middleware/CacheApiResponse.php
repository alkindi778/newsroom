<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Response Caching Middleware
 * تخزين استجابات API في الذاكرة المؤقتة لتحسين الأداء
 */
class CacheApiResponse
{
    /**
     * القيم الافتراضية لفترات التخزين المؤقت (بالثواني)
     */
    protected array $defaultTtl = [
        'articles' => 300,           // 5 دقائق
        'categories' => 3600,        // ساعة
        'settings' => 3600,          // ساعة
        'homepage-sections' => 600,  // 10 دقائق
        'writers' => 1800,           // 30 دقيقة
        'opinions' => 600,           // 10 دقائق
        'videos' => 600,             // 10 دقائق
        'infographics' => 600,       // 10 دقائق
        'breaking-news' => 60,       // دقيقة واحدة
        'advertisements' => 300,     // 5 دقائق
        'default' => 300,            // 5 دقائق
    ];

    /**
     * Routes التي لا يجب تخزينها مؤقتاً
     */
    protected array $excludedRoutes = [
        'user',
        'push',
        'contact-messages',
        'smart-summary',
        'search',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?int $ttl = null): Response
    {
        // لا تخزن مؤقتاً إذا كان الطلب ليس GET
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // لا تخزن مؤقتاً إذا كان المستخدم مسجل الدخول
        if ($request->user()) {
            return $next($request);
        }

        // لا تخزن مؤقتاً إذا كان Route مستثنى
        if ($this->isExcludedRoute($request)) {
            return $next($request);
        }

        // توليد مفتاح فريد للـ Cache
        $cacheKey = $this->generateCacheKey($request);
        
        // فترة التخزين المؤقت
        $cacheTtl = $ttl ?? $this->getTtlForRoute($request);

        // التحقق من وجود استجابة مخزنة
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);
            
            return response()
                ->json($cachedResponse['data'], $cachedResponse['status'])
                ->withHeaders([
                    'X-Cache' => 'HIT',
                    'X-Cache-Key' => $cacheKey,
                    'X-Cache-TTL' => $cacheTtl,
                ]);
        }

        // تنفيذ الطلب
        $response = $next($request);

        // تخزين الاستجابة الناجحة فقط
        if ($response->isSuccessful() && $response->headers->get('Content-Type') === 'application/json') {
            $responseData = [
                'data' => json_decode($response->getContent(), true),
                'status' => $response->getStatusCode(),
            ];
            
            Cache::put($cacheKey, $responseData, $cacheTtl);
            
            $response->headers->set('X-Cache', 'MISS');
            $response->headers->set('X-Cache-Key', $cacheKey);
            $response->headers->set('X-Cache-TTL', $cacheTtl);
        }

        return $response;
    }

    /**
     * التحقق إذا كان Route مستثنى
     */
    protected function isExcludedRoute(Request $request): bool
    {
        $path = $request->path();
        
        foreach ($this->excludedRoutes as $excluded) {
            if (str_contains($path, $excluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * توليد مفتاح Cache فريد
     */
    protected function generateCacheKey(Request $request): string
    {
        $path = $request->path();
        $query = $request->query();
        ksort($query);
        
        $locale = app()->getLocale();
        
        return 'api_cache:' . $locale . ':' . md5($path . serialize($query));
    }

    /**
     * الحصول على فترة التخزين المؤقت للـ Route
     */
    protected function getTtlForRoute(Request $request): int
    {
        $path = $request->path();
        
        foreach ($this->defaultTtl as $route => $ttl) {
            if (str_contains($path, $route)) {
                return $ttl;
            }
        }

        return $this->defaultTtl['default'];
    }

    /**
     * مسح Cache لـ Route معين
     */
    public static function clearCache(string $pattern = '*'): int
    {
        $cleared = 0;
        
        // استخدام Redis إذا كان متاحاً
        if (config('cache.default') === 'redis') {
            $keys = Cache::getRedis()->keys('api_cache:' . $pattern);
            foreach ($keys as $key) {
                Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
                $cleared++;
            }
        } else {
            // للـ Database/File cache، نستخدم tags إذا كانت مدعومة
            Cache::flush(); // سيمسح كل الـ Cache
            $cleared = -1; // للإشارة أنه تم مسح الكل
        }

        return $cleared;
    }
}
