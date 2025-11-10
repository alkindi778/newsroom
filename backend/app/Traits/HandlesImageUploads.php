<?php

namespace App\Traits;

use App\Services\ImageOptimizationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesImageUploads
{
    /**
     * رفع صورة وتحويلها إلى WebP تلقائياً
     * 
     * @param UploadedFile $file الملف المرفوع
     * @param string $directory المجلد (افتراضي: images)
     * @param int $quality جودة الصورة (افتراضي: 85)
     * @param int|null $maxWidth الحد الأقصى للعرض
     * @param int|null $maxHeight الحد الأقصى للارتفاع
     * @return string|null مسار الملف أو null في حالة الفشل
     */
    protected function uploadImage(
        UploadedFile $file,
        string $directory = 'images',
        int $quality = 85,
        ?int $maxWidth = 1920,
        ?int $maxHeight = 1080
    ): ?string {
        try {
            $imageService = app(ImageOptimizationService::class);

            // التحقق من أن الملف صورة
            if (!$this->isImage($file)) {
                Log::warning('الملف المرفوع ليس صورة');
                return null;
            }

            // رفع وتحويل الصورة إلى WebP
            $path = $imageService->convertUploadedFile($file, $directory, $quality);

            if (!$path) {
                return null;
            }

            // تصغير الصورة إذا كانت كبيرة جداً
            if ($maxWidth && $maxHeight) {
                $fullPath = storage_path('app/public/' . $path);
                $imageService->resize($fullPath, $maxWidth, $maxHeight);
            }

            return $path;

        } catch (\Exception $e) {
            Log::error('خطأ في رفع الصورة: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * رفع عدة صور دفعة واحدة
     * 
     * @param array $files مصفوفة من الملفات
     * @param string $directory
     * @param int $quality
     * @return array مصفوفة من مسارات الملفات
     */
    protected function uploadMultipleImages(
        array $files,
        string $directory = 'images',
        int $quality = 85
    ): array {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $this->uploadImage($file, $directory, $quality);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    /**
     * حذف صورة من التخزين
     * 
     * @param string|null $path
     * @return bool
     */
    protected function deleteImage(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            // حذف ملف WebP
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            // حذف الصورة الأصلية إذا كانت موجودة
            $originalPath = str_replace('.webp', '', $path);
            $extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            foreach ($extensions as $ext) {
                $testPath = $originalPath . '.' . $ext;
                if (Storage::disk('public')->exists($testPath)) {
                    Storage::disk('public')->delete($testPath);
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('خطأ في حذف الصورة: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * التحقق من أن الملف صورة
     * 
     * @param UploadedFile $file
     * @return bool
     */
    protected function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        
        return in_array($mimeType, $allowedMimes);
    }

    /**
     * الحصول على URL كامل للصورة
     * 
     * @param string|null $path
     * @return string|null
     */
    protected function getImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
