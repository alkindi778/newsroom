<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SmartSummaryController extends Controller
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    /**
     * استرجاع ملخص بواسطة hash
     */
    public function getSummary(string $hash): JsonResponse
    {
        try {
            $summary = $this->cacheService->getSummary($hash);
            
            if ($summary) {
                return response()->json([
                    'success' => true,
                    'summary' => $summary
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على الملخص'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('خطأ في استرجاع الملخص: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم'
            ], 500);
        }
    }

    /**
     * توليد وحفظ ملخص جديد (من Frontend)
     */
    public function generateAndStore(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|min:100',
                'type' => 'required|in:news,opinion,analysis',
                'length' => 'required|in:short,medium,long'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 400);
            }

            // توليد hash للمحتوى (متطابق مع Frontend)
            $content = $request->input('content');
            $type = $request->input('type');
            $length = $request->input('length');
            
            // تنظيف المحتوى مثل Frontend
            $normalizedContent = trim(strtolower(preg_replace('/\s+/', ' ', $content)));
            $key = $normalizedContent . '-' . $type . '-' . $length;
            $contentHash = hash('sha256', $key);
            
            // التحقق من وجود ملخص محفوظ
            $existingSummary = $this->cacheService->getSummary($contentHash);
            if ($existingSummary) {
                return response()->json([
                    'success' => true,
                    'summary' => is_array($existingSummary) ? $existingSummary['summary'] : $existingSummary->summary,
                    'cached' => true
                ]);
            }

            // توليد الملخص (مؤقت - بسيط)
            
            $summary = $this->generateSimpleSummary($content, $type, $length);
            
            // حفظ الملخص
            $summaryData = [
                'content_hash' => $contentHash,
                'original_content_sample' => mb_substr(strip_tags($content), 0, 500, 'UTF-8'),
                'summary' => $summary,
                'type' => $type,
                'length' => $length,
                'word_count' => str_word_count($summary),
                'original_length' => strlen($content)
            ];
            
            $savedSummary = $this->cacheService->storeSummary($summaryData);

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'cached' => false
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في توليد الملخص: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في توليد الملخص'
            ], 500);
        }
    }

    /**
     * توليد ملخص بسيط مؤقت
     */
    private function generateSimpleSummary(string $content, string $type, string $length): string
    {
        // تنظيف المحتوى من HTML والصور
        $cleanContent = strip_tags($content);
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = trim($cleanContent);
        
        // تقسيم إلى جمل
        $sentences = preg_split('/[.!?]+/', $cleanContent);
        $sentences = array_filter(array_map('trim', $sentences));
        
        // تحديد عدد الجمل حسب الطول
        $maxSentences = $length === 'short' ? 2 : ($length === 'medium' ? 3 : 4);
        
        // أخذ أول الجمل
        $selectedSentences = array_slice($sentences, 0, $maxSentences);
        
        $summary = implode('. ', $selectedSentences) . '.';
        
        // تحديد الطول حسب النوع
        if ($type === 'opinion') {
            $summary = 'يرى الكاتب أن ' . $summary;
        }
        
        return $summary;
    }

    /**
     * حفظ ملخص جديد
     */
    public function storeSummary(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'content_hash' => 'required|string|max:64',
                'original_content_sample' => 'required|string|max:1000',
                'summary' => 'required|string',
                'type' => 'required|in:news,opinion,analysis',
                'length' => 'required|in:short,medium,long',
                'word_count' => 'nullable|integer|min:0',
                'compression_ratio' => 'nullable|integer|min:0|max:100',
                'original_length' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 400);
            }

            $summary = $this->cacheService->storeSummary($request->all());

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الملخص بنجاح',
                'summary' => [
                    'id' => $summary->id,
                    'content_hash' => $summary->content_hash,
                    'usage_count' => $summary->usage_count,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في حفظ الملخص: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حفظ الملخص'
            ], 500);
        }
    }

    /**
     * إحصائيات الملخصات
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->cacheService->getStatistics();
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في استرجاع الإحصائيات: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في استرجاع الإحصائيات'
            ], 500);
        }
    }

    /**
     * تنظيف الملخصات القديمة
     */
    public function cleanup(): JsonResponse
    {
        try {
            $result = $this->cacheService->cleanup();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تنظيف الملخصات القديمة بنجاح',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في تنظيف الملخصات: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تنظيف الملخصات'
            ], 500);
        }
    }

    /**
     * قائمة الملخصات الحديثة
     */
    public function getRecent(): JsonResponse
    {
        try {
            $summaries = $this->cacheService->getRecentSummaries();

            return response()->json([
                'success' => true,
                'summaries' => $summaries
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في استرجاع الملخصات الحديثة: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في استرجاع البيانات'
            ], 500);
        }
    }
}
