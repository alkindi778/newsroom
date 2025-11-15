<?php

namespace App\Services;

use App\Repositories\Interfaces\OpinionRepositoryInterface;
use App\Services\WriterService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Helpers\MediaHelper;

class OpinionService
{
    protected $opinionRepository;
    protected $writerService;

    public function __construct(OpinionRepositoryInterface $opinionRepository, WriterService $writerService)
    {
        $this->opinionRepository = $opinionRepository;
        $this->writerService = $writerService;
    }

    /**
     * الحصول على جميع مقالات الرأي مع الفلاتر
     */
    public function getAllWithFilters($search = null, $status = null, $writerId = null, $featured = null, $sortBy = 'created_at', $sortDirection = 'desc', $perPage = 10)
    {
        try {
            return $this->opinionRepository->getAllWithFilters($search, $status, $writerId, $featured, $sortBy, $sortDirection, $perPage);
        } catch (Exception $e) {
            Log::error('Error fetching opinions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * الحصول على مقال بالمعرف
     */
    public function getById($id)
    {
        try {
            return $this->opinionRepository->getById($id);
        } catch (Exception $e) {
            Log::error('خطأ في جلب المقال: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * الحصول على مقال بالـ slug
     */
    public function getBySlug($slug)
    {
        try {
            return $this->opinionRepository->getBySlug($slug);
        } catch (Exception $e) {
            Log::error('خطأ في جلب المقال بالـ slug: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * إنشاء مقال جديد
     */
    public function createOpinion(array $data)
    {
        try {
            // معالجة رفع الصورة - سيتم التعامل معها بعد إنشاء المقال
            $imageFile = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imageFile = $data['image'];
                unset($data['image']); // إزالة من البيانات
            }

            // معالجة الكلمات الدلالية
            if (isset($data['tags']) && is_string($data['tags'])) {
                $data['tags'] = array_map('trim', explode(',', $data['tags']));
            }

            // التحقق من صلاحية النشر
            $canPublish = auth()->user()->can('نشر مقالات الرأي');
            
            if (isset($data['is_published']) && $data['is_published'] && !$canPublish) {
                Log::warning('محاولة نشر مقال بدون صلاحية', [
                    'user_id' => auth()->id(),
                    'writer_id' => $data['writer_id'] ?? null
                ]);
                
                $data['is_published'] = false;
            }

            // تحديد تاريخ النشر
            if ($data['is_published']) {
                $data['published_at'] = now();
            }

            $opinion = $this->opinionRepository->create($data);

            // رفع الصورة باستخدام Media Library
            if ($imageFile) {
                MediaHelper::addImage(
                    $opinion,
                    $imageFile,
                    MediaHelper::COLLECTION_OPINIONS,
                    [
                        'alt' => $opinion->title,
                        'title' => $opinion->title
                    ]
                );
            }

            // تحديث عدد مقالات الكاتب
            if (isset($data['writer_id'])) {
                $this->writerService->updateOpinionsCount($data['writer_id']);
            }

            Log::info('تم إنشاء مقال رأي جديد', [
                'opinion_id' => $opinion->id,
                'opinion_title' => $opinion->title,
                'writer_id' => $opinion->writer_id,
                'is_published' => $opinion->is_published,
                'user_id' => auth()->id()
            ]);
            
            // إطلاق Event لإرسال Push Notifications عند النشر
            if ($opinion->is_published) {
                event(new \App\Events\OpinionPublished($opinion));
                
                // نشر على السوشيال ميديا
                \App\Jobs\ShareToAllPlatforms::dispatch('opinion', $opinion->id);
            }

            return $opinion;
        } catch (Exception $e) {
            Log::error('خطأ في إنشاء المقال: ' . $e->getMessage(), [
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * تحديث مقال
     */
    public function updateOpinion($id, array $data)
    {
        try {
            $opinion = $this->opinionRepository->getById($id);
            
            if (!$opinion) {
                throw new Exception('المقال غير موجود');
            }

            $oldWriterId = $opinion->writer_id;

            // معالجة رفع الصورة الجديدة باستخدام Media Library
            $imageFile = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imageFile = $data['image'];
                unset($data['image']); // إزالة من البيانات
            }

            // معالجة الكلمات الدلالية
            if (isset($data['tags']) && is_string($data['tags'])) {
                $data['tags'] = array_map('trim', explode(',', $data['tags']));
            } elseif (!isset($data['tags'])) {
                $data['tags'] = null;
            }

            // التحقق من صلاحية النشر
            $canPublish = auth()->user()->can('نشر مقالات الرأي');
            
            if (isset($data['is_published']) && $data['is_published'] && !$canPublish) {
                Log::warning('محاولة نشر مقال بدون صلاحية', [
                    'opinion_id' => $id,
                    'user_id' => auth()->id()
                ]);
                
                $data['is_published'] = false;
            }

            // تحديد تاريخ النشر
            if (isset($data['is_published'])) {
                if ($data['is_published'] && !$opinion->published_at) {
                    $data['published_at'] = now();
                } elseif (!$data['is_published']) {
                    $data['published_at'] = null;
                }
            }

            $success = $this->opinionRepository->update($id, $data);

            // رفع الصورة الجديدة باستخدام Media Library
            if ($success && $imageFile) {
                MediaHelper::addImage(
                    $opinion->fresh(),
                    $imageFile,
                    MediaHelper::COLLECTION_OPINIONS,
                    [
                        'alt' => $opinion->title,
                        'title' => $opinion->title
                    ]
                );
            }

            if ($success) {
                // تحديث عدد مقالات الكاتب القديم والجديد
                if ($oldWriterId) {
                    $this->writerService->updateOpinionsCount($oldWriterId);
                }
                
                if (isset($data['writer_id']) && $data['writer_id'] != $oldWriterId) {
                    $this->writerService->updateOpinionsCount($data['writer_id']);
                }

                Log::info('تم تحديث المقال', [
                    'opinion_id' => $id,
                    'opinion_title' => $data['title'] ?? $opinion->title,
                    'user_id' => auth()->id()
                ]);
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في تحديث المقال: ' . $e->getMessage(), [
                'opinion_id' => $id,
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * حذف مقال (نقل إلى سلة المهملات)
     */
    public function deleteOpinion($id)
    {
        try {
            $opinion = $this->opinionRepository->getById($id);
            
            if (!$opinion) {
                throw new Exception('المقال غير موجود');
            }

            $writerId = $opinion->writer_id;

            $success = $this->opinionRepository->delete($id);

            if ($success) {
                // تحديث عدد مقالات الكاتب
                $this->writerService->updateOpinionsCount($writerId);

                Log::info('تم نقل المقال إلى سلة المهملات', [
                    'opinion_id' => $id,
                    'opinion_title' => $opinion->title,
                    'writer_id' => $writerId,
                    'user_id' => auth()->id()
                ]);
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في حذف المقال: ' . $e->getMessage(), [
                'opinion_id' => $id,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * استعادة مقال من سلة المهملات
     */
    public function restoreOpinion($id)
    {
        try {
            $success = $this->opinionRepository->restore($id);

            if ($success) {
                // الحصول على المقال المستعاد
                $opinion = $this->opinionRepository->getById($id);
                
                if ($opinion) {
                    // تحديث عدد مقالات الكاتب
                    $this->writerService->updateOpinionsCount($opinion->writer_id);

                    Log::info('تم استعادة المقال من سلة المهملات', [
                        'opinion_id' => $id,
                        'opinion_title' => $opinion->title,
                        'user_id' => auth()->id()
                    ]);
                }
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في استعادة المقال: ' . $e->getMessage(), [
                'opinion_id' => $id,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * حذف نهائي للمقال
     */
    public function forceDeleteOpinion($id)
    {
        try {
            // الحصول على المقال مع المحذوفات
            $opinion = $this->opinionRepository->getById($id);
            
            if (!$opinion) {
                // محاولة الحصول على المقال من سلة المهملات
                $trashedOpinions = $this->opinionRepository->getTrashed();
                $opinion = $trashedOpinions->where('id', $id)->first();
            }

            if (!$opinion) {
                throw new Exception('المقال غير موجود');
            }

            $writerId = $opinion->writer_id;
            $imagePath = $opinion->image;

            $success = $this->opinionRepository->forceDelete($id);

            if ($success) {
                // حذف الصورة
                if ($imagePath) {
                    $this->deleteImage($imagePath);
                }

                // تحديث عدد مقالات الكاتب
                $this->writerService->updateOpinionsCount($writerId);

                Log::info('تم حذف المقال نهائياً', [
                    'opinion_id' => $id,
                    'opinion_title' => $opinion->title,
                    'user_id' => auth()->id()
                ]);
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في الحذف النهائي للمقال: ' . $e->getMessage(), [
                'opinion_id' => $id,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * تغيير حالة النشر
     */
    public function toggleStatus($id)
    {
        try {
            $opinion = $this->opinionRepository->getById($id);
            
            if (!$opinion) {
                throw new Exception('المقال غير موجود');
            }

            // التحقق من صلاحية النشر إذا كان سيتم نشر المقال
            if (!$opinion->is_published && !auth()->user()->can('نشر مقالات الرأي')) {
                Log::warning('محاولة نشر مقال بدون صلاحية', [
                    'opinion_id' => $id,
                    'user_id' => auth()->id()
                ]);
                
                throw new Exception('ليس لديك صلاحية لنشر المقالات');
            }

            $success = $this->opinionRepository->toggleStatus($id);

            if ($success) {
                $newStatus = !$opinion->is_published ? 'نشر' : 'إلغاء نشر';
                
                Log::info("تم {$newStatus} المقال", [
                    'opinion_id' => $id,
                    'opinion_title' => $opinion->title,
                    'new_status' => !$opinion->is_published,
                    'user_id' => auth()->id()
                ]);
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في تغيير حالة النشر: ' . $e->getMessage(), [
                'opinion_id' => $id,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * تغيير حالة التمييز
     */
    public function toggleFeatured($id)
    {
        try {
            $opinion = $this->opinionRepository->getById($id);
            
            if (!$opinion) {
                throw new Exception('المقال غير موجود');
            }

            $success = $this->opinionRepository->toggleFeatured($id);

            if ($success) {
                $newStatus = !$opinion->is_featured ? 'تمييز' : 'إلغاء تمييز';
                
                Log::info("تم {$newStatus} المقال", [
                    'opinion_id' => $id,
                    'opinion_title' => $opinion->title,
                    'user_id' => auth()->id()
                ]);
            }

            return $success;
        } catch (Exception $e) {
            Log::error('خطأ في تغيير حالة التمييز: ' . $e->getMessage(), [
                'opinion_id' => $id,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * العمليات المجمعة
     */
    public function bulkAction($action, array $opinionIds)
    {
        try {
            $results = [];
            $canPublish = auth()->user()->can('نشر مقالات الرأي');

            foreach ($opinionIds as $opinionId) {
                switch ($action) {
                    case 'publish':
                        if (!$canPublish) {
                            Log::warning('محاولة نشر مقالات بدون صلاحية', [
                                'opinion_ids' => $opinionIds,
                                'user_id' => auth()->id()
                            ]);
                            throw new Exception('ليس لديك صلاحية لنشر المقالات');
                        }
                        
                        $results[$opinionId] = $this->opinionRepository->update($opinionId, [
                            'is_published' => true,
                            'published_at' => now()
                        ]);
                        break;
                        
                    case 'unpublish':
                        $results[$opinionId] = $this->opinionRepository->update($opinionId, [
                            'is_published' => false,
                            'published_at' => null
                        ]);
                        break;
                        
                    case 'feature':
                        $results[$opinionId] = $this->opinionRepository->update($opinionId, ['is_featured' => true]);
                        break;
                        
                    case 'unfeature':
                        $results[$opinionId] = $this->opinionRepository->update($opinionId, ['is_featured' => false]);
                        break;
                        
                    case 'delete':
                        $results[$opinionId] = $this->deleteOpinion($opinionId);
                        break;
                        
                    default:
                        throw new Exception('عملية غير مدعومة');
                }
            }

            Log::info('تم تنفيذ عملية مجمعة على المقالات', [
                'action' => $action,
                'opinion_ids' => $opinionIds,
                'results' => $results,
                'user_id' => auth()->id()
            ]);

            return $results;
        } catch (Exception $e) {
            Log::error('خطأ في العملية المجمعة: ' . $e->getMessage(), [
                'action' => $action,
                'opinion_ids' => $opinionIds,
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * الحصول على المقالات المنشورة
     */
    public function getPublishedOpinions($limit = null)
    {
        try {
            return $this->opinionRepository->getPublished($limit);
        } catch (Exception $e) {
            Log::error('خطأ في جلب المقالات المنشورة: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * الحصول على المقالات المميزة
     */
    public function getFeaturedOpinions($limit = null)
    {
        try {
            return $this->opinionRepository->getFeatured($limit);
        } catch (Exception $e) {
            Log::error('خطأ في جلب المقالات المميزة: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * الحصول على إحصائيات المقالات
     */
    public function getStatistics()
    {
        try {
            return $this->opinionRepository->getStatistics();
        } catch (Exception $e) {
            Log::error('خطأ في جلب إحصائيات المقالات: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * زيادة عدد المشاهدات
     */
    public function incrementViews($id)
    {
        try {
            return $this->opinionRepository->incrementViews($id);
        } catch (Exception $e) {
            Log::error('خطأ في زيادة المشاهدات: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * زيادة عدد الإعجابات
     */
    public function incrementLikes($id)
    {
        try {
            return $this->opinionRepository->incrementLikes($id);
        } catch (Exception $e) {
            Log::error('خطأ في زيادة الإعجابات: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * رفع صورة
     */
    protected function uploadImage(UploadedFile $file): string
    {
        try {
            $path = $file->store('opinions', 'public');
            
            Log::info('تم رفع صورة مقال', [
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize()
            ]);

            return $path;
        } catch (Exception $e) {
            Log::error('خطأ في رفع صورة المقال: ' . $e->getMessage());
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
                
                Log::info('تم حذف صورة مقال', ['path' => $path]);
            }
        } catch (Exception $e) {
            Log::warning('خطأ في حذف صورة المقال: ' . $e->getMessage(), ['path' => $path]);
        }
    }
}
