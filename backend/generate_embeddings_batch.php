<?php
/**
 * Generate embeddings for articles in batches
 * Usage: php generate_embeddings_batch.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Services\EmbeddingService;

// إعدادات
$batchSize = 100;        // عدد المقالات في كل دفعة
$maxBatches = 5;         // عدد الدفعات (500 مقال في المرة)
$delayBetweenArticles = 1; // تأخير بالثواني بين كل مقال
$delayBetweenBatches = 10; // تأخير بالثواني بين كل دفعة

echo "🤖 Starting embeddings generation...\n";
echo "Configuration:\n";
echo "  - Batch size: $batchSize articles\n";
echo "  - Max batches: $maxBatches\n";
echo "  - Total: " . ($batchSize * $maxBatches) . " articles max\n\n";

$embeddingService = app(EmbeddingService::class);
$totalProcessed = 0;
$totalSuccess = 0;
$totalFailed = 0;

for ($i = 0; $i < $maxBatches; $i++) {
    // جلب المقالات بدون embeddings
    $articles = Article::doesntHave('embedding')
        ->limit($batchSize)
        ->get();
    
    if ($articles->isEmpty()) {
        echo "\n✅ Done! No more articles without embeddings.\n";
        break;
    }
    
    echo "📦 Processing batch " . ($i + 1) . "/" . $maxBatches . " (" . $articles->count() . " articles)...\n";
    
    foreach ($articles as $article) {
        $totalProcessed++;
        
        try {
            // تحضير النص
            $parts = [$article->title];
            
            if ($article->subtitle) {
                $parts[] = $article->subtitle;
            }
            
            $parts[] = strip_tags($article->content);
            
            $text = implode(' ', $parts);
            
            // تحديد الحجم إلى 9900 بايت
            if (strlen($text) > 9900) {
                $text = substr($text, 0, 9900);
                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
            }
            
            // توليد embedding
            $embedding = $embeddingService->generateEmbedding($text, 'RETRIEVAL_DOCUMENT');
            
            // حذف embedding القديم إن وجد
            if ($article->embedding) {
                $article->embedding->delete();
            }
            
            // حفظ embedding الجديد
            $article->embedding()->create([
                'embedding' => $embedding,
                'text_used' => $text,
                'task_type' => 'RETRIEVAL_DOCUMENT',
            ]);
            
            $totalSuccess++;
            echo "  ✓ Article #{$article->id}: {$article->title}\n";
            
            // تأخير بين المقالات
            sleep($delayBetweenArticles);
            
        } catch (\Exception $e) {
            $totalFailed++;
            echo "  ✗ Article #{$article->id}: {$e->getMessage()}\n";
        }
    }
    
    echo "\n📊 Batch " . ($i + 1) . " completed:\n";
    echo "  - Processed: " . $articles->count() . " articles\n";
    echo "  - Total success: $totalSuccess\n";
    echo "  - Total failed: $totalFailed\n";
    
    // تأخير بين الدفعات
    if ($i < $maxBatches - 1) {
        echo "⏳ Waiting {$delayBetweenBatches} seconds before next batch...\n\n";
        sleep($delayBetweenBatches);
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 Process completed!\n";
echo "📊 Final statistics:\n";
echo "  - Total processed: $totalProcessed articles\n";
echo "  - Successful: $totalSuccess\n";
echo "  - Failed: $totalFailed\n";
echo "  - Success rate: " . round(($totalSuccess / max($totalProcessed, 1)) * 100, 2) . "%\n";
echo str_repeat("=", 50) . "\n";
