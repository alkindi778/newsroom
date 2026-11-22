<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * حماية وتشفير الـ API بالكامل
 * يمنع فتح روابط الـ API مباشرة من المتصفح أو الأدوات الخارجية ويعرض صفحة 404 الموحدة،
 * كما يقوم بتشفير جميع استجابات الـ API بحيث لا تظهر البيانات الخام أبداً.
 */
class ProtectAndEncryptApi
{
    public const CLIENT_HEADER = 'X-Aden-Client-Token';
    public const CLIENT_TOKEN = 'aden-nr-v1-9f8e7d6c5b4a3f2e1d0c9b8a7f6e5d4c';
    private const CRYPTO_SECRET = 'aden-newsroom-enc-key-2026-x9a8b7c6d5e4f3';

    /**
     * التحقق من صلاحية طلب الـ API
     */
    public static function isAuthorizedRequest(Request $request): bool
    {
        // منع التصفح المباشر عبر شريط عنوان المتصفح بشكل قاطع
        $secFetchMode = strtolower((string) $request->header('Sec-Fetch-Mode', ''));
        $secFetchDest = strtolower((string) $request->header('Sec-Fetch-Dest', ''));
        if ($secFetchMode === 'navigate' || $secFetchDest === 'document') {
            return false;
        }

        $token = (string) $request->header(self::CLIENT_HEADER, '');
        return hash_equals(self::CLIENT_TOKEN, $token);
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // السماح بطلبات Preflight الخاصة بـ CORS
        if ($request->isMethod('OPTIONS')) {
            return $next($request);
        }

        // في حال فتح الرابط مباشرة أو بدون التوكن الخاص بالفرونت إند -> إرجاع صفحة 404 الموحدة
        if (!self::isAuthorizedRequest($request)) {
            return response()->view('errors.404', [], 404);
        }

        $response = $next($request);

        // تشفير محتوى الاستجابة إذا كانت JSON
        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($response instanceof JsonResponse || str_contains(strtolower($contentType), 'application/json')) {
            $rawContent = $response->getContent();
            if (is_string($rawContent) && $rawContent !== '' && !str_contains($rawContent, '"_enc":1')) {
                $encrypted = self::encryptPayload($rawContent);
                $wrapped = json_encode([
                    '_enc' => 1,
                    'd' => $encrypted,
                ], JSON_UNESCAPED_SLASHES);

                if ($response instanceof JsonResponse) {
                    $response->setJson($wrapped);
                } else {
                    $response->setContent($wrapped);
                    $response->headers->set('Content-Type', 'application/json');
                }
            }
        }

        return $response;
    }

    /**
     * تشفير البيانات باستخدام خوارزمية متماثلة سريعة مع IV عشوائي لكل طلب
     */
    public static function encryptPayload(string $plainText): string
    {
        $iv = bin2hex(random_bytes(16));
        $key = self::CRYPTO_SECRET . ':' . $iv;
        $keyLen = strlen($key);

        // Key-Scheduling Algorithm (KSA)
        $s = range(0, 255);
        $j = 0;
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % $keyLen])) & 0xFF;
            $tmp = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $tmp;
        }

        // Pseudo-Random Generation Algorithm (RC4-drop256)
        $i = 0;
        $j = 0;
        for ($k = 0; $k < 256; $k++) {
            $i = ($i + 1) & 0xFF;
            $j = ($j + $s[$i]) & 0xFF;
            $tmp = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $tmp;
        }

        $len = strlen($plainText);
        $out = $plainText;
        for ($k = 0; $k < $len; $k++) {
            $i = ($i + 1) & 0xFF;
            $j = ($j + $s[$i]) & 0xFF;
            $tmp = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $tmp;
            $t = ($s[$i] + $s[$j]) & 0xFF;
            $out[$k] = chr(ord($plainText[$k]) ^ $s[$t]);
        }

        return $iv . '.' . base64_encode($out);
    }
}
