<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use App\Jobs\TranslateContentJob;
use App\Services\GeminiTranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class TranslationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and category for testing
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function it_dispatches_translation_job_when_article_is_created()
    {
        Queue::fake();

        $article = Article::create([
            'title' => 'عنوان المقال التجريبي',
            'content' => '<p>هذا محتوى تجريبي للمقال</p>',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
        ]);

        Queue::assertPushed(TranslateContentJob::class, function ($job) use ($article) {
            return $job->article->id === $article->id;
        });
    }

    /** @test */
    public function it_does_not_dispatch_job_for_articles_without_content()
    {
        Queue::fake();

        $article = Article::create([
            'title' => '',
            'content' => '',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
        ]);

        Queue::assertNotPushed(TranslateContentJob::class);
    }

    /** @test */
    public function it_updates_article_with_english_translation()
    {
        // This test requires actual Gemini API key
        if (empty(config('services.gemini.api_key'))) {
            $this->markTestSkipped('Gemini API key not configured');
        }

        $article = Article::factory()->create([
            'title' => 'الأخبار العاجلة',
            'content' => '<p>هذا خبر عاجل من الموقع</p>',
        ]);

        $service = new GeminiTranslationService();
        $translation = $service->translateContent($article->title, $article->content);

        $this->assertNotNull($translation);
        $this->assertArrayHasKey('title_en', $translation);
        $this->assertArrayHasKey('content_en', $translation);
        $this->assertNotEmpty($translation['title_en']);
        $this->assertNotEmpty($translation['content_en']);
    }

    /** @test */
    public function it_preserves_html_in_translation()
    {
        // This test requires actual Gemini API key
        if (empty(config('services.gemini.api_key'))) {
            $this->markTestSkipped('Gemini API key not configured');
        }

        $content = '<h2>عنوان فرعي</h2><p>فقرة نصية</p><ul><li>نقطة أولى</li></ul>';

        $service = new GeminiTranslationService();
        $translation = $service->translateContent('عنوان', $content);

        $this->assertNotNull($translation);
        $this->assertStringContainsString('<h2>', $translation['content_en']);
        $this->assertStringContainsString('<p>', $translation['content_en']);
        $this->assertStringContainsString('<ul>', $translation['content_en']);
        $this->assertStringContainsString('<li>', $translation['content_en']);
    }

    /** @test */
    public function translation_service_can_test_connection()
    {
        if (empty(config('services.gemini.api_key'))) {
            $this->markTestSkipped('Gemini API key not configured');
        }

        $service = new GeminiTranslationService();
        $connected = $service->testConnection();

        $this->assertTrue($connected);
    }

    /** @test */
    public function it_re_dispatches_translation_when_content_is_updated()
    {
        Queue::fake();

        $article = Article::factory()->create([
            'title' => 'عنوان أصلي',
            'content' => '<p>محتوى أصلي</p>',
        ]);

        Queue::assertPushed(TranslateContentJob::class, 1);

        $article->update([
            'title' => 'عنوان محدث',
        ]);

        Queue::assertPushed(TranslateContentJob::class, 2);
    }

    /** @test */
    public function article_has_english_translation_fields()
    {
        $article = Article::factory()->create();

        $this->assertTrue(in_array('title_en', $article->getFillable()));
        $this->assertTrue(in_array('content_en', $article->getFillable()));
    }
}
