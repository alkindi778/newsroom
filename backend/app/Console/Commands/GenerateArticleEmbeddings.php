<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateArticleEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'embeddings:generate {--force : Force regenerate all embeddings} {--article-id= : Generate embedding for specific article}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate embeddings for articles using Google Gemini API';

    private EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        parent::__construct();
        $this->embeddingService = $embeddingService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $articleId = $this->option('article-id');
            $force = $this->option('force');

            if ($articleId) {
                return $this->generateForArticle((int)$articleId);
            }

            return $this->generateForAllArticles($force);
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            Log::error('GenerateArticleEmbeddings command failed', [
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    /**
     * Generate embedding for a specific article
     */
    private function generateForArticle(int $articleId): int
    {
        $article = Article::find($articleId);

        if (!$article) {
            $this->error("Article with ID {$articleId} not found");
            return 1;
        }

        $this->info("Generating embedding for article: {$article->title}");

        try {
            $text = $this->prepareText($article);
            $embedding = $this->embeddingService->generateEmbedding(
                $text,
                'RETRIEVAL_DOCUMENT'
            );

            // Delete existing embedding if exists
            if ($article->embedding) {
                $article->embedding->delete();
            }

            // Create new embedding
            $article->embedding()->create([
                'embedding' => $embedding,
                'text_used' => $text,
                'task_type' => 'RETRIEVAL_DOCUMENT',
            ]);

            $this->info("✓ Embedding generated successfully for article ID: {$articleId}");
            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Failed to generate embedding: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Generate embeddings for all articles
     */
    private function generateForAllArticles(bool $force = false): int
    {
        $query = Article::query();

        if (!$force) {
            // Only process articles without embeddings
            $query->doesntHave('embedding');
        }

        $articles = $query->get();
        $total = $articles->count();

        if ($total === 0) {
            $this->info('No articles to process');
            return 0;
        }

        $this->info("Processing {$total} articles...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $successful = 0;
        $failed = 0;

        foreach ($articles as $article) {
            try {
                $text = $this->prepareText($article);
                $embedding = $this->embeddingService->generateEmbedding(
                    $text,
                    'RETRIEVAL_DOCUMENT'
                );

                // Delete existing embedding if exists
                if ($article->embedding) {
                    $article->embedding->delete();
                }

                // Create new embedding
                $article->embedding()->create([
                    'embedding' => $embedding,
                    'text_used' => $text,
                    'task_type' => 'RETRIEVAL_DOCUMENT',
                ]);

                $successful++;
            } catch (\Exception $e) {
                $failed++;
                Log::error('Failed to generate embedding for article', [
                    'article_id' => $article->id,
                    'error' => $e->getMessage()
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✓ Completed: {$successful} successful, {$failed} failed");

        return $failed > 0 ? 1 : 0;
    }

    /**
     * Prepare text for embedding
     */
    private function prepareText(Article $article): string
    {
        $parts = [$article->title];

        if ($article->subtitle) {
            $parts[] = $article->subtitle;
        }

        $parts[] = strip_tags($article->content);

        $text = implode(' ', $parts);
        
        // Limit to 9900 bytes to stay within API limits (10KB max)
        if (strlen($text) > 9900) {
            $text = substr($text, 0, 9900);
            // Remove incomplete multi-byte characters
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }
        
        return $text;
    }
}
