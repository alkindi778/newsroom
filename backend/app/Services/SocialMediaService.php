<?php

namespace App\Services;

use App\Models\Article;
use App\Models\SocialMediaPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialMediaService
{
    /**
     * نشر المقالة على جميع المنصات المفعّلة
     */
    public function publishArticle(Article $article, bool $schedule = false): array
    {
        $results = [];
        $config = config('social-media');

        foreach ($config['platforms'] as $platform => $settings) {
            if (!$settings['enabled'] || !$settings['auto_publish']) {
                continue;
            }

            try {
                $message = $this->buildMessage($article, $platform);
                
                if ($schedule && isset($article->scheduled_publish_at)) {
                    $results[$platform] = $this->schedulePost($article, $platform, $message);
                } else {
                    $results[$platform] = $this->publishToplatform($article, $platform, $message);
                }
            } catch (\Exception $e) {
                Log::error("Social Media Error ({$platform}): " . $e->getMessage());
                $results[$platform] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * بناء رسالة المنشور
     */
    private function buildMessage(Article $article, string $platform): string
    {
        $config = config('social-media');
        $template = $config['templates']['article'];

        $title = $article->title;
        $excerpt = substr($article->excerpt ?? $article->content ?? '', 0, 150) . '...';
        $link = url('/news/' . $article->slug);
        $hashtags = $this->buildHashtags($article);

        $message = str_replace(
            ['{title}', '{excerpt}', '{link}', '{hashtags}'],
            [$title, $excerpt, $link, $hashtags],
            $template
        );

        // تقليص الرسالة حسب حد المنصة
        if ($platform === 'twitter') {
            $message = substr($message, 0, $config['global']['excerpt_length']);
        }

        return $message;
    }

    /**
     * بناء الهاشتاجات
     */
    private function buildHashtags(Article $article): string
    {
        $config = config('social-media');
        
        if (!$config['global']['include_hashtags']) {
            return '';
        }

        $hashtags = [];

        if ($config['global']['include_category'] && $article->category) {
            $hashtags[] = '#' . str_replace(' ', '', $article->category->name);
        }

        // إضافة كلمات مفتاحية
        if ($article->keywords) {
            $keywords = is_array($article->keywords) 
                ? $article->keywords 
                : explode(',', $article->keywords);
            
            foreach (array_slice($keywords, 0, $config['global']['max_hashtags']) as $keyword) {
                $hashtags[] = '#' . str_replace(' ', '', trim($keyword));
            }
        }

        return implode(' ', $hashtags);
    }

    /**
     * النشر على Facebook
     */
    private function publishToplatform(Article $article, string $platform, string $message): array
    {
        $config = config('social-media')['platforms'][$platform];

        $post = SocialMediaPost::create([
            'article_id' => $article->id,
            'platform' => $platform,
            'message' => $message,
            'status' => 'pending',
        ]);

        try {
            $externalId = match ($platform) {
                'facebook' => $this->publishToFacebook($article, $message, $config),
                'twitter' => $this->publishToTwitter($article, $message, $config),
                'telegram' => $this->publishToTelegram($article, $message, $config),
                default => null,
            };

            if ($externalId) {
                $post->update([
                    'external_id' => $externalId,
                    'status' => 'published',
                    'published_at' => now(),
                ]);

                return [
                    'success' => true,
                    'platform' => $platform,
                    'external_id' => $externalId,
                ];
            }
        } catch (\Exception $e) {
            $post->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }

        return [
            'success' => false,
            'error' => 'Unknown error',
        ];
    }

    /**
     * النشر على Facebook
     */
    private function publishToFacebook(Article $article, string $message, array $config): ?string
    {
        $pageId = $config['page_id'];
        $accessToken = $config['access_token'];
        $apiVersion = $config['api_version'];

        $url = "https://graph.facebook.com/{$apiVersion}/{$pageId}/feed";

        $payload = [
            'message' => $message,
            'access_token' => $accessToken,
        ];

        // إضافة الصورة إذا كانت مفعّلة
        if ($config['include_image'] && $article->image_path) {
            $payload['picture'] = asset('storage/' . $article->image_path);
        }

        // إضافة الرابط
        if ($config['include_link']) {
            $payload['link'] = url('/news/' . $article->slug);
        }

        $response = Http::post($url, $payload);

        if ($response->successful()) {
            return $response->json('id');
        }

        throw new \Exception('Facebook API Error: ' . $response->body());
    }

    /**
     * النشر على Twitter
     */
    private function publishToTwitter(Article $article, string $message, array $config): ?string
    {
        $bearerToken = $config['bearer_token'];

        $url = 'https://api.twitter.com/2/tweets';

        $payload = [
            'text' => $message,
        ];

        $response = Http::withToken($bearerToken)
            ->post($url, $payload);

        if ($response->successful()) {
            return $response->json('data.id');
        }

        throw new \Exception('Twitter API Error: ' . $response->body());
    }

    /**
     * النشر على Telegram
     */
    private function publishToTelegram(Article $article, string $message, array $config): ?string
    {
        $botToken = $config['bot_token'];
        $channelId = $config['channel_id'];

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $payload = [
            'chat_id' => $channelId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
        ];

        // إضافة الصورة إذا كانت مفعّلة
        if ($config['include_image'] && $article->image_path) {
            $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
            $payload['photo'] = asset('storage/' . $article->image_path);
            $payload['caption'] = $message;
            unset($payload['text']);
        }

        $response = Http::post($url, $payload);

        if ($response->successful()) {
            return $response->json('result.message_id');
        }

        throw new \Exception('Telegram API Error: ' . $response->body());
    }

    /**
     * جدولة المنشور
     */
    private function schedulePost(Article $article, string $platform, string $message): array
    {
        $post = SocialMediaPost::create([
            'article_id' => $article->id,
            'platform' => $platform,
            'message' => $message,
            'status' => 'scheduled',
            'scheduled_for' => $article->scheduled_publish_at ?? now()->addHours(1),
        ]);

        return [
            'success' => true,
            'platform' => $platform,
            'scheduled_for' => $post->scheduled_for,
        ];
    }
}
