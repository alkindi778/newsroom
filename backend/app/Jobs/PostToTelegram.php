<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    protected $message;
    protected $channelId;
    protected $imagePath;

    public function __construct(string $message, string $channelId, ?string $imagePath = null)
    {
        $this->message = $message;
        $this->channelId = $channelId;
        $this->imagePath = $imagePath;
    }

    public function handle()
    {
        try {
            $botToken = config('social-media.platforms.telegram.bot_token');
            
            if (!$botToken) {
                Log::error('Telegram bot token not configured');
                return false;
            }

            $payload = [
                'chat_id' => $this->channelId,
                'parse_mode' => 'HTML',
            ];

            // التحقق من نوع المسار (URL أو مسار محلي)
            $isUrl = filter_var($this->imagePath, FILTER_VALIDATE_URL);
            
            // Log للتحقق من مسار الصورة
            if ($this->imagePath) {
                Log::info('Telegram image check', [
                    'image_path' => $this->imagePath,
                    'is_url' => $isUrl,
                    'file_exists' => !$isUrl && file_exists($this->imagePath),
                    'is_readable' => !$isUrl && is_readable($this->imagePath ?? '')
                ]);
            }

            // إذا كانت هناك صورة (URL)
            if ($this->imagePath && $isUrl) {
                $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
                $response = Http::post($url, [
                    'chat_id' => $this->channelId,
                    'photo' => $this->imagePath,
                    'caption' => $this->message,
                    'parse_mode' => 'HTML'
                ]);
                
                if ($response->successful()) {
                    Log::info('Telegram message with photo URL sent successfully', [
                        'channel_id' => $this->channelId,
                        'image_url' => $this->imagePath
                    ]);
                    return true;
                } else {
                    Log::error('Failed to send photo via URL', [
                        'url' => $this->imagePath,
                        'response' => $response->body()
                    ]);
                    throw new \Exception('Telegram API error: ' . $response->body());
                }
            }
            // إذا كانت هناك صورة (ملف محلي)
            elseif ($this->imagePath && file_exists($this->imagePath)) {
                // استخدام المسار المحلي
                $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
                $payload['photo'] = new \CURLFile($this->imagePath);
                $payload['caption'] = $this->message;
                
                // استخدام cURL للصور
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                $result = json_decode($response, true);
                
                if ($httpCode === 200 && isset($result['ok']) && $result['ok']) {
                    Log::info('Telegram message with photo sent successfully', [
                        'channel_id' => $this->channelId
                    ]);
                    return true;
                } else {
                    throw new \Exception('Telegram API error: ' . ($result['description'] ?? 'Unknown error'));
                }
            } elseif ($this->imagePath) {
                // الملف غير موجود محلياً - جرب استخدام URL
                Log::warning('Image file not found locally, trying URL', [
                    'path' => $this->imagePath
                ]);
                
                // تحويل المسار إلى URL
                $relativePath = str_replace(storage_path('app/public/'), '', $this->imagePath);
                $imageUrl = url('storage/' . $relativePath);
                
                $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
                $response = Http::post($url, [
                    'chat_id' => $this->channelId,
                    'photo' => $imageUrl,
                    'caption' => $this->message,
                    'parse_mode' => 'HTML'
                ]);
                
                if ($response->successful()) {
                    Log::info('Telegram message with photo URL sent successfully', [
                        'channel_id' => $this->channelId,
                        'image_url' => $imageUrl
                    ]);
                    return true;
                } else {
                    Log::error('Failed to send photo via URL', [
                        'url' => $imageUrl,
                        'response' => $response->body()
                    ]);
                    // إرسال بدون صورة كخيار أخير
                    $fallbackUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
                    $fallbackResponse = Http::post($fallbackUrl, [
                        'chat_id' => $this->channelId,
                        'text' => $this->message,
                        'parse_mode' => 'HTML'
                    ]);
                    return $fallbackResponse->successful();
                }
            } else {
                // رسالة نصية فقط
                $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
                $payload['text'] = $this->message;
                
                $response = Http::post($url, $payload);
                
                if ($response->successful()) {
                    Log::info('Telegram message sent successfully', [
                        'channel_id' => $this->channelId
                    ]);
                    return true;
                } else {
                    throw new \Exception('Telegram API error: ' . $response->body());
                }
            }
        } catch (\Exception $e) {
            Log::error('Error posting to Telegram: ' . $e->getMessage(), [
                'channel_id' => $this->channelId,
                'attempt' => $this->attempts()
            ]);
            
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
            }
            
            return false;
        }
    }
}
