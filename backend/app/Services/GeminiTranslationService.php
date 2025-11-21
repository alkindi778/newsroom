<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiTranslationService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        $this->model = config('services.gemini.model', 'gemini-flash-latest');
    }

    /**
     * Translate content from Arabic to English using Gemini API
     *
     * @param string $title Arabic title to translate
     * @param string $content Arabic content to translate
     * @return array|null Returns ['title_en' => '...', 'content_en' => '...'] or null on failure
     */
    public function translateContent(string $title, string $content): ?array
    {
        try {
            $prompt = $this->buildTranslationPrompt($title, $content);
            
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => config('translation.prompt.temperature', 0.3),
                        'topK' => config('translation.prompt.top_k', 40),
                        'topP' => config('translation.prompt.top_p', 0.95),
                        'maxOutputTokens' => config('translation.prompt.max_output_tokens', 8192),
                    ],
                    'thinkingConfig' => [
                        'thinkingBudget' => 0,
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }

            $result = $response->json();
            
            // Extract the translated content from Gemini response
            $translatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$translatedText) {
                Log::error('No translation text in Gemini response', ['response' => $result]);
                return null;
            }

            // Parse the JSON response from Gemini
            $translatedData = $this->parseTranslationResponse($translatedText);
            
            if (!$translatedData) {
                Log::error('Failed to parse translation response', ['text' => $translatedText]);
                return null;
            }

            return $translatedData;

        } catch (\Exception $e) {
            Log::error('Translation Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Build the translation prompt for Gemini
     */
    protected function buildTranslationPrompt(string $title, string $content): string
    {
        return <<<PROMPT
You are a professional Arabic-to-English translator with 30+ years of experience specializing in news articles, journalistic content, and media translation.

# Task
Translate the following Arabic news article/content to English with FULL PROFESSIONAL TRANSLATION.

# Critical Translation Requirements
1. **COMPLETE TRANSLATION**: Translate EVERYTHING - DO NOT abbreviate, shorten, summarize, or omit ANY content
2. **PROFESSIONAL QUALITY**: Use high-quality journalistic English with proper grammar and style
3. **PRESERVE MEANING**: Maintain the exact meaning, tone, and nuance of the original Arabic text
4. **FULL LENGTH**: The English translation must be as complete and detailed as the Arabic original
5. Preserve all HTML tags exactly as they appear - DO NOT modify, remove, or translate HTML tags
6. Maintain the exact structure and formatting of the content
7. Translate only the text content, not HTML attributes or tags
8. Use professional journalistic English terminology
9. Ensure cultural context is appropriate for English readers while maintaining authenticity
10. Keep proper nouns in their original form or use standard English transliterations

# Output Format Requirements
1. Return ONLY a valid JSON object with no additional text, markdown, or code blocks
2. The JSON must have exactly two keys: "title_en" and "content_en"
3. Both translations must be COMPLETE and PROFESSIONAL

# Input Data
Title (Arabic): {$title}

Content (Arabic): {$content}

# Expected Output Format
{"title_en":"FULL professional English translation of the complete title","content_en":"FULL professional English translation of the complete content with all HTML preserved and all details translated"}

Return only the JSON object with COMPLETE translations, nothing else.
PROMPT;
    }

    /**
     * Parse the translation response from Gemini
     * Handles both clean JSON and JSON wrapped in markdown code blocks
     */
    protected function parseTranslationResponse(string $response): ?array
    {
        // Remove markdown code blocks if present
        $cleaned = preg_replace('/```json\s*|\s*```/', '', $response);
        $cleaned = trim($cleaned);

        // Try to decode JSON
        $decoded = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode error', [
                'error' => json_last_error_msg(),
                'response' => $cleaned
            ]);
            return null;
        }

        // Validate the response has required fields
        if (!isset($decoded['title_en']) || !isset($decoded['content_en'])) {
            Log::error('Missing required fields in translation', ['decoded' => $decoded]);
            return null;
        }

        return [
            'title_en' => $decoded['title_en'],
            'content_en' => $decoded['content_en']
        ];
    }

    /**
     * Translate simple text (like category names) from Arabic to English
     *
     * @param string $text Arabic text to translate
     * @return string|null Returns translated English text or null on failure
     */
    public function translateText(string $text): ?string
    {
        try {
            $prompt = $this->buildSimpleTranslationPrompt($text);
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ],
                    'thinkingConfig' => [
                        'thinkingBudget' => 0,
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error (simple translation)', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }

            $result = $response->json();
            
            // Extract the translated text from Gemini response
            $translatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$translatedText) {
                Log::error('No translation text in Gemini response', ['response' => $result]);
                return null;
            }

            // Clean up the response (remove any extra whitespace or quotes)
            return trim($translatedText, " \n\r\t\v\0\"'");

        } catch (\Exception $e) {
            Log::error('Simple Translation Exception', [
                'message' => $e->getMessage(),
                'text' => $text,
            ]);
            return null;
        }
    }

    /**
     * Build a simple translation prompt for short texts
     */
    protected function buildSimpleTranslationPrompt(string $text): string
    {
        return <<<PROMPT
You are a professional translator with 30+ years of experience specializing in Arabic-to-English translation for news websites and media outlets.

# Task
Translate the following Arabic text to English professionally and accurately.

# Critical Requirements
1. Provide a COMPLETE and PROFESSIONAL translation
2. DO NOT abbreviate, shorten, or summarize - translate EVERYTHING
3. Use standard journalistic English terminology
4. Maintain the exact meaning and tone
5. Use title case for titles (capitalize first letter of each main word)
6. Return ONLY the translated text, no explanations or quotes
7. For video titles, article titles, or long texts: translate FULLY without cutting any words

# Examples
- "كنوز مصر المفقودة: الملكة الفرعونية المحاربة" → "Lost Treasures of Egypt: The Warrior Pharaoh Queen"
- "الدحيح | اكتشاف مقبرة توت" → "Al-Dahih | Discovery of Tutankhamun's Tomb"
- "نفرتيتي.. سر القبر المفقود" → "Nefertiti: The Secret of the Lost Tomb"

# Arabic Text to Translate
{$text}

# Full Professional English Translation:
PROMPT;
    }

    /**
     * Test the API connection
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/models/{$this->model}?key={$this->apiKey}");
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Gemini connection test failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
