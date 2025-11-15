<?php

namespace App\Console\Commands;

use App\Models\SocialMediaPost;
use App\Services\SocialMediaService;
use Illuminate\Console\Command;

class PublishScheduledSocialPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social-media:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'نشر المنشورات المجدولة على وسائل التواصل الاجتماعي';

    /**
     * Execute the console command.
     */
    public function handle(SocialMediaService $socialMediaService): int
    {
        $this->info('جاري البحث عن منشورات مجدولة...');

        // الحصول على المنشورات المجدولة التي حان وقتها
        $posts = SocialMediaPost::where('status', 'scheduled')
            ->where('scheduled_for', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('لا توجد منشورات مجدولة للنشر الآن');
            return 0;
        }

        $this->info("وجدت {$posts->count()} منشورات مجدولة");

        foreach ($posts as $post) {
            try {
                // محاولة نشر المنشور
                $result = $this->publishPost($post, $socialMediaService);

                if ($result) {
                    $this->info("✅ تم نشر المنشور #{$post->id} على {$post->platform}");
                } else {
                    $this->error("❌ فشل نشر المنشور #{$post->id} على {$post->platform}");
                }
            } catch (\Exception $e) {
                $this->error("❌ خطأ في المنشور #{$post->id}: {$e->getMessage()}");
                $post->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        $this->info('✅ انتهت معالجة المنشورات المجدولة');
        return 0;
    }

    /**
     * نشر منشور واحد
     */
    private function publishPost(SocialMediaPost $post, SocialMediaService $service): bool
    {
        $article = $post->article;
        $config = config('social-media')['platforms'][$post->platform];

        try {
            $externalId = match ($post->platform) {
                'facebook' => $this->publishToFacebook($article, $post->message, $config),
                'twitter' => $this->publishToTwitter($article, $post->message, $config),
                'telegram' => $this->publishToTelegram($article, $post->message, $config),
                default => null,
            };

            if ($externalId) {
                $post->update([
                    'external_id' => $externalId,
                    'status' => 'published',
                    'published_at' => now(),
                ]);
                return true;
            }
        } catch (\Exception $e) {
            $post->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return false;
    }

    private function publishToFacebook($article, $message, $config)
    {
        // نفس المنطق من SocialMediaService
        return null;
    }

    private function publishToTwitter($article, $message, $config)
    {
        // نفس المنطق من SocialMediaService
        return null;
    }

    private function publishToTelegram($article, $message, $config)
    {
        // نفس المنطق من SocialMediaService
        return null;
    }
}
