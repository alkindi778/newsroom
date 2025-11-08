<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Services\ImageOptimizerService;
use Illuminate\Support\Facades\Log;

class OptimizeMediaImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $media;
    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageOptimizerService $optimizer): void
    {
        // التحقق من أن الملف هو صورة
        if (!str_starts_with($this->media->mime_type, 'image/')) {
            return;
        }

        $imagePath = $this->media->getPath();

        // التحقق من وجود الملف
        if (!file_exists($imagePath)) {
            Log::warning('تخطي ضغط الصورة - الملف غير موجود', [
                'file_name' => $this->media->file_name,
                'path' => $imagePath,
                'attempt' => $this->attempts()
            ]);
            
            // إعادة المحاولة إذا لم يكن هذا آخر محاولة
            if ($this->attempts() < $this->tries) {
                $this->release(2); // إعادة المحاولة بعد ثانيتين
            }
            return;
        }

        $originalSize = filesize($imagePath);

        Log::info('بدء ضغط الصورة المرفوعة (Job)', [
            'file_name' => $this->media->file_name,
            'original_size' => $originalSize,
            'mime_type' => $this->media->mime_type,
            'collection' => $this->media->collection_name,
            'path' => $imagePath
        ]);

        // ضغط الصورة
        if ($optimizer->optimizeImage($imagePath)) {
            // تحديث حجم الملف في قاعدة البيانات
            $newSize = filesize($imagePath);
            $saved = $originalSize - $newSize;
            $reduction = $originalSize > 0 ? round((($originalSize - $newSize) / $originalSize) * 100, 2) : 0;

            $this->media->size = $newSize;
            $this->media->saveQuietly();

            Log::info('✅ تم ضغط الصورة بنجاح (Job)', [
                'file_name' => $this->media->file_name,
                'original_size' => $originalSize,
                'new_size' => $newSize,
                'saved' => $saved,
                'reduction' => $reduction . '%'
            ]);
        } else {
            Log::warning('فشل ضغط الصورة (Job)', [
                'file_name' => $this->media->file_name,
                'size' => $originalSize
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('فشل Job ضغط الصورة', [
            'media_id' => $this->media->id,
            'file_name' => $this->media->file_name,
            'error' => $exception->getMessage()
        ]);
    }
}
