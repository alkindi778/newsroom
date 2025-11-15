<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Video;
use App\Models\Opinion;
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

    /**
     * نشر الفيديو على جميع المنصات المفعّلة
     */
    public function publishVideo(Video $video, bool $schedule = false): array
    {
        $results = [];
        $config = config('social-media');

        foreach ($config['platforms'] as $platform => $settings) {
            if (!$settings['enabled'] || !$settings['auto_publish']) {
                continue;
            }

            try {
                $message = $this->buildVideoMessage($video, $platform);
                
                if ($schedule && isset($video->scheduled_publish_at)) {
                    $results[$platform] = $this->scheduleVideoPost($video, $platform, $message);
                } else {
                    $results[$platform] = $this->publishVideoToplatform($video, $platform, $message);
                }
            } catch (\Exception $e) {
                Log::error("Social Media Video Error ({$platform}): " . $e->getMessage());
                $results[$platform] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * نشر مقال الرأي على جميع المنصات المفعّلة
     */
    public function publishOpinion(Opinion $opinion, bool $schedule = false): array
    {
        $results = [];
        $config = config('social-media');

        foreach ($config['platforms'] as $platform => $settings) {
            if (!$settings['enabled'] || !$settings['auto_publish']) {
                continue;
            }

            try {
                $message = $this->buildOpinionMessage($opinion, $platform);
                
                if ($schedule && isset($opinion->scheduled_publish_at)) {
                    $results[$platform] = $this->scheduleOpinionPost($opinion, $platform, $message);
                } else {
                    $results[$platform] = $this->publishOpinionToplatform($opinion, $platform, $message);
                }
            } catch (\Exception $e) {
                Log::error("Social Media Opinion Error ({$platform}): " . $e->getMessage());
                $results[$platform] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * بناء رسالة الفيديو
     */
    private function buildVideoMessage(Video $video, string $platform): string
    {
        $config = config('social-media');
        $template = $config['templates']['video'];

        $title = $video->title;
        $link = url('/videos/' . $video->slug);

        $message = str_replace(
            ['{title}', '{link}'],
            [$title, $link],
            $template
        );

        return $message;
    }

    /**
     * بناء رسالة مقال الرأي
     */
    private function buildOpinionMessage(Opinion $opinion, string $platform): string
    {
        $config = config('social-media');
        $template = $config['templates']['article']; // نستخدم نفس template الأخبار

        $title = $opinion->title;
        $link = url('/opinions/' . $opinion->slug);

        $message = str_replace(
            ['{title}', '{link}'],
            [$title, $link],
            $template
        );

        return $message;
    }

    /**
     * نشر الفيديو على المنصة
     */
    private function publishVideoToplatform(Video $video, string $platform, string $message): array
    {
        $config = config('social-media.platforms.' . $platform);

        switch ($platform) {
            case 'telegram':
                return $this->publishVideoToTelegram($video, $message, $config);
            case 'facebook':
                return $this->publishVideoToFacebook($video, $message, $config);
            case 'twitter':
                return $this->publishVideoToTwitter($video, $message, $config);
            default:
                throw new \Exception("Unsupported platform: {$platform}");
        }
    }

    /**
     * نشر مقال الرأي على المنصة
     */
    private function publishOpinionToplatform(Opinion $opinion, string $platform, string $message): array
    {
        $config = config('social-media.platforms.' . $platform);

        switch ($platform) {
            case 'telegram':
                return $this->publishOpinionToTelegram($opinion, $message, $config);
            case 'facebook':
                return $this->publishOpinionToFacebook($opinion, $message, $config);
            case 'twitter':
                return $this->publishOpinionToTwitter($opinion, $message, $config);
            default:
                throw new \Exception("Unsupported platform: {$platform}");
        }
    }

    /**
     * نشر الفيديو على Telegram
     */
    private function publishVideoToTelegram(Video $video, string $message, array $config): array
    {
        $url = "https://api.telegram.org/bot{$config['bot_token']}/sendMessage";

        $payload = [
            'chat_id' => $config['channel_id'],
            'text' => $message,
            'parse_mode' => 'HTML',
        ];

        $response = Http::post($url, $payload);

        if ($response->successful()) {
            $this->recordPost($video->id, 'telegram', $message, 'published', $response->json());
            return ['success' => true, 'response' => $response->json()];
        }

        throw new \Exception('Telegram API Error: ' . $response->body());
    }

    /**
     * نشر مقال الرأي على Telegram
     */
    private function publishOpinionToTelegram(Opinion $opinion, string $message, array $config): array
    {
        $url = "https://api.telegram.org/bot{$config['bot_token']}/sendMessage";

        $payload = [
            'chat_id' => $config['channel_id'],
            'text' => $message,
            'parse_mode' => 'HTML',
        ];

        $response = Http::post($url, $payload);

        if ($response->successful()) {
            $this->recordOpinionPost($opinion->id, 'telegram', $message, 'published', $response->json());
            return ['success' => true, 'response' => $response->json()];
        }

        throw new \Exception('Telegram API Error: ' . $response->body());
    }

    /**
     * نشر الفيديو على Facebook
     */
    private function publishVideoToFacebook(Video $video, string $message, array $config): array
    {
        // TODO: Implement Facebook video publishing
        return ['success' => false, 'error' => 'Facebook publishing not implemented yet'];
    }

    /**
     * نشر الفيديو على Twitter
     */
    private function publishVideoToTwitter(Video $video, string $message, array $config): array
    {
        // TODO: Implement Twitter video publishing
        return ['success' => false, 'error' => 'Twitter publishing not implemented yet'];
    }

    /**
     * نشر مقال الرأي على Facebook
     */
    private function publishOpinionToFacebook(Opinion $opinion, string $message, array $config): array
    {
        // TODO: Implement Facebook opinion publishing
        return ['success' => false, 'error' => 'Facebook publishing not implemented yet'];
    }

    /**
     * نشر مقال الرأي على Twitter
     */
    private function publishOpinionToTwitter(Opinion $opinion, string $message, array $config): array
    {
        // TODO: Implement Twitter opinion publishing
        return ['success' => false, 'error' => 'Twitter publishing not implemented yet'];
    }

    /**
     * تسجيل منشور الفيديو في قاعدة البيانات
     */
    private function recordPost(?int $videoId, string $platform, string $message, string $status, ?array $response): void
    {
        SocialMediaPost::create([
            'video_id' => $videoId,
            'platform' => $platform,
            'message' => $message,
            'status' => $status,
            'response' => $response,
            'published_at' => now(),
        ]);
    }

    /**
     * تسجيل منشور مقال الرأي في قاعدة البيانات
     */
    private function recordOpinionPost(?int $opinionId, string $platform, string $message, string $status, ?array $response): void
    {
        SocialMediaPost::create([
            'opinion_id' => $opinionId,
            'platform' => $platform,
            'message' => $message,
            'status' => $status,
            'response' => $response,
            'published_at' => now(),
        ]);
    }

    /**
     * جدولة منشور فيديو
     */
    private function scheduleVideoPost(Video $video, string $platform, string $message): array
    {
        $post = SocialMediaPost::create([
            'video_id' => $video->id,
            'platform' => $platform,
            'message' => $message,
            'status' => 'scheduled',
            'scheduled_for' => $video->scheduled_publish_at ?? now()->addHours(1),
        ]);

        return [
            'success' => true,
            'platform' => $platform,
            'scheduled_for' => $post->scheduled_for,
        ];
    }

    /**
     * جدولة منشور مقال رأي
     */
    private function scheduleOpinionPost(Opinion $opinion, string $platform, string $message): array
    {
        $post = SocialMediaPost::create([
            'opinion_id' => $opinion->id,
            'platform' => $platform,
            'message' => $message,
            'status' => 'scheduled',
            'scheduled_for' => $opinion->scheduled_publish_at ?? now()->addHours(1),
        ]);

        return [
            'success' => true,
            'platform' => $platform,
            'scheduled_for' => $post->scheduled_for,
        ];
    }
}
