<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WriterService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WriterController extends Controller
{
    protected $writerService;

    public function __construct(WriterService $writerService)
    {
        $this->writerService = $writerService;
    }

    /**
     * عرض قائمة الكُتاب
     */
    public function index(Request $request)
    {
        try {
            $writers = $this->writerService->getAllWithFilters(
                $request->get('search'),
                $request->get('status'),
                $request->get('sort_by', 'created_at'),
                $request->get('sort_direction', 'desc'),
                10
            );

            return view('admin.writers.index', compact('writers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في عرض الكُتاب: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج إضافة كاتب جديد
     */
    public function create()
    {
        try {
            return view('admin.writers.create');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.writers.index')
                ->with('error', 'حدث خطأ في تحميل نموذج الإضافة: ' . $e->getMessage());
        }
    }

    /**
     * حفظ كاتب جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:writers,email',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'image' => 'nullable|string|max:500', // URL من المكتبة أو مسار الملف
            'position' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            $writer = $this->writerService->createWriter($validated);

            return redirect()
                ->route('admin.writers.index')
                ->with('success', 'تم إضافة الكاتب بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ في إضافة الكاتب: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الكاتب
     */
    public function show($id)
    {
        try {
            $writer = $this->writerService->getById($id);
            
            if (!$writer) {
                return redirect()
                    ->route('admin.writers.index')
                    ->with('error', 'الكاتب غير موجود');
            }

            return view('admin.writers.show', compact('writer'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.writers.index')
                ->with('error', 'حدث خطأ في عرض الكاتب: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تعديل الكاتب
     */
    public function edit($id)
    {
        try {
            $writer = $this->writerService->getById($id);
            
            if (!$writer) {
                return redirect()
                    ->route('admin.writers.index')
                    ->with('error', 'الكاتب غير موجود');
            }

            return view('admin.writers.edit', compact('writer'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.writers.index')
                ->with('error', 'حدث خطأ في تحميل نموذج التعديل: ' . $e->getMessage());
        }
    }

    /**
     * تحديث بيانات الكاتب
     */
    public function update(Request $request, $id)
    {
        $writer = $this->writerService->getById($id);
        
        if (!$writer) {
            return redirect()
                ->route('admin.writers.index')
                ->with('error', 'الكاتب غير موجود');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('writers')->ignore($id)
            ],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'image' => 'nullable|string|max:500', // URL من المكتبة أو مسار الملف
            'position' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            $this->writerService->updateWriter($id, $validated);

            return redirect()
                ->route('admin.writers.index')
                ->with('success', 'تم تحديث بيانات الكاتب بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ في تحديث الكاتب: ' . $e->getMessage());
        }
    }

    /**
     * حذف الكاتب
     */
    public function destroy($id)
    {
        try {
            $success = $this->writerService->deleteWriter($id);

            if ($success) {
                return redirect()
                    ->route('admin.writers.index')
                    ->with('success', 'تم حذف الكاتب بنجاح');
            } else {
                return redirect()
                    ->route('admin.writers.index')
                    ->with('error', 'حدث خطأ في حذف الكاتب');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.writers.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * تغيير حالة النشاط
     */
    public function toggleStatus($id)
    {
        try {
            $writer = $this->writerService->getById($id);
            
            if (!$writer) {
                return redirect()
                    ->route('admin.writers.index')
                    ->with('error', 'الكاتب غير موجود');
            }

            $this->writerService->toggleStatus($id);

            $status = !$writer->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';
            
            return redirect()
                ->route('admin.writers.index')
                ->with('success', "{$status} الكاتب بنجاح");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.writers.index')
                ->with('error', 'حدث خطأ في تغيير حالة الكاتب: ' . $e->getMessage());
        }
    }

    /**
     * العمليات المجمعة
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'writers' => 'required|array',
            'writers.*' => 'exists:writers,id'
        ]);

        try {
            $results = $this->writerService->bulkAction($request->action, $request->writers);
            
            $successCount = count(array_filter($results));
            $totalCount = count($results);
            
            if ($successCount === $totalCount) {
                switch ($request->action) {
                    case 'activate':
                        $message = 'تم تفعيل الكُتاب المحددين بنجاح';
                        break;
                    case 'deactivate':
                        $message = 'تم إلغاء تفعيل الكُتاب المحددين بنجاح';
                        break;
                    case 'delete':
                        $message = 'تم حذف الكُتاب المحددين بنجاح';
                        break;
                }
            } else {
                $message = "تم تنفيذ العملية على {$successCount} من {$totalCount} كاتب";
            }

            return redirect()
                ->route('admin.writers.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.writers.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * عرض مقالات كاتب محدد
     */
    public function opinions(Request $request, $writerId)
    {
        try {
            $writer = $this->writerService->getById($writerId);
            
            if (!$writer) {
                return redirect()
                    ->route('admin.writers.index')
                    ->with('error', 'الكاتب غير موجود');
            }

            // استخدام OpinionService للحصول على المقالات
            app(\App\Services\OpinionService::class);
            $opinionService = app(\App\Services\OpinionService::class);
            
            $opinions = $opinionService->getAllWithFilters(
                $request->get('search'),
                $request->get('status'),
                $writerId,
                null,
                'created_at',
                'desc',
                10
            );

            return view('admin.writers.opinions', compact('writer', 'opinions'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.writers.index')
                ->with('error', 'حدث خطأ في عرض مقالات الكاتب: ' . $e->getMessage());
        }
    }
}
