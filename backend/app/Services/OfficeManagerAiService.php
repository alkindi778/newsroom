<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OfficeManagerAiService
{
    private ?string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.office_manager_key') ?? env('OFFICE_MANAGER_GEMINI_API_KEY');
        $this->model = config('services.gemini.office_manager_model') ?? env('OFFICE_MANAGER_GEMINI_MODEL', 'gemini-2.5-pro');
    }

    private function validateApiKey(): void
    {
        if (!$this->apiKey) {
            throw new Exception('Office Manager Gemini API key not configured.');
        }
    }

    /**
     * توليد نص باستخدام Gemini
     */
    public function generateContent(string $prompt): string
    {
        $this->validateApiKey();

        try {
            // Build request body
            $requestBody = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ];
            
            // إضافة thinkingConfig لموديلات 2.5-pro
            if (str_contains($this->model, '2.5-pro') || str_contains($this->model, '2.5')) {
                $requestBody['generationConfig'] = [
                    'thinkingConfig' => [
                        'thinkingBudget' => -1 // Unlimited thinking
                    ]
                ];
            }

            $response = Http::timeout(60) // زيادة timeout للـ thinking models
                ->withHeader('x-goog-api-key', $this->apiKey)
                ->post("{$this->baseUrl}/{$this->model}:generateContent", $requestBody);

            if (!$response->successful()) {
                Log::error('Gemini Office Manager API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception("Gemini API Error: {$response->status()}");
            }

            $data = $response->json();
            
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return '';
            }

            return $data['candidates'][0]['content']['parts'][0]['text'];

        } catch (Exception $e) {
            Log::error('Gemini Office Manager Generation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * تحليل رسالة تواصل (تلخيص، مشاعر، تصنيف، واقتراح رد)
     */
    public function analyzeContactMessage(string $message, string $senderName, string $subject): array
    {
        $prompt = "
        أنت مساعد ذكي لإدارة مكتب رسمي حكومي. لديك رسالة واردة:
        
        من: $senderName
        الموضوع: $subject
        الرسالة: \"$message\"

        المطلوب منك إخراج النتيجة بصيغة JSON فقط تحتوي على:
        1. summary: ملخص للرسالة في سطرين.
        2. sentiment: تحليل المشاعر (إما 'positive', 'negative', 'neutral' أو 'urgent').
        3. category: تصنيف الرسالة (إما 'complaint' شكوى، 'inquiry' استفسار، 'meeting_request' طلب لقاء، 'suggestion' اقتراح، 'praise' إشادة، أو 'other' أخرى).
        4. suggested_reply: مسودة رد رسمي ومحترم باسم 'انتقالي العاصمة عدن' يناسب محتوى الرسالة.

        JSON Format:
        {
            \"summary\": \"...\",
            \"sentiment\": \"...\",
            \"category\": \"...\",
            \"suggested_reply\": \"...\"
        }
        ";

        try {
            $resultText = $this->generateContent($prompt);
            
            // تنظيف النص من علامات Markdown code block
            $resultText = preg_replace('/^```json\s*|\s*```$/', '', trim($resultText));
            $resultText = preg_replace('/^```\s*|\s*```$/', '', trim($resultText));
            
            $json = json_decode($resultText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode Gemini JSON', ['text' => $resultText, 'error' => json_last_error_msg()]);
                
                if (preg_match('/\{[\s\S]*\}/', $resultText, $matches)) {
                    $json = json_decode($matches[0], true);
                }
            }
            
            if (!$json) {
                 return [
                    'summary' => 'فشل تحليل الرد من الذكاء الاصطناعي.',
                    'sentiment' => 'neutral',
                    'category' => 'other',
                    'suggested_reply' => 'لم يتمكن النظام من توليد رد مقترح.'
                ];
            }

            // التأكد من وجود category
            if (!isset($json['category'])) {
                $json['category'] = 'other';
            }

            return $json;

        } catch (Exception $e) {
            return [
                'summary' => null,
                'sentiment' => 'neutral',
                'category' => 'other',
                'suggested_reply' => null
            ];
        }
    }
}
