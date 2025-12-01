<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateArticleSeoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $article;

    /**
     * Create a new job instance.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Execute the job.
     */
    public function handle(GeminiService $geminiService): void
    {
        // تحقق مما إذا كان المقال يحتوي بالفعل على بيانات SEO يدوية
        if (!empty($this->article->meta_description) && !empty($this->article->keywords)) {
            return;
        }

        try {
            $seoData = $geminiService->generateSeoData(
                $this->article->title,
                $this->article->content
            );

            if (!empty($seoData)) {
                $updates = [];
                
                if (empty($this->article->meta_description) && !empty($seoData['meta_description'])) {
                    $updates['meta_description'] = $seoData['meta_description'];
                }

                if (empty($this->article->keywords) && !empty($seoData['keywords'])) {
                    $updates['keywords'] = $seoData['keywords'];
                }

                if (!empty($updates)) {
                    $this->article->update($updates);
                    Log::info("SEO data generated automatically for article #{$this->article->id}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to generate SEO for article #{$this->article->id}: " . $e->getMessage());
        }
    }
}
