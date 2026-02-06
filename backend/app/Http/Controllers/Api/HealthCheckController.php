<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * Health Check Controller
 * فحص صحة النظام والخدمات
 */
class HealthCheckController extends Controller
{
    /**
     * فحص صحة سريع (للـ Load Balancer)
     */
    public function ping(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * فحص صحة شامل
     */
    public function health(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];

        $allHealthy = collect($checks)->every(fn($check) => $check['status'] === 'healthy');
        
        $status = $allHealthy ? 'healthy' : 'unhealthy';
        $httpCode = $allHealthy ? 200 : 503;

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
            'version' => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
        ], $httpCode);
    }

    /**
     * فحص صحة قاعدة البيانات
     */
    protected function checkDatabase(): array
    {
        try {
            $startTime = microtime(true);
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'connection' => config('database.default'),
            ];
        } catch (\Exception $e) {
            Log::error('Health Check - Database failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 'unhealthy',
                'error' => 'Database connection failed',
                'message' => app()->environment('local') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * فحص صحة الـ Cache
     */
    protected function checkCache(): array
    {
        try {
            $startTime = microtime(true);
            $testKey = 'health_check_' . uniqid();
            $testValue = 'test_' . time();
            
            Cache::put($testKey, $testValue, 10);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($retrieved !== $testValue) {
                throw new \Exception('Cache read/write mismatch');
            }

            return [
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            Log::error('Health Check - Cache failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 'unhealthy',
                'error' => 'Cache operation failed',
                'message' => app()->environment('local') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * فحص صحة التخزين
     */
    protected function checkStorage(): array
    {
        try {
            $startTime = microtime(true);
            $testFile = 'health_check_' . uniqid() . '.txt';
            $testContent = 'Health check at ' . now()->toIso8601String();
            
            Storage::put($testFile, $testContent);
            $retrieved = Storage::get($testFile);
            Storage::delete($testFile);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($retrieved !== $testContent) {
                throw new \Exception('Storage read/write mismatch');
            }

            return [
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'disk' => config('filesystems.default'),
            ];
        } catch (\Exception $e) {
            Log::error('Health Check - Storage failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 'unhealthy',
                'error' => 'Storage operation failed',
                'message' => app()->environment('local') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * فحص صحة الـ Queue
     */
    protected function checkQueue(): array
    {
        try {
            $driver = config('queue.default');
            
            // للـ Database queue، نتحقق من وجود الجدول
            if ($driver === 'database') {
                $startTime = microtime(true);
                $pendingJobs = DB::table('jobs')->count();
                $failedJobs = DB::table('failed_jobs')->count();
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);

                return [
                    'status' => 'healthy',
                    'response_time_ms' => $responseTime,
                    'driver' => $driver,
                    'pending_jobs' => $pendingJobs,
                    'failed_jobs' => $failedJobs,
                ];
            }

            return [
                'status' => 'healthy',
                'driver' => $driver,
            ];
        } catch (\Exception $e) {
            Log::error('Health Check - Queue failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 'unhealthy',
                'error' => 'Queue check failed',
                'message' => app()->environment('local') ? $e->getMessage() : null,
            ];
        }
    }

    /**
     * فحص معلومات النظام
     */
    public function info(): JsonResponse
    {
        // هذا الـ endpoint يجب أن يكون محمياً
        return response()->json([
            'app' => [
                'name' => config('app.name'),
                'version' => config('app.version', '1.0.0'),
                'environment' => app()->environment(),
                'debug' => config('app.debug'),
                'locale' => app()->getLocale(),
                'timezone' => config('app.timezone'),
            ],
            'php' => [
                'version' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ],
            'server' => [
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'time' => now()->toIso8601String(),
                'uptime' => $this->getUptime(),
            ],
            'database' => [
                'driver' => config('database.default'),
                'version' => $this->getDatabaseVersion(),
            ],
            'cache' => [
                'driver' => config('cache.default'),
            ],
            'queue' => [
                'driver' => config('queue.default'),
            ],
        ]);
    }

    /**
     * الحصول على وقت التشغيل
     */
    protected function getUptime(): ?string
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                return null;
            }
            
            $uptime = shell_exec('uptime -p');
            return trim($uptime);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * الحصول على إصدار قاعدة البيانات
     */
    protected function getDatabaseVersion(): ?string
    {
        try {
            $version = DB::select('SELECT VERSION() as version');
            return $version[0]->version ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * إحصائيات النظام
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'articles' => [
                    'total' => DB::table('articles')->count(),
                    'published' => DB::table('articles')->where('status', 'published')->count(),
                ],
                'categories' => [
                    'total' => DB::table('categories')->count(),
                ],
                'users' => [
                    'total' => DB::table('users')->count(),
                ],
                'videos' => [
                    'total' => DB::table('videos')->count(),
                ],
                'contact_messages' => [
                    'total' => DB::table('contact_messages')->count(),
                    'unread' => DB::table('contact_messages')->where('status', 'new')->count(),
                ],
            ];

            return response()->json([
                'status' => 'ok',
                'timestamp' => now()->toIso8601String(),
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch stats',
            ], 500);
        }
    }
}
