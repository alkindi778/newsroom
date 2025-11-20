<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * توليد ملخص باستخدام Gemini
     */
    public function generateSummary(string $content, string $type = 'news', string $length = 'medium'): ?string
    {
        try {
            // تحديد عدد الكلمات حسب الطول
            $wordCount = match($length) {
                'short' => 50,
                'medium' => 100,
                'long' => 150,
                default => 100
            };

            // تنظيف المحتوى
            $cleanContent = strip_tags($content);
            $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
            $cleanContent = trim($cleanContent);

            // بناء الـ prompt حسب النوع
            $prompt = $this->buildPrompt($cleanContent, $type, $wordCount);

            // إرسال الطلب لـ Gemini
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 1000,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $summary = $data['candidates'][0]['content']['parts'][0]['text'];
                    return trim($summary);
                }
            }

            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('خطأ في Gemini Service: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * بناء الـ prompt حسب نوع المحتوى
     */
    private function buildPrompt(string $content, string $type, int $wordCount): string
    {
        $typeInstructions = match($type) {
            'opinion' => 'هذا مقال رأي. لخص وجهة نظر الكاتب الرئيسية.',
            'analysis' => 'هذا تحليل إخباري. لخص النقاط التحليلية الأساسية.',
            default => 'هذا خبر. لخص الأحداث والحقائق الرئيسية.'
        };

        return <<<PROMPT
{$typeInstructions}

اكتب ملخصاً باللغة العربية الفصحى في حوالي {$wordCount} كلمة.
الملخص يجب أن يكون:
- واضح ومباشر
- يحافظ على المعلومات الأساسية
- مكتوب بأسلوب احترافي
- بدون عناوين أو ترقيم

المحتوى:
{$content}

الملخص:
PROMPT;
    }

    /**
     * التحقق من صلاحية API Key
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::timeout(10)
                ->get($this->apiUrl . '?key=' . $this->apiKey);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
