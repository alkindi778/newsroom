<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent';

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
     * توليد بيانات السيو (كلمات دلالية ووصف)
     */
    public function generateSeoData(string $title, string $content): array
    {
        try {
            // تنظيف المحتوى
            $cleanContent = strip_tags($content);
            $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
            $cleanContent = trim($cleanContent);
            
            // تقليص المحتوى إذا كان طويلاً جداً لتوفير التوكنز
            if (mb_strlen($cleanContent) > 10000) {
                $cleanContent = mb_substr($cleanContent, 0, 10000) . '...';
            }

            $prompt = <<<PROMPT
أنت خبير SEO متخصص في المواقع الإخبارية. قم بتحليل العنوان والمحتوى التالي واستخرج:
1. كلمات دلالية (Keywords) قوية وذات صلة (10-15 كلمة مفصولة بفاصلة).
2. وصف ميتا (Meta Description) جذاب ومحفز للنقر (حوالي 150-160 حرف).

العنوان: {$title}
المحتوى: {$cleanContent}

المطلوب إخراج النتيجة بصيغة JSON فقط كالتالي:
{
    "keywords": "كلمة1, كلمة2, كلمة3...",
    "meta_description": "وصف مختصر وجذاب..."
}
PROMPT;

            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 500,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $text = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // تنظيف النص من علامات Markdown JSON إذا وجدت
                    $text = preg_replace('/^```json\s*|\s*```$/', '', trim($text));
                    
                    $json = json_decode($text, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return [
                            'keywords' => $json['keywords'] ?? '',
                            'meta_description' => $json['meta_description'] ?? ''
                        ];
                    }
                }
            }

            Log::error('Gemini SEO Generation Error', ['status' => $response->status(), 'body' => $response->body()]);
            return [];

        } catch (\Exception $e) {
            Log::error('Error in GeminiService::generateSeoData: ' . $e->getMessage());
            return [];
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
