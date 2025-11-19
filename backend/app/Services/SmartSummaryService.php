<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class SmartSummaryService
{
    /**
     * Generate smart summary for an article
     */
    public function generateSummary(Article $article): array
    {
        try {
            // Check cache first
            $cacheKey = "smart_summary_article_{$article->id}";
            $cachedSummary = Cache::get($cacheKey);
            
            if ($cachedSummary) {
                Log::info("Smart Summary: Using cached summary for article {$article->id}");
                return $cachedSummary;
            }

            // Prepare article content for processing
            $content = $this->prepareContent($article);
            
            // Generate summary using multiple methods
            $summary = $this->processContent($content);
            
            // Enhance summary with additional data
            $enhancedSummary = $this->enhanceSummary($summary, $article);
            
            // Cache the result for 24 hours
            Cache::put($cacheKey, $enhancedSummary, now()->addHours(24));
            
            Log::info("Smart Summary: Generated new summary for article {$article->id}");
            
            return $enhancedSummary;
            
        } catch (Exception $e) {
            Log::error("Smart Summary Error for article {$article->id}: " . $e->getMessage());
            throw new Exception('فشل في إنشاء الملخص الذكي: ' . $e->getMessage());
        }
    }

    /**
     * Prepare article content for processing
     */
    private function prepareContent(Article $article): string
    {
        // Clean HTML and extract text
        $content = strip_tags($article->content);
        
        // Add title and subtitle for context
        $fullText = $article->title . "\n\n";
        
        if ($article->subtitle) {
            $fullText .= $article->subtitle . "\n\n";
        }
        
        $fullText .= $content;
        
        // Clean up extra whitespace and normalize
        $fullText = preg_replace('/\s+/', ' ', $fullText);
        $fullText = trim($fullText);
        
        return $fullText;
    }

    /**
     * Process content to generate summary
     */
    private function processContent(string $content): array
    {
        // Split content into sentences
        $sentences = $this->splitIntoSentences($content);
        
        if (empty($sentences)) {
            throw new Exception('المحتوى فارغ أو غير صالح للمعالجة');
        }

        // Extract key points using extractive summarization
        $keyPoints = $this->extractKeyPoints($sentences);
        
        // Generate abstractive summary
        $summaryText = $this->generateAbstractiveSummary($sentences);
        
        // Analyze sentiment
        $sentiment = $this->analyzeSentiment($content);
        
        // Extract keywords
        $keywords = $this->extractKeywords($content);
        
        // Identify main topic
        $mainTopic = $this->identifyMainTopic($content, $keywords);
        
        return [
            'points' => $keyPoints,
            'text' => $summaryText,
            'sentiment' => $sentiment,
            'keywords' => $keywords,
            'mainTopic' => $mainTopic,
            'originalLength' => strlen($content),
            'summaryLength' => strlen($summaryText)
        ];
    }

    /**
     * Split text into sentences
     */
    private function splitIntoSentences(string $text): array
    {
        // Arabic sentence splitting considering Arabic punctuation
        $sentences = preg_split('/[.!?؟]/', $text);
        
        // Clean and filter sentences
        $sentences = array_map('trim', $sentences);
        $sentences = array_filter($sentences, function($sentence) {
            return strlen($sentence) > 10; // Minimum sentence length
        });
        
        return array_values($sentences);
    }

    /**
     * Extract key points from sentences
     */
    private function extractKeyPoints(array $sentences): array
    {
        if (count($sentences) <= 3) {
            return array_slice($sentences, 0, 3);
        }

        // Score sentences based on various factors
        $scoredSentences = [];
        
        foreach ($sentences as $index => $sentence) {
            $score = 0;
            
            // Position weight (beginning and end are more important)
            if ($index < 2) {
                $score += 2;
            } elseif ($index >= count($sentences) - 2) {
                $score += 1.5;
            }
            
            // Length weight (medium sentences are preferred)
            $wordCount = str_word_count($sentence);
            if ($wordCount >= 10 && $wordCount <= 30) {
                $score += 1;
            }
            
            // Keyword density
            $keywordCount = $this->countImportantWords($sentence);
            $score += $keywordCount * 0.5;
            
            // Numbers and dates (often important)
            if (preg_match('/\d+/', $sentence)) {
                $score += 0.5;
            }
            
            $scoredSentences[] = [
                'sentence' => $sentence,
                'score' => $score,
                'index' => $index
            ];
        }
        
        // Sort by score and take top 3-5 sentences
        usort($scoredSentences, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        $topSentences = array_slice($scoredSentences, 0, min(5, count($scoredSentences)));
        
        // Sort selected sentences by original order
        usort($topSentences, function($a, $b) {
            return $a['index'] <=> $b['index'];
        });
        
        return array_map(function($item) {
            return $item['sentence'];
        }, $topSentences);
    }

    /**
     * Generate abstractive summary
     */
    private function generateAbstractiveSummary(array $sentences): string
    {
        // For now, use extractive method with better sentence selection
        $selectedSentences = array_slice($sentences, 0, min(3, count($sentences)));
        
        // Try to create a coherent summary
        $summary = implode(' ', $selectedSentences);
        
        // Ensure reasonable length (150-300 characters)
        if (strlen($summary) > 300) {
            $summary = substr($summary, 0, 297) . '...';
        }
        
        return $summary;
    }

    /**
     * Analyze sentiment of the content
     */
    private function analyzeSentiment(string $content): string
    {
        // Simple Arabic sentiment analysis
        $positiveWords = [
            'جيد', 'ممتاز', 'رائع', 'نجح', 'تقدم', 'إنجاز', 'فوز', 'نصر', 
            'سعادة', 'فرحة', 'تحسن', 'ازدهار', 'نمو', 'تطور', 'إيجابي'
        ];
        
        $negativeWords = [
            'سيء', 'فشل', 'خطأ', 'مشكلة', 'أزمة', 'كارثة', 'حزن', 'غضب',
            'تراجع', 'انخفاض', 'خسارة', 'هزيمة', 'سلبي', 'ضرر', 'خطر'
        ];
        
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($content, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($content, $word);
        }
        
        if ($positiveCount > $negativeCount) {
            return 'positive';
        } elseif ($negativeCount > $positiveCount) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }

    /**
     * Extract keywords from content
     */
    private function extractKeywords(string $content): array
    {
        // Remove common Arabic stop words
        $stopWords = [
            'في', 'على', 'من', 'إلى', 'عن', 'مع', 'هذا', 'هذه', 'ذلك', 'تلك',
            'التي', 'الذي', 'التي', 'والتي', 'والذي', 'كان', 'كانت', 'يكون',
            'تكون', 'هو', 'هي', 'أن', 'إن', 'لا', 'ما', 'قد', 'قال', 'قالت'
        ];
        
        // Extract words and count frequency
        $words = str_word_count($content, 1);
        $wordFreq = array_count_values($words);
        
        // Filter out stop words and short words
        $filteredWords = [];
        foreach ($wordFreq as $word => $freq) {
            if (strlen($word) >= 3 && !in_array($word, $stopWords) && $freq >= 2) {
                $filteredWords[$word] = $freq;
            }
        }
        
        // Sort by frequency and take top keywords
        arsort($filteredWords);
        
        return array_keys(array_slice($filteredWords, 0, 8));
    }

    /**
     * Identify main topic
     */
    private function identifyMainTopic(string $content, array $keywords): string
    {
        // Use the most frequent keyword as main topic
        if (empty($keywords)) {
            return 'موضوع عام';
        }
        
        return $keywords[0];
    }

    /**
     * Count important words in a sentence
     */
    private function countImportantWords(string $sentence): int
    {
        $importantWords = [
            'حكومة', 'رئيس', 'وزير', 'مجلس', 'برلمان', 'قرار', 'قانون', 'اتفاق',
            'اقتصاد', 'سياسة', 'تعليم', 'صحة', 'أمن', 'دفاع', 'خارجية', 'داخلية',
            'مال', 'استثمار', 'شركة', 'بنك', 'سوق', 'تجارة', 'صناعة', 'زراعة'
        ];
        
        $count = 0;
        foreach ($importantWords as $word) {
            $count += substr_count($sentence, $word);
        }
        
        return $count;
    }

    /**
     * Enhance summary with additional metadata
     */
    private function enhanceSummary(array $summary, Article $article): array
    {
        return array_merge($summary, [
            'articleId' => $article->id,
            'articleTitle' => $article->title,
            'category' => $article->category?->name,
            'author' => $article->user?->name,
            'publishedAt' => $article->published_at?->toDateTimeString(),
            'wordCount' => str_word_count(strip_tags($article->content)),
            'readingTime' => ceil(str_word_count(strip_tags($article->content)) / 200), // 200 words per minute
            'generatedAt' => now()->toDateTimeString(),
            'version' => '1.0'
        ]);
    }

    /**
     * Clear cache for an article's summary
     */
    public function clearCache(Article $article): bool
    {
        $cacheKey = "smart_summary_article_{$article->id}";
        return Cache::forget($cacheKey);
    }

    /**
     * Get cached summary if exists
     */
    public function getCachedSummary(Article $article): ?array
    {
        $cacheKey = "smart_summary_article_{$article->id}";
        return Cache::get($cacheKey);
    }
}
