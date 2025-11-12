<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleEmbedding;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class VectorSearchService
{
    private EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    /**
     * Search articles using semantic similarity
     * 
     * @param string $query The search query
     * @param int $limit Maximum number of results
     * @param float $threshold Minimum similarity threshold (0-1)
     * @return Collection Articles sorted by similarity
     */
    public function search(string $query, int $limit = 10, float $threshold = 0.1): Collection
    {
        try {
            // Generate embedding for the query
            $queryEmbedding = $this->embeddingService->generateEmbedding(
                $query,
                'RETRIEVAL_QUERY'
            );

            // Get all article embeddings with their articles
            $embeddings = ArticleEmbedding::with('article')
                ->whereHas('article', function ($q) {
                    $q->where('is_published', true)
                      ->where('approval_status', 'approved');
                })
                ->get();

            // Calculate similarity for each article
            $results = $embeddings->map(function ($embedding) use ($queryEmbedding) {
                $similarity = ArticleEmbedding::cosineSimilarity(
                    $queryEmbedding,
                    $embedding->embedding
                );

                return [
                    'article' => $embedding->article,
                    'similarity' => $similarity
                ];
            })
            ->filter(fn($item) => $item['similarity'] >= $threshold)
            ->sortByDesc('similarity')
            ->take($limit);

            Log::info('Vector search results', [
                'query' => $query,
                'total_embeddings' => $embeddings->count(),
                'filtered_results' => $results->count(),
                'threshold' => $threshold
            ]);

            // Get article IDs and fetch them as Eloquent Collection
            $articleIds = $results->pluck('article.id')->toArray();
            return Article::whereIn('id', $articleIds)->get();
        } catch (\Exception $e) {
            Log::error('Vector search failed', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);
            return Article::query()->limit(0)->get();
        }
    }

    /**
     * Find similar articles to a given article
     * 
     * @param Article $article The article to find similar ones for
     * @param int $limit Maximum number of results
     * @param float $threshold Minimum similarity threshold
     * @return Collection Similar articles
     */
    public function findSimilarArticles(Article $article, int $limit = 5, float $threshold = 0.3): Collection
    {
        try {
            if (!$article->embedding) {
                return Article::query()->limit(0)->get();
            }

            $articleEmbedding = $article->embedding->embedding;

            // Get other articles' embeddings
            $embeddings = ArticleEmbedding::with('article')
                ->where('article_id', '!=', $article->id)
                ->whereHas('article', function ($q) {
                    $q->where('is_published', true)
                      ->where('approval_status', 'approved');
                })
                ->get();

            // Calculate similarity
            $results = $embeddings->map(function ($embedding) use ($articleEmbedding) {
                $similarity = ArticleEmbedding::cosineSimilarity(
                    $articleEmbedding,
                    $embedding->embedding
                );

                return [
                    'article' => $embedding->article,
                    'similarity' => $similarity
                ];
            })
            ->filter(fn($item) => $item['similarity'] >= $threshold)
            ->sortByDesc('similarity')
            ->take($limit);

            Log::info('Similar articles found', [
                'article_id' => $article->id,
                'total_embeddings' => $embeddings->count(),
                'filtered_results' => $results->count(),
                'threshold' => $threshold
            ]);

            // Get article IDs and fetch them as Eloquent Collection
            $articleIds = $results->pluck('article.id')->toArray();
            return Article::whereIn('id', $articleIds)->get();
        } catch (\Exception $e) {
            Log::error('Similar articles search failed', [
                'error' => $e->getMessage(),
                'article_id' => $article->id
            ]);
            return Article::query()->limit(0)->get();
        }
    }

    /**
     * Detect duplicate or very similar articles
     * 
     * @param Article $article The article to check
     * @param float $threshold Similarity threshold for duplicates (default 0.95)
     * @return Collection Potential duplicate articles
     */
    public function findDuplicates(Article $article, float $threshold = 0.95): Collection
    {
        try {
            if (!$article->embedding) {
                return Article::query()->limit(0)->get();
            }

            $articleEmbedding = $article->embedding->embedding;

            $embeddings = ArticleEmbedding::with('article')
                ->where('article_id', '!=', $article->id)
                ->get();

            $results = $embeddings->map(function ($embedding) use ($articleEmbedding) {
                $similarity = ArticleEmbedding::cosineSimilarity(
                    $articleEmbedding,
                    $embedding->embedding
                );

                return [
                    'article' => $embedding->article,
                    'similarity' => $similarity
                ];
            })
            ->filter(fn($item) => $item['similarity'] >= $threshold)
            ->sortByDesc('similarity');

            // Get article IDs and fetch them as Eloquent Collection
            $articleIds = $results->pluck('article.id')->toArray();
            return Article::whereIn('id', $articleIds)->get();
        } catch (\Exception $e) {
            Log::error('Duplicate detection failed', [
                'error' => $e->getMessage(),
                'article_id' => $article->id
            ]);
            return Article::query()->limit(0)->get();
        }
    }

    /**
     * Get similarity score between two articles
     * 
     * @param Article $article1
     * @param Article $article2
     * @return float Similarity score (0-1)
     */
    public function getSimilarityScore(Article $article1, Article $article2): float
    {
        try {
            if (!$article1->embedding || !$article2->embedding) {
                return 0;
            }

            return ArticleEmbedding::cosineSimilarity(
                $article1->embedding->embedding,
                $article2->embedding->embedding
            );
        } catch (\Exception $e) {
            Log::error('Similarity score calculation failed', [
                'error' => $e->getMessage(),
                'article1_id' => $article1->id,
                'article2_id' => $article2->id
            ]);
            return 0;
        }
    }
}
