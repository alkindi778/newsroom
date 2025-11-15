<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PostToTwitter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    protected $message;
    protected $imagePath;

    public function __construct(string $message, ?string $imagePath = null)
    {
        $this->message = $message;
        $this->imagePath = $imagePath;
    }

    public function handle()
    {
        try {
            // التحقق من إعدادات Twitter
            $apiKey = config('social-media.platforms.twitter.api_key');
            $apiSecret = config('social-media.platforms.twitter.api_secret');
            $accessToken = config('social-media.platforms.twitter.access_token');
            $accessTokenSecret = config('social-media.platforms.twitter.access_token_secret');
            
            if (!$apiKey || !$apiSecret || !$accessToken || !$accessTokenSecret) {
                Log::warning('Twitter API credentials not configured - skipping post');
                return true; // لا نفشل الـ job
            }

            // التحقق من نوع المسار (URL أو مسار محلي)
            $isUrl = $this->imagePath ? filter_var($this->imagePath, FILTER_VALIDATE_URL) : false;
            
            // Log للتحقق
            Log::info('Twitter posting started', [
                'message' => substr($this->message, 0, 100) . '...',
                'has_image' => !is_null($this->imagePath),
                'image_path' => $this->imagePath,
                'is_url' => $isUrl
            ]);

            // TODO: Implement Twitter API v2 integration
            // يمكن استخدام مكتبة noweh/twitter-api-v2-php
            // composer require noweh/twitter-api-v2-php
            
            /*
            use Noweh\TwitterApi\Client;
            
            $client = new Client([
                'account_id' => config('social-media.platforms.twitter.account_id'),
                'access_token' => $accessToken,
                'access_token_secret' => $accessTokenSecret,
                'consumer_key' => $apiKey,
                'consumer_secret' => $apiSecret,
                'bearer_token' => config('social-media.platforms.twitter.bearer_token'),
            ]);

            if ($this->imagePath) {
                // رفع الصورة أولاً
                if ($isUrl) {
                    // تحميل الصورة من URL
                    $imageContent = file_get_contents($this->imagePath);
                    $mediaResponse = $client->uploadMedia()->upload($imageContent);
                } else {
                    // رفع من ملف محلي
                    $mediaResponse = $client->uploadMedia()->upload($this->imagePath);
                }
                
                $mediaId = $mediaResponse['media_id_string'];
                
                // نشر التغريدة مع الصورة
                $response = $client->tweet()->create()->performRequest([
                    'text' => $this->message,
                    'media' => ['media_ids' => [$mediaId]]
                ]);
            } else {
                // نشر نص فقط
                $response = $client->tweet()->create()->performRequest([
                    'text' => $this->message
                ]);
            }
            */
            
            Log::info('Twitter post queued (API integration pending)', [
                'message_length' => strlen($this->message),
                'has_image' => !is_null($this->imagePath)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error posting to Twitter: ' . $e->getMessage(), [
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
