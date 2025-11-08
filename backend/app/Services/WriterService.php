<?php

namespace App\Services;

use App\Repositories\Interfaces\WriterRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Helpers\MediaHelper;

class WriterService
{
    protected $writerRepository;

    public function __construct(WriterRepositoryInterface $writerRepository)
    {
        $this->writerRepository = $writerRepository;
    }

    /**
     * الحصول على جميع الكُتاب مع الفلاتر
     */
    public function getAllWithFilters($search = null, $status = null, $sortBy = 'created_at', $sortDirection = 'desc', $perPage = 10)
    {
        try {
            return $this->writerRepository->getAllWithFilters($search, $status, $sortBy, $sortDirection, $perPage);
        } catch (Exception $e) {
            Log::error('خطأ في جلب الكُتاب: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * الحصول على كاتب بالمعرف
     */
    public function getById($id)
    {
        try {
            return $this->writerRepository->getById($id);
        } catch (Exception $e) {
            Log::error('خطأ في جلب الكاتب: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * الحصول على كاتب بالـ slug
     */
    public function getBySlug($slug)
    {
        try {
            return $this->writerRepository->getBySlug($slug);
        } catch (Exception $e) {
            Log::error('خطأ في جلب الكاتب بالـ slug: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * إنشاء كاتب جديد
     */
    public function createWriter(array $data)
    {
        try {
            // معالجة رفع الصورة - سيتم التعامل معها بعد إنشاء الكاتب
            $imageFile = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imageFile = $data['image'];
                unset($data['image']); // إزالة من البيانات
            }

            // التأكد من وجود الحقول المطلوبة
            $data['is_active'] = $data['is_active'] ?? true;
            $data['opinions_count'] = 0;

            $writer = $this->writerRepository->create($data);

            // رفع الصورة باستخدام Media Library
            if ($imageFile) {
                MediaHelper::addImage(
                    $writer,
                    $imageFile,
                    MediaHelper::COLLECTION_WRITERS,
                    [
                        'alt' => $writer->name,
                        'title' => $writer->name
                    ]
                );
            }

            Log::info('تم إنشاء كاتب جديد', [
                'writer_id' => $writer->id,
                'writer_name' => $writer->name,
                'user_id' => auth()->id()
            ]);

            return $writer;
        } catch (Exception $e) {
            Log::error('خطأ في إنشاء الكاتب: ' . $e->getMessage(), [
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * تحديث كاتب
     */
    public function updateWriter($id, array $data)
    {
        try {
            $writer = $this->writerRepository->getById($id);
            
            if (!$writer) {
                throw new Exception('الكاتب غير موجود');
            }

            // معالجة رفع الصورة باستخدام Media Library
            $imageFile = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imageFile = $data['image'];
                unset($data['image']); // إزالة من البيانات
            }

            $success = $this->writerRepository->update($id, $data);

            if ($success && $imageFile) {
                // رفع الصورة الجديدة باستخدام Media Library
                MediaHelper::addImage(
                    $writer,
                    $imageFile,
                    MediaHelper::COLLECTION_WRITERS,
                    [
                        'alt' => $writer->name,
                        'title' => $writer->name
                    ]
                );
            }

            if ($success) {
                Log::info('تم تحديث الكاتب', [
                    'writer_id' => $id,
                    'writer_name' => $data['name'] ?? $writer->name,
                    'user_id' => auth()->id()
                ]);
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في تحديث الكاتب: ' . $e->getMessage(), [
                'writer_id' => $id,
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * حذف كاتب
     */
    public function deleteWriter($id)
    {
        try {
            $writer = $this->writerRepository->getById($id);
            
            if (!$writer) {
                throw new Exception('الكاتب غير موجود');
            }

            // التحقق من وجود مقالات
            if ($writer->opinions_count > 0) {
                throw new Exception('لا يمكن حذف الكاتب لوجود مقالات مرتبطة به');
            }

            // حذف الصور باستخدام Media Library
            MediaHelper::clearCollection($writer, MediaHelper::COLLECTION_WRITERS);
            
            // حذف الصورة القديمة إن وجدت (backward compatibility)
            if ($writer->image) {
                $this->deleteImage($writer->image);
            }

            $success = $this->writerRepository->delete($id);

            if ($success) {
                Log::info('تم حذف الكاتب', [
                    'writer_id' => $id,
                    'writer_name' => $writer->name,
                    'user_id' => auth()->id()
                ]);
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في حذف الكاتب: ' . $e->getMessage(), [
                'writer_id' => $id,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * تغيير حالة الكاتب
     */
    public function toggleStatus($id)
    {
        try {
            $writer = $this->writerRepository->getById($id);
            
            if (!$writer) {
                throw new Exception('الكاتب غير موجود');
            }

            $success = $this->writerRepository->toggleStatus($id);

            if ($success) {
                $newStatus = !$writer->is_active ? 'تفعيل' : 'إلغاء تفعيل';
                
                Log::info("تم {$newStatus} الكاتب", [
                    'writer_id' => $id,
                    'writer_name' => $writer->name,
                    'new_status' => !$writer->is_active,
                    'user_id' => auth()->id()
                ]);
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في تغيير حالة الكاتب: ' . $e->getMessage(), [
                'writer_id' => $id,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * العمليات المجمعة
     */
    public function bulkAction($action, array $writerIds)
    {
        try {
            $results = [];

            foreach ($writerIds as $writerId) {
                switch ($action) {
                    case 'activate':
                        $writer = $this->writerRepository->getById($writerId);
                        if ($writer && !$writer->is_active) {
                            $results[$writerId] = $this->writerRepository->update($writerId, ['is_active' => true]);
                        }
                        break;
                        
                    case 'deactivate':
                        $writer = $this->writerRepository->getById($writerId);
                        if ($writer && $writer->is_active) {
                            $results[$writerId] = $this->writerRepository->update($writerId, ['is_active' => false]);
                        }
                        break;
                        
                    case 'delete':
                        $results[$writerId] = $this->deleteWriter($writerId);
                        break;
                        
                    default:
                        throw new Exception('عملية غير مدعومة');
                }
            }

            Log::info('تم تنفيذ عملية مجمعة على الكُتاب', [
                'action' => $action,
                'writer_ids' => $writerIds,
                'results' => $results,
                'user_id' => auth()->id()
            ]);

            return $results;
        } catch (Exception $e) {
            Log::error('خطأ في العملية المجمعة: ' . $e->getMessage(), [
                'action' => $action,
                'writer_ids' => $writerIds,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * الحصول على الكُتاب النشطين
     */
    public function getActiveWriters()
    {
        try {
            return $this->writerRepository->getActive();
        } catch (Exception $e) {
            Log::error('خطأ في جلب الكُتاب النشطين: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * الحصول على أفضل الكُتاب
     */
    public function getTopWriters($limit = 10)
    {
        try {
            return $this->writerRepository->getTopWriters($limit);
        } catch (Exception $e) {
            Log::error('خطأ في جلب أفضل الكُتاب: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * الحصول على إحصائيات الكُتاب
     */
    public function getStatistics()
    {
        try {
            return $this->writerRepository->getStatistics();
        } catch (Exception $e) {
            Log::error('خطأ في جلب إحصائيات الكُتاب: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * البحث في الكُتاب
     */
    public function searchWriters($query)
    {
        try {
            return $this->writerRepository->search($query);
        } catch (Exception $e) {
            Log::error('خطأ في البحث في الكُتاب: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * رفع صورة
     */
    protected function uploadImage(UploadedFile $file): string
    {
        try {
            $path = $file->store('writers', 'public');
            
            Log::info('تم رفع صورة كاتب', [
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize()
            ]);

            return $path;
        } catch (Exception $e) {
            Log::error('خطأ في رفع صورة الكاتب: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * حذف صورة
     */
    protected function deleteImage(string $path): void
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                
                Log::info('تم حذف صورة كاتب', ['path' => $path]);
            }
        } catch (Exception $e) {
            Log::warning('خطأ في حذف صورة الكاتب: ' . $e->getMessage(), ['path' => $path]);
        }
    }

    /**
     * تحديث عدد مقالات الكاتب
     */
    public function updateOpinionsCount($writerId)
    {
        try {
            return $this->writerRepository->updateOpinionsCount($writerId);
        } catch (Exception $e) {
            Log::error('خطأ في تحديث عدد المقالات: ' . $e->getMessage(), [
                'writer_id' => $writerId
            ]);
            return false;
        }
    }
}
