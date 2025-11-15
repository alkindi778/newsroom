<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PostToFacebook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    protected $message;
    protected $link;
    protected $imagePath;

    public function __construct(string $message, ?string $link = null, ?string $imagePath = null)
    {
        $this->message = $message;
        $this->link = $link;
        $this->imagePath = $imagePath;
    }

    public function handle()
    {
        try {
            // التحقق من إعدادات Facebook
            $pageId = config('social-media.platforms.facebook.page_id');
            $accessToken = config('social-media.platforms.facebook.access_token');
            
            if (!$pageId || !$accessToken) {
                Log::warning('Facebook API credentials not configured - skipping post');
                return true; // لا نفشل الـ job
            }

            // التحقق من نوع المسار (URL أو مسار محلي)
            $isUrl = $this->imagePath ? filter_var($this->imagePath, FILTER_VALIDATE_URL) : false;
            
            // Log للتحقق
            Log::info('Facebook posting started', [
                'message' => substr($this->message, 0, 100) . '...',
                'has_link' => !is_null($this->link),
                'has_image' => !is_null($this->imagePath),
                'image_path' => $this->imagePath,
                'is_url' => $isUrl
            ]);

            // TODO: Implement Facebook Graph API integration
            // يمكن استخدام مكتبة facebook/graph-sdk
            // composer require facebook/graph-sdk
            
            /*
            use Facebook\Facebook;
            
            $fb = new Facebook([
                'app_id' => config('social-media.platforms.facebook.app_id'),
                'app_secret' => config('social-media.platforms.facebook.app_secret'),
                'default_graph_version' => 'v18.0',
            ]);

            if ($this->imagePath) {
                // نشر مع صورة
                if ($isUrl) {
                    // استخدام URL الصورة مباشرة
                    $data = [
                        'message' => $this->message,
                        'url' => $this->imagePath,
                    ];
                    if ($this->link) {
                        $data['link'] = $this->link;
                    }
                    $response = $fb->post('/' . $pageId . '/photos', $data, $accessToken);
                } else {
                    // رفع صورة من ملف محلي
                    $data = [
                        'message' => $this->message,
                        'source' => $fb->fileToUpload($this->imagePath),
                    ];
                    if ($this->link) {
                        $data['link'] = $this->link;
                    }
                    $response = $fb->post('/' . $pageId . '/photos', $data, $accessToken);
                }
            } else {
                // نشر نص مع رابط
                $data = [
                    'message' => $this->message,
                ];
                if ($this->link) {
                    $data['link'] = $this->link;
                }
                $response = $fb->post('/' . $pageId . '/feed', $data, $accessToken);
            }
            
            $graphNode = $response->getGraphNode();
            Log::info('Facebook post created successfully', [
                'post_id' => $graphNode['id']
            ]);
            */
            
            Log::info('Facebook post queued (API integration pending)', [
                'message_length' => strlen($this->message),
                'has_link' => !is_null($this->link),
                'has_image' => !is_null($this->imagePath)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error posting to Facebook: ' . $e->getMessage(), [
                'attempt' => $this->attempts(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
            }
            
            return false;
        }
    }
}
