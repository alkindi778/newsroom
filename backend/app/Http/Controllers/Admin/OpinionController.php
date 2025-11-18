<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OpinionService;
use App\Services\WriterService;
use Illuminate\Http\Request;

class OpinionController extends Controller
{
    protected $opinionService;
    protected $writerService;

    public function __construct(OpinionService $opinionService, WriterService $writerService)
    {
        $this->opinionService = $opinionService;
        $this->writerService = $writerService;
    }

    /**
     * عرض قائمة مقالات الرأي
     */
    public function index(Request $request)
    {
        try {
            $opinions = $this->opinionService->getAllWithFilters(
                $request->get('search'),
                $request->get('status'),
                $request->get('writer'),
                $request->get('featured'),
                $request->get('sort_by', 'published_at'),
                $request->get('sort_direction', 'desc'),
                10
            );

            $writers = $this->writerService->getActiveWriters();

            return view('admin.opinions.index', compact('opinions', 'writers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في عرض مقالات الرأي: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج إضافة مقال جديد
     */
    public function create()
    {
        try {
            $writers = $this->writerService->getActiveWriters();

            return view('admin.opinions.create', compact('writers'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', 'حدث خطأ في تحميل نموذج الإضافة: ' . $e->getMessage());
        }
    }

    /**
     * حفظ مقال جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|string|max:500', // URL من المكتبة أو مسار الملف
            'writer_id' => 'required|exists:writers,id',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'tags' => 'nullable|string',
        ]);

        try {
            $opinion = $this->opinionService->createOpinion($validated);
            
            // تحديد رسالة النجاح بناءً على صلاحية النشر
            $canPublish = auth()->user()->can('نشر مقالات الرأي');
            
            if ($validated['is_published'] && $canPublish) {
                $message = 'تم نشر المقال بنجاح';
            } elseif ($validated['is_published'] && !$canPublish) {
                $message = 'تم حفظ المقال كمسودة (لا توجد صلاحية نشر)';
            } else {
                $message = 'تم حفظ المقال كمسودة بنجاح';
            }
            
            return redirect()
                ->route('admin.opinions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ في إضافة المقال: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل المقال
     */
    public function show($id)
    {
        try {
            $opinion = $this->opinionService->getById($id);
            
            if (!$opinion) {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'المقال غير موجود');
            }

            return view('admin.opinions.show', compact('opinion'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', 'حدث خطأ في عرض المقال: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تعديل المقال
     */
    public function edit($id)
    {
        try {
            $opinion = $this->opinionService->getById($id);
            
            if (!$opinion) {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'المقال غير موجود');
            }

            $writers = $this->writerService->getActiveWriters();
            
            return view('admin.opinions.edit', compact('opinion', 'writers'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', 'حدث خطأ في تحميل نموذج التعديل: ' . $e->getMessage());
        }
    }

    /**
     * تحديث المقال
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|string|max:500', // URL من المكتبة أو مسار الملف
            'writer_id' => 'required|exists:writers,id',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'tags' => 'nullable|string',
        ]);

        try {
            $success = $this->opinionService->updateOpinion($id, $validated);

            if ($success) {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('success', 'تم تحديث المقال بنجاح');
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'حدث خطأ في تحديث المقال');
            }
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ في تحديث المقال: ' . $e->getMessage());
        }
    }

    /**
     * حذف المقال (نقل إلى سلة المهملات)
     */
    public function destroy($id)
    {
        try {
            $success = $this->opinionService->deleteOpinion($id);

            if ($success) {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('success', 'تم نقل المقال إلى سلة المهملات');
            } else {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'حدث خطأ في حذف المقال');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * تغيير حالة النشر
     */
    public function toggleStatus($id)
    {
        try {
            $opinion = $this->opinionService->getById($id);
            
            if (!$opinion) {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'المقال غير موجود');
            }

            $success = $this->opinionService->toggleStatus($id);

            if ($success) {
                $status = !$opinion->is_published ? 'نشر' : 'إلغاء نشر';
                
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('success', "تم {$status} المقال بنجاح");
            } else {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'حدث خطأ في تغيير حالة النشر');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * تغيير حالة التمييز
     */
    public function toggleFeatured($id)
    {
        try {
            $opinion = $this->opinionService->getById($id);
            
            if (!$opinion) {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'المقال غير موجود');
            }

            $success = $this->opinionService->toggleFeatured($id);

            if ($success) {
                $status = !$opinion->is_featured ? 'تمييز' : 'إلغاء تمييز';
                
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('success', "تم {$status} المقال بنجاح");
            } else {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'حدث خطأ في تغيير حالة التمييز');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * العمليات المجمعة
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,feature,unfeature,delete',
            'opinions' => 'required|array',
            'opinions.*' => 'exists:opinions,id'
        ]);

        try {
            $results = $this->opinionService->bulkAction($request->action, $request->opinions);
            
            $successCount = count(array_filter($results));
            $totalCount = count($results);
            
            if ($successCount === $totalCount) {
                switch ($request->action) {
                    case 'publish':
                        $message = 'تم نشر المقالات المحددة بنجاح';
                        break;
                    case 'unpublish':
                        $message = 'تم إلغاء نشر المقالات المحددة بنجاح';
                        break;
                    case 'feature':
                        $message = 'تم تمييز المقالات المحددة بنجاح';
                        break;
                    case 'unfeature':
                        $message = 'تم إلغاء تمييز المقالات المحددة بنجاح';
                        break;
                    case 'delete':
                        $message = 'تم نقل المقالات المحددة إلى سلة المهملات';
                        break;
                }
            } else {
                $message = "تم تنفيذ العملية على {$successCount} من {$totalCount} مقال";
            }

            return redirect()
                ->route('admin.opinions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * استعادة المقال من سلة المهملات
     */
    public function restore($id)
    {
        try {
            $success = $this->opinionService->restoreOpinion($id);

            if ($success) {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('success', 'تم استعادة المقال بنجاح');
            } else {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'حدث خطأ في استعادة المقال');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * حذف نهائي للمقال
     */
    public function forceDelete($id)
    {
        try {
            $success = $this->opinionService->forceDeleteOpinion($id);

            if ($success) {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('success', 'تم حذف المقال نهائياً');
            } else {
                return redirect()
                    ->route('admin.opinions.index')
                    ->with('error', 'حدث خطأ في الحذف النهائي للمقال');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.opinions.index')
                ->with('error', $e->getMessage());
        }
    }
}
