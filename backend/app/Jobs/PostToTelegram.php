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

            // إذا كانت هناك صورة
            if ($this->imagePath && file_exists($this->imagePath)) {
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
