<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Services\ImageOptimizerService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Media Helper لإدارة الصور باستخدام Spatie Media Library
 */
class MediaHelper
{
    // أنواع الصور المختلفة
    const COLLECTION_ARTICLES = 'articles';
    const COLLECTION_WRITERS = 'writers';
    const COLLECTION_OPINIONS = 'opinions';
    const COLLECTION_CATEGORIES = 'categories';
    const COLLECTION_USERS = 'users';
    const COLLECTION_ADVERTISEMENTS = 'advertisements';

    // أحجام الصور
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LARGE = 'large';
    const SIZE_HERO = 'hero';

    /**
     * إضافة صورة لـ model
     *
     * @param HasMedia $model
     * @param UploadedFile $file
     * @param string $collection
     * @param array $customProperties
     * @return Media|null
     */
    public static function addImage(
        HasMedia $model,
        UploadedFile $file,
        string $collection = 'default',
        array $customProperties = []
    ): ?Media {
        try {
            // حذف الصورة القديمة من نفس المجموعة
            self::clearCollection($model, $collection);

            // إضافة الصورة الجديدة
            $media = $model->addMedia($file)
                ->setFileName(self::generateFileName($file, $collection))
                ->withCustomProperties($customProperties)
                ->toMediaCollection($collection);

            Log::info('تم رفع صورة جديدة', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection,
                'file_name' => $media->file_name,
                'file_size' => $media->size
            ]);

            return $media;
        } catch (Exception $e) {
            Log::error('خطأ في رفع الصورة: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection,
                'file_name' => $file->getClientOriginalName()
            ]);

            return null;
        }
    }

    /**
     * إضافة صورة من URL
     *
     * @param HasMedia $model
     * @param string $url
     * @param string $collection
     * @param array $customProperties
     * @return Media|null
     */
    public static function addImageFromUrl(
        HasMedia $model,
        string $url,
        string $collection = 'default',
        array $customProperties = []
    ): ?Media {
        try {
            // حذف الصورة القديمة من نفس المجموعة
            self::clearCollection($model, $collection);

            $media = $model->addMediaFromUrl($url)
                ->withCustomProperties($customProperties)
                ->toMediaCollection($collection);

            Log::info('تم رفع صورة من URL', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection,
                'url' => $url
            ]);

            return $media;
        } catch (Exception $e) {
            Log::error('خطأ في رفع الصورة من URL: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection,
                'url' => $url
            ]);

            return null;
        }
    }

    /**
     * الحصول على رابط الصورة
     *
     * @param HasMedia $model
     * @param string $collection
     * @param string $conversion
     * @return string|null
     */
    public static function getImageUrl(
        HasMedia $model,
        string $collection = 'default',
        string $conversion = ''
    ): ?string {
        try {
            $media = $model->getFirstMedia($collection);
            
            if (!$media) {
                return null;
            }

            return $conversion ? $media->getUrl($conversion) : $media->getUrl();
        } catch (Exception $e) {
            Log::error('خطأ في الحصول على رابط الصورة: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection,
                'conversion' => $conversion
            ]);

            return null;
        }
    }

    /**
     * الحصول على رابط الصورة المصغرة
     *
     * @param HasMedia $model
     * @param string $collection
     * @return string|null
     */
    public static function getThumbnailUrl(HasMedia $model, string $collection = 'default'): ?string
    {
        return self::getImageUrl($model, $collection, self::SIZE_THUMBNAIL);
    }

    /**
     * الحصول على رابط الصورة المتوسطة
     *
     * @param HasMedia $model
     * @param string $collection
     * @return string|null
     */
    public static function getMediumUrl(HasMedia $model, string $collection = 'default'): ?string
    {
        return self::getImageUrl($model, $collection, self::SIZE_MEDIUM);
    }

    /**
     * الحصول على رابط الصورة الكبيرة
     *
     * @param HasMedia $model
     * @param string $collection
     * @return string|null
     */
    public static function getLargeUrl(HasMedia $model, string $collection = 'default'): ?string
    {
        return self::getImageUrl($model, $collection, self::SIZE_LARGE);
    }

    /**
     * حذف صورة معينة
     *
     * @param HasMedia $model
     * @param string $collection
     * @return bool
     */
    public static function deleteImage(HasMedia $model, string $collection = 'default'): bool
    {
        try {
            $media = $model->getFirstMedia($collection);
            
            if ($media) {
                $media->delete();
                
                Log::info('تم حذف الصورة', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'collection' => $collection,
                    'media_id' => $media->id
                ]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('خطأ في حذف الصورة: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection
            ]);

            return false;
        }
    }

    /**
     * حذف جميع الصور من مجموعة معينة
     *
     * @param HasMedia $model
     * @param string $collection
     * @return bool
     */
    public static function clearCollection(HasMedia $model, string $collection = 'default'): bool
    {
        try {
            $model->clearMediaCollection($collection);
            
            Log::info('تم حذف جميع الصور من المجموعة', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('خطأ في حذف المجموعة: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection
            ]);

            return false;
        }
    }

    /**
     * التحقق من وجود صورة
     *
     * @param HasMedia $model
     * @param string $collection
     * @return bool
     */
    public static function hasImage(HasMedia $model, string $collection = 'default'): bool
    {
        return $model->getFirstMedia($collection) !== null;
    }

    /**
     * الحصول على معلومات الصورة
     *
     * @param HasMedia $model
     * @param string $collection
     * @return array|null
     */
    public static function getImageInfo(HasMedia $model, string $collection = 'default'): ?array
    {
        $media = $model->getFirstMedia($collection);
        
        if (!$media) {
            return null;
        }

        return [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'human_readable_size' => $media->humanReadableSize,
            'url' => $media->getUrl(),
            'thumbnail_url' => $media->hasGeneratedConversion(self::SIZE_THUMBNAIL) 
                ? $media->getUrl(self::SIZE_THUMBNAIL) : null,
            'medium_url' => $media->hasGeneratedConversion(self::SIZE_MEDIUM) 
                ? $media->getUrl(self::SIZE_MEDIUM) : null,
            'large_url' => $media->hasGeneratedConversion(self::SIZE_LARGE) 
                ? $media->getUrl(self::SIZE_LARGE) : null,
            'created_at' => $media->created_at,
            'custom_properties' => $media->custom_properties
        ];
    }

    /**
     * توليد اسم ملف فريد
     *
     * @param UploadedFile $file
     * @param string $collection
     * @return string
     */
    private static function generateFileName(UploadedFile $file, string $collection): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = substr(md5(uniqid()), 0, 8);
        $extension = $file->getClientOriginalExtension();
        
        return "{$collection}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * الحصول على جميع الصور من مجموعة معينة
     *
     * @param HasMedia $model
     * @param string $collection
     * @return \Illuminate\Support\Collection
     */
    public static function getAllImages(HasMedia $model, string $collection = 'default')
    {
        return $model->getMedia($collection);
    }

    /**
     * نسخ الصور من model إلى آخر
     *
     * @param HasMedia $fromModel
     * @param HasMedia $toModel
     * @param string $fromCollection
     * @param string $toCollection
     * @return bool
     */
    public static function copyImages(
        HasMedia $fromModel,
        HasMedia $toModel,
        string $fromCollection = 'default',
        string $toCollection = 'default'
    ): bool {
        try {
            $media = $fromModel->getMedia($fromCollection);
            
            foreach ($media as $item) {
                $toModel->copyMedia($item->getPath())
                    ->toMediaCollection($toCollection);
            }

            Log::info('تم نسخ الصور بنجاح', [
                'from_model' => get_class($fromModel),
                'from_id' => $fromModel->id,
                'to_model' => get_class($toModel),
                'to_id' => $toModel->id,
                'count' => $media->count()
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('خطأ في نسخ الصور: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ضغط صورة معينة يدوياً
     *
     * @param HasMedia $model
     * @param string $collection
     * @return bool
     */
    public static function optimizeImage(HasMedia $model, string $collection = 'default'): bool
    {
        try {
            $media = $model->getFirstMedia($collection);
            
            if (!$media) {
                Log::warning('لا توجد صورة للضغط', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'collection' => $collection
                ]);
                return false;
            }

            $optimizer = app(ImageOptimizerService::class);
            $result = $optimizer->optimizeImage($media->getPath());

            if ($result) {
                // تحديث حجم الملف في قاعدة البيانات
                $media->size = filesize($media->getPath());
                $media->save();
            }

            return $result;
        } catch (Exception $e) {
            Log::error('خطأ في ضغط الصورة: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection
            ]);
            return false;
        }
    }

    /**
     * ضغط جميع صور model
     *
     * @param HasMedia $model
     * @return array نتائج الضغط
     */
    public static function optimizeAllImages(HasMedia $model): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'collections' => []
        ];

        try {
            $allMedia = $model->getMedia();
            
            if ($allMedia->isEmpty()) {
                return $results;
            }

            $optimizer = app(ImageOptimizerService::class);

            foreach ($allMedia as $media) {
                $collection = $media->collection_name;
                
                if ($optimizer->optimizeImage($media->getPath())) {
                    $results['success']++;
                    $results['collections'][$collection] = 'success';
                    
                    // تحديث حجم الملف
                    $media->size = filesize($media->getPath());
                    $media->save();
                } else {
                    $results['failed']++;
                    $results['collections'][$collection] = 'failed';
                }
            }

            Log::info('تم ضغط صور الـ Model', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'results' => $results
            ]);

            return $results;
        } catch (Exception $e) {
            Log::error('خطأ في ضغط صور الـ Model: ' . $e->getMessage());
            return $results;
        }
    }
}
