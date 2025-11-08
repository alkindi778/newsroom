<?php

namespace App\Listeners;

use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded;
use App\Services\ImageOptimizerService;
use Illuminate\Support\Facades\Log;

/**
 * Event Listener لضغط الصور تلقائياً بعد رفعها
 */
class OptimizeUploadedImage
{
    protected ImageOptimizerService $optimizer;

    public function __construct(ImageOptimizerService $optimizer)
    {
        $this->optimizer = $optimizer;
    }

    /**
     * Handle the event.
     */
    public function handle(MediaHasBeenAdded $event): void
    {
        $media = $event->media;
        
        // التحقق من أن الملف هو صورة
        if (!$this->isImage($media->mime_type)) {
            return;
        }

        Log::info('بدء ضغط الصورة المرفوعة', [
            'file_name' => $media->file_name,
            'original_size' => $media->size,
            'mime_type' => $media->mime_type,
            'collection' => $media->collection_name
        ]);

        // الحصول على المسار الكامل للصورة
        $imagePath = $media->getPath();

        // ضغط الصورة
        $this->optimizer->optimizeImage($imagePath);

        // تحديث حجم الملف في قاعدة البيانات
        $newSize = filesize($imagePath);
        $media->size = $newSize;
        $media->save();

        Log::info('تم تحديث معلومات الصورة في قاعدة البيانات', [
            'file_name' => $media->file_name,
            'new_size' => $newSize
        ]);
    }

    /**
     * التحقق من أن الملف هو صورة
     */
    private function isImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }
}
