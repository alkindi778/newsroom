<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewspaperIssue;
use App\Services\NewspaperIssueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewspaperIssueController extends Controller
{
    protected NewspaperIssueService $service;

    public function __construct(NewspaperIssueService $service)
    {
        $this->service = $service;
    }

    /**
     * قائمة الإصدارات
     */
    public function index(Request $request)
    {
        $filters = [
            'search'   => $request->search,
            'status'   => $request->status,
            'featured' => $request->featured,
            'sort'     => $request->sort,
            'per_page' => $request->per_page ?? 15,
        ];

        $issues = $this->service->getAllIssues($filters);

        return view('admin.newspaper-issues.index', compact('issues'));
    }

    /**
     * صفحة إنشاء إصدار جديد
     */
    public function create()
    {
        return view('admin.newspaper-issues.create');
    }

    /**
     * حفظ إصدار جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'newspaper_name'   => 'required|string|max:255',
            'issue_number'     => 'required|integer',
            'description'      => 'nullable|string',
            'pdf_url'          => 'required|url',
            'cover_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'publication_date' => 'required|date',
            'is_featured'      => 'boolean',
            'is_published'     => 'boolean',
        ]);

        try {
            $data = $request->except('_token');
            $data['slug'] = Str::slug($data['newspaper_name'].'-'.$data['issue_number']);
            $data['user_id'] = auth()->id();

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image');
            }

            $this->service->createIssue($data);

            return redirect()->route('admin.newspaper-issues.index')
                ->with('success', 'تم إضافة الإصدار بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating newspaper issue: '.$e->getMessage());

            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الإصدار: '.$e->getMessage());
        }
    }

    /**
     * صفحة تعديل إصدار
     */
    public function edit($id)
    {
        try {
            $issue = $this->service->getIssueById($id);

            return view('admin.newspaper-issues.edit', compact('issue'));
        } catch (\Exception $e) {
            return redirect()->route('admin.newspaper-issues.index')
                ->with('error', 'الإصدار غير موجود');
        }
    }

    /**
     * عرض تفاصيل إصدار معين
     */
    public function show($id)
    {
        try {
            $issue = $this->service->getIssueById($id);

            return view('admin.newspaper-issues.show', compact('issue'));
        } catch (\Exception $e) {
            return redirect()->route('admin.newspaper-issues.index')
                ->with('error', 'الإصدار غير موجود');
        }
    }

    /**
     * تحديث الإصدار
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'newspaper_name'   => 'required|string|max:255',
            'issue_number'     => 'required|integer',
            'description'      => 'nullable|string',
            'pdf_url'          => 'required|url',
            'cover_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'publication_date' => 'required|date',
            'is_featured'      => 'boolean',
            'is_published'     => 'boolean',
        ]);

        try {
            $data = $request->except('_token', '_method');

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image');
            }

            $this->service->updateIssue($id, $data);

            return redirect()->route('admin.newspaper-issues.index')
                ->with('success', 'تم تحديث الإصدار بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating newspaper issue: '.$e->getMessage());

            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الإصدار: '.$e->getMessage());
        }
    }

    /**
     * حذف إصدار
     */
    public function destroy($id)
    {
        try {
            $this->service->deleteIssue($id);

            return redirect()->route('admin.newspaper-issues.index')
                ->with('success', 'تم حذف الإصدار بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف الإصدار');
        }
    }

    /**
     * تبديل التمييز
     */
    public function toggleFeatured($id)
    {
        try {
            $this->service->toggleFeatured($id);

            return back()->with('success', 'تم تحديث حالة التمييز بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث حالة التمييز');
        }
    }
}
