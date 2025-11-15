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
use App\Models\NewspaperIssue;

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
            'newspaper_issue' => NewspaperIssue::find($this->contentId),
            default => null
        };
    }

    protected function prepareContent($content): array
    {
        // بناء الرسالة
        if ($this->contentType === 'newspaper_issue') {
            // رسالة خاصة لإصدارات الصحف
            $message = "📰 {$content->newspaper_name} - العدد رقم {$content->issue_number}\n\n";
            if ($content->description) {
                $message .= "{$content->description}\n\n";
            }
            if ($content->pdf_url) {
                $message .= "📖 لتصفح العدد: {$content->pdf_url}";
            }
        } else {
            $url = $this->getContentUrl($content);
            $template = config('social-media.templates.' . $this->contentType);
            
            $message = str_replace(
                ['{title}', '{link}'],
                [$content->title, $url],
                $template
            );
        }

        // تحديد الصورة
        $imagePath = null;
        if ($this->contentType === 'article' && $content->image) {
            $imagePath = Storage::disk('public')->path($content->image);
        } elseif ($this->contentType === 'video') {
            // للفيديو: أولاً جرب المسار المحلي، ثم استخدم thumbnail_url من YouTube/Vimeo
            if ($content->thumbnail) {
                $imagePath = Storage::disk('public')->path($content->thumbnail);
            } else {
                // استخدم URL مباشر من YouTube/Vimeo
                $imagePath = $content->thumbnail_url;
            }
        } elseif ($this->contentType === 'opinion' && $content->image) {
            $imagePath = Storage::disk('public')->path($content->image);
        } elseif ($this->contentType === 'newspaper_issue' && $content->cover_image) {
            $imagePath = Storage::disk('public')->path($content->cover_image);
        }

        return [$message, $imagePath];
    }

    protected function getContentUrl($content): string
    {
        return match($this->contentType) {
            'article' => url('/news/' . $content->slug),
            'video' => url('/videos/' . $content->slug),
            'opinion' => url('/opinions/' . $content->slug),
            'newspaper_issue' => url('/newspaper-issues/' . $content->slug),
            default => url('/')
        };
    }

    protected function publishToPlatforms($content, string $message, ?string $imagePath)
    {
        $platforms = config('social-media.platforms');
        $contentUrl = $this->getContentUrl($content);

        // Telegram
        if ($platforms['telegram']['enabled'] && $platforms['telegram']['auto_publish']) {
            $channelId = $platforms['telegram']['channel_id'];
            $includeImage = $platforms['telegram']['include_image'];
            
            PostToTelegram::dispatch(
                $message,
                $channelId,
                $includeImage ? $imagePath : null
            );
            
            Log::info('Dispatched to Telegram', [
                'channel_id' => $channelId,
                'has_image' => !is_null($imagePath)
            ]);
        }

        // Twitter
        if ($platforms['twitter']['enabled'] && $platforms['twitter']['auto_publish']) {
            $includeImage = $platforms['twitter']['include_image'] ?? true;
            
            PostToTwitter::dispatch(
                $message,
                $includeImage ? $imagePath : null
            );
            
            Log::info('Dispatched to Twitter', [
                'has_image' => !is_null($imagePath)
            ]);
        }

        // Facebook
        if ($platforms['facebook']['enabled'] && $platforms['facebook']['auto_publish']) {
            $includeImage = $platforms['facebook']['include_image'] ?? true;
            
            PostToFacebook::dispatch(
                $message,
                $contentUrl,
                $includeImage ? $imagePath : null
            );
            
            Log::info('Dispatched to Facebook', [
                'has_link' => !is_null($contentUrl),
                'has_image' => !is_null($imagePath)
            ]);
        }
    }
}
