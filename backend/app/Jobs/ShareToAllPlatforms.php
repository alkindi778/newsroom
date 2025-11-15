<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Article;
use App\Models\Video;
use App\Models\Opinion;

class ShareToAllPlatforms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    protected $contentType;
    protected $contentId;

    public function __construct(string $contentType, int $contentId)
    {
        $this->contentType = $contentType;
        $this->contentId = $contentId;
    }

    public function handle()
    {
        try {
            Log::info('ShareToAllPlatforms job started', [
                'type' => $this->contentType,
                'id' => $this->contentId
            ]);

            // التحقق من تفعيل النشر التلقائي
            $autoPublish = config('social-media.global.auto_publish_on_create');
            if (!$autoPublish) {
                Log::info('Auto-publish is disabled');
                return false;
            }

            // جلب المحتوى
            $content = $this->getContent();
            if (!$content) {
                Log::error('Content not found', [
                    'type' => $this->contentType,
                    'id' => $this->contentId
                ]);
                return false;
            }

            // بناء الرسالة والصورة
            [$message, $imagePath] = $this->prepareContent($content);

            // النشر على المنصات المفعلة
            $this->publishToPlatforms($content, $message, $imagePath);

            Log::info('ShareToAllPlatforms job finished successfully', [
                'type' => $this->contentType,
                'id' => $this->contentId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error in ShareToAllPlatforms: ' . $e->getMessage(), [
                'type' => $this->contentType,
                'id' => $this->contentId,
                'attempt' => $this->attempts()
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
            }

            return false;
        }
    }

    protected function getContent()
    {
        return match($this->contentType) {
            'article' => Article::find($this->contentId),
            'video' => Video::find($this->contentId),
            'opinion' => Opinion::find($this->contentId),
            default => null
        };
    }

    protected function prepareContent($content): array
    {
        // بناء الرسالة
        $url = $this->getContentUrl($content);
        $template = config('social-media.templates.' . $this->contentType);
        
        $message = str_replace(
            ['{title}', '{link}'],
            [$content->title, $url],
            $template
        );

        // تحديد الصورة
        $imagePath = null;
        if ($this->contentType === 'article' && $content->image) {
            $imagePath = Storage::disk('public')->path($content->image);
        } elseif ($this->contentType === 'video' && $content->thumbnail) {
            $imagePath = Storage::disk('public')->path($content->thumbnail);
        } elseif ($this->contentType === 'opinion' && $content->image) {
            $imagePath = Storage::disk('public')->path($content->image);
        }

        return [$message, $imagePath];
    }

    protected function getContentUrl($content): string
    {
        return match($this->contentType) {
            'article' => url('/news/' . $content->slug),
            'video' => url('/videos/' . $content->slug),
            'opinion' => url('/opinions/' . $content->slug),
            default => url('/')
        };
    }

    protected function publishToPlatforms($content, string $message, ?string $imagePath)
    {
        $platforms = config('social-media.platforms');

        // Telegram
        if ($platforms['telegram']['enabled'] && $platforms['telegram']['auto_publish']) {
            $channelId = $platforms['telegram']['channel_id'];
            $includeImage = $platforms['telegram']['include_image'];
            
            PostToTelegram::dispatch(
                $message,
                $channelId,
                $includeImage ? $imagePath : null
            );
            
            Log::info('Dispatched to Telegram', ['channel_id' => $channelId]);
        }

        // Twitter
        if ($platforms['twitter']['enabled'] && $platforms['twitter']['auto_publish']) {
            PostToTwitter::dispatch($message, $imagePath);
            Log::info('Dispatched to Twitter');
        }

        // Facebook
        if ($platforms['facebook']['enabled'] && $platforms['facebook']['auto_publish']) {
            PostToFacebook::dispatch($content);
            Log::info('Dispatched to Facebook');
        }
    }
}
