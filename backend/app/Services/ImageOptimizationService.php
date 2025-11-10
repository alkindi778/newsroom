<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageOptimizationService
{
    protected $manager;
    protected $supportedFormats = ['jpg', 'jpeg', 'png', 'gif'];

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * تحويل صورة إلى WebP
     * 
     * @param string $sourcePath مسار الملف الأصلي
     * @param int $quality جودة الصورة (1-100)
     * @return string|null مسار ملف WebP أو null في حالة الفشل
     */
    public function convertToWebP(string $sourcePath, int $quality = 85): ?string
    {
        try {
            // التحقق من وجود الملف
            if (!file_exists($sourcePath)) {
                Log::warning("الملف غير موجود: {$sourcePath}");
                return null;
            }

            // الحصول على معلومات الملف
            $pathInfo = pathinfo($sourcePath);
            $extension = strtolower($pathInfo['extension'] ?? '');

            // التحقق من أن الصورة بصيغة مدعومة
            if (!in_array($extension, $this->supportedFormats)) {
                return null;
            }

            // إنشاء مسار ملف WebP
            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

            // إذا كان ملف WebP موجود بالفعل، تخطي
            if (file_exists($webpPath)) {
                return $webpPath;
            }

            // تحميل الصورة وتحويلها
            $image = $this->manager->read($sourcePath);
            $image->toWebp($quality)->save($webpPath);

            Log::info("تم تحويل الصورة إلى WebP: {$webpPath}");

            return $webpPath;

        } catch (\Exception $e) {
            Log::error("خطأ في تحويل الصورة إلى WebP: " . $e->getMessage());
            return null;
        }
    }

    /**
     * تحويل صورة محملة إلى WebP
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory المجلد الذي سيتم الحفظ فيه
     * @param int $quality
     * @return string|null المسار النسبي للملف
     */
    public function convertUploadedFile($file, string $directory = 'images', int $quality = 85): ?string
    {
        try {
            // إنشاء اسم فريد للملف
            $filename = uniqid() . '_' . time() . '.webp';
            $path = $directory . '/' . $filename;
            
            // تحميل الصورة
            $image = $this->manager->read($file->getPathname());
            
            // حفظ بصيغة WebP
            $fullPath = storage_path('app/public/' . $path);
            
            // التأكد من وجود المجلد
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $image->toWebp($quality)->save($fullPath);

            Log::info("تم رفع وتحويل الصورة: {$path}");

            return $path;

        } catch (\Exception $e) {
            Log::error("خطأ في معالجة الصورة المرفوعة: " . $e->getMessage());
            return null;
        }
    }

    /**
     * تحويل جميع الصور في مجلد محدد
     * 
     * @param string $directory
     * @param int $quality
     * @return array إحصائيات التحويل
     */
    public function convertDirectoryImages(string $directory, int $quality = 85): array
    {
        $stats = [
            'total' => 0,
            'converted' => 0,
            'skipped' => 0,
            'failed' => 0
        ];

        if (!is_dir($directory)) {
            Log::warning("المجلد غير موجود: {$directory}");
            return $stats;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $extension = strtolower($file->getExtension());
            
            if (!in_array($extension, $this->supportedFormats)) {
                continue;
            }

            $stats['total']++;

            $webpPath = $this->convertToWebP($file->getPathname(), $quality);
            
            if ($webpPath === null) {
                $stats['failed']++;
            } elseif ($webpPath === $file->getPathname()) {
                $stats['skipped']++;
            } else {
                $stats['converted']++;
            }
        }

        return $stats;
    }

    /**
     * تحسين حجم الصورة مع الحفاظ على النسب
     * 
     * @param string $sourcePath
     * @param int $maxWidth العرض الأقصى
     * @param int $maxHeight الارتفاع الأقصى
     * @return bool
     */
    public function resize(string $sourcePath, int $maxWidth = 1920, int $maxHeight = 1080): bool
    {
        try {
            $image = $this->manager->read($sourcePath);
            
            // الحصول على الأبعاد الحالية
            $width = $image->width();
            $height = $image->height();

            // إذا كانت الصورة أصغر من الحد الأقصى، لا تفعل شيء
            if ($width <= $maxWidth && $height <= $maxHeight) {
                return true;
            }

            // تصغير الصورة مع الحفاظ على النسب
            $image->scale(width: $maxWidth, height: $maxHeight);
            $image->save($sourcePath);

            Log::info("تم تصغير الصورة: {$sourcePath}");

            return true;

        } catch (\Exception $e) {
            Log::error("خطأ في تصغير الصورة: " . $e->getMessage());
            return false;
        }
    }
}
