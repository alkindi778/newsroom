<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Image Optimizer Service
 * يقوم بضغط الصور تلقائياً بحد أقصى 200 كيلوبايت
 */
class ImageOptimizerService
{
    // الحد الأقصى لحجم الصورة بالبايت (200 كيلوبايت)
    const MAX_FILE_SIZE = 200 * 1024; // 200 KB
    
    // جودة الضغط الابتدائية
    const INITIAL_QUALITY = 85;
    
    // الحد الأدنى للجودة
    const MIN_QUALITY = 40;
    
    // خطوة تقليل الجودة
    const QUALITY_STEP = 5;
    
    // الحد الأقصى للعرض بالبكسل (لتقليل حجم الصور الكبيرة جداً)
    const MAX_WIDTH = 1920;
    const MAX_HEIGHT = 1080;

    /**
     * ضغط صورة لتصبح أقل من 200 كيلوبايت
     *
     * @param string $imagePath المسار الكامل للصورة
     * @return bool نجاح أو فشل العملية
     */
    public function optimizeImage(string $imagePath): bool
    {
        try {
            if (!file_exists($imagePath)) {
                Log::warning('الصورة غير موجودة: ' . $imagePath);
                return false;
            }

            $originalSize = filesize($imagePath);
            Log::info('بدء ضغط الصورة', [
                'path' => basename($imagePath),
                'original_size' => $this->formatBytes($originalSize)
            ]);

            // إذا كانت الصورة أصغر من 200 كيلوبايت، لا حاجة للضغط
            if ($originalSize <= self::MAX_FILE_SIZE) {
                Log::info('الصورة بالفعل ضمن الحد المسموح', [
                    'size' => $this->formatBytes($originalSize)
                ]);
                return true;
            }

            // تحميل الصورة
            $image = Image::read($imagePath);
            
            // تقليل الأبعاد إذا كانت الصورة كبيرة جداً
            $this->resizeIfNeeded($image);

            // محاولة الضغط بجودة متدرجة
            $compressed = $this->compressWithQuality($image, $imagePath);

            if ($compressed) {
                $newSize = filesize($imagePath);
                Log::info('تم ضغط الصورة بنجاح', [
                    'path' => basename($imagePath),
                    'original_size' => $this->formatBytes($originalSize),
                    'new_size' => $this->formatBytes($newSize),
                    'saved' => $this->formatBytes($originalSize - $newSize),
                    'reduction' => round((($originalSize - $newSize) / $originalSize) * 100, 2) . '%'
                ]);
                return true;
            }

            Log::warning('فشل ضغط الصورة إلى الحد المطلوب', [
                'path' => basename($imagePath),
                'final_size' => $this->formatBytes(filesize($imagePath))
            ]);
            
            return false;

        } catch (Exception $e) {
            Log::error('خطأ في ضغط الصورة: ' . $e->getMessage(), [
                'path' => $imagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * تقليل حجم الصورة إذا كانت كبيرة جداً
     */
    private function resizeIfNeeded($image): void
    {
        $width = $image->width();
        $height = $image->height();

        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            $image->scale(
                width: $width > self::MAX_WIDTH ? self::MAX_WIDTH : null,
                height: $height > self::MAX_HEIGHT ? self::MAX_HEIGHT : null
            );
            
            Log::info('تم تقليل أبعاد الصورة', [
                'original' => "{$width}x{$height}",
                'new' => "{$image->width()}x{$image->height()}"
            ]);
        }
    }

    /**
     * ضغط الصورة بجودة متدرجة حتى تصل للحجم المطلوب
     */
    private function compressWithQuality($image, string $imagePath): bool
    {
        $quality = self::INITIAL_QUALITY;
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        while ($quality >= self::MIN_QUALITY) {
            // اختيار الـ encoder المناسب حسب نوع الصورة
            $encoder = $this->getEncoder($extension, $quality);
            
            // حفظ الصورة
            $image->save($imagePath, encoder: $encoder);
            
            $currentSize = filesize($imagePath);
            
            Log::debug('محاولة ضغط بجودة ' . $quality, [
                'size' => $this->formatBytes($currentSize),
                'target' => $this->formatBytes(self::MAX_FILE_SIZE)
            ]);

            // إذا وصلنا للحجم المطلوب
            if ($currentSize <= self::MAX_FILE_SIZE) {
                return true;
            }

            // تقليل الجودة للمحاولة التالية
            $quality -= self::QUALITY_STEP;
        }

        // محاولة أخيرة: تحويل إلى WebP (أفضل ضغط)
        if ($extension !== 'webp') {
            Log::info('محاولة التحويل إلى WebP للحصول على ضغط أفضل');
            $webpPath = preg_replace('/\.[^.]+$/', '.webp', $imagePath);
            $image->save($webpPath, encoder: new WebpEncoder(quality: 80));
            
            if (filesize($webpPath) <= self::MAX_FILE_SIZE) {
                // حذف الصورة القديمة واستبدالها
                unlink($imagePath);
                rename($webpPath, $imagePath);
                return true;
            }
            
            // حذف نسخة WebP إذا لم تنجح
            if (file_exists($webpPath)) {
                unlink($webpPath);
            }
        }

        return false;
    }

    /**
     * اختيار الـ encoder المناسب حسب نوع الصورة
     */
    private function getEncoder(string $extension, int $quality)
    {
        return match($extension) {
            'jpg', 'jpeg' => new JpegEncoder(quality: $quality),
            'webp' => new WebpEncoder(quality: $quality),
            'png' => new AutoEncoder(quality: $quality),
            default => new AutoEncoder(quality: $quality)
        };
    }

    /**
     * تنسيق حجم الملف بشكل قابل للقراءة
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * ضغط مجموعة من الصور دفعة واحدة
     *
     * @param array $imagePaths مصفوفة من مسارات الصور
     * @return array نتائج الضغط
     */
    public function optimizeBatch(array $imagePaths): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'details' => []
        ];

        foreach ($imagePaths as $path) {
            if (!file_exists($path)) {
                $results['skipped']++;
                $results['details'][$path] = 'File not found';
                continue;
            }

            $originalSize = filesize($path);
            
            if ($this->optimizeImage($path)) {
                $results['success']++;
                $results['details'][$path] = [
                    'status' => 'success',
                    'original_size' => $this->formatBytes($originalSize),
                    'new_size' => $this->formatBytes(filesize($path))
                ];
            } else {
                $results['failed']++;
                $results['details'][$path] = [
                    'status' => 'failed',
                    'size' => $this->formatBytes(filesize($path))
                ];
            }
        }

        return $results;
    }
}
