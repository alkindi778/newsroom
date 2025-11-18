<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EmbeddingService
{
    private string $apiKey;
    private string $model = 'gemini-embedding-001';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Validate API key before making requests
     */
    private function validateApiKey(): void
    {
        if (!$this->apiKey) {
            throw new Exception('Gemini API key not configured. Please set GEMINI_API_KEY in .env');
        }
    }

    /**
     * Generate embedding for a given text
     * 
     * @param string $text The text to embed
     * @param string $taskType The task type (RETRIEVAL_DOCUMENT, RETRIEVAL_QUERY, etc.)
     * @return array The embedding vector
     * @throws Exception
     */
    public function generateEmbedding(string $text, string $taskType = 'RETRIEVAL_DOCUMENT'): array
    {
        try {
            $this->validateApiKey();
            
            $response = Http::timeout(60)
                ->withHeader('x-goog-api-key', $this->apiKey)
                ->post("{$this->baseUrl}/{$this->model}:embedContent", [
                    'model' => "models/{$this->model}",
                    'content' => [
                        'parts' => [
                            [
                                'text' => $text
                            ]
                        ]
                    ],
                    'task_type' => $taskType,
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'text_length' => strlen($text)
                ]);
                throw new Exception("Gemini API Error: {$response->status()} - {$response->body()}");
            }

            $data = $response->json();
            
            if (!isset($data['embedding']['values'])) {
                throw new Exception('Invalid response format from Gemini API');
            }

            return $data['embedding']['values'];
        } catch (Exception $e) {
            Log::error('Embedding generation failed', [
                'error' => $e->getMessage(),
                'text' => substr($text, 0, 100)
            ]);
            throw $e;
        }
    }

    /**
     * Generate embeddings for multiple texts at once
     * 
     * @param array $texts Array of texts to embed
     * @param string $taskType The task type
     * @return array Array of embeddings
     * @throws Exception
     */
    public function generateBatchEmbeddings(array $texts, string $taskType = 'RETRIEVAL_DOCUMENT'): array
    {
        try {
            $this->validateApiKey();
            
            $parts = array_map(fn($text) => ['text' => $text], $texts);

            $response = Http::timeout(60)
                ->withHeader('x-goog-api-key', $this->apiKey)
                ->post("{$this->baseUrl}/{$this->model}:embedContent", [
                    'model' => "models/{$this->model}",
                    'content' => [
                        'parts' => $parts
                    ],
                    'task_type' => $taskType,
                ]);

            if (!$response->successful()) {
                Log::error('Gemini Batch API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception("Gemini API Error: {$response->status()}");
            }

            $data = $response->json();
            
            if (!isset($data['embeddings'])) {
                throw new Exception('Invalid response format from Gemini API');
            }

            return array_map(fn($item) => $item['values'], $data['embeddings']);
        } catch (Exception $e) {
            Log::error('Batch embedding generation failed', [
                'error' => $e->getMessage(),
                'count' => count($texts)
            ]);
            throw $e;
        }
    }

    /**
     * Prepare text for embedding (combine title, subtitle, and content)
     * Limit to 30KB to stay within API limits
     * 
     * @param string $title
     * @param string|null $subtitle
     * @param string $content
     * @return string
     */
    public function prepareText(string $title, ?string $subtitle, string $content): string
    {
        $parts = [$title];
        
        if ($subtitle) {
            $parts[] = $subtitle;
        }
        
        $parts[] = $content;
        
        $text = implode(' ', $parts);
        
        // Limit to 9900 bytes to stay within API limits (10KB max)
        // Use proper UTF-8 truncation to avoid breaking multi-byte characters
        $maxBytes = 9900;
        $encoded = $text;
        
        if (strlen($encoded) > $maxBytes) {
            // Truncate and handle UTF-8 properly
            $truncated = substr($encoded, 0, $maxBytes);
            // Remove incomplete multi-byte characters at the end
            $text = mb_convert_encoding($truncated, 'UTF-8', 'UTF-8');
        }
        
        return $text;
    }
}
