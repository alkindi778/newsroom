<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Category;
use App\Services\HomepageSectionService;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    protected $homepageSectionService;

    public function __construct(HomepageSectionService $homepageSectionService)
    {
        $this->homepageSectionService = $homepageSectionService;
    }

    /**
     * Display a listing of the homepage sections.
     */
    public function index()
    {
        $sections = $this->homepageSectionService->getAllSections();

        return view('admin.homepage-sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new section.
     */
    public function create()
    {
        $types = $this->homepageSectionService->getAvailableTypes();
        $categories = Category::all();

        return view('admin.homepage-sections.create', compact('types', 'categories'));
    }

    /**
     * Store a newly created section in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:homepage_sections',
            'type' => 'required|in:slider,breaking_news,latest_news,category_news,trending,opinions,videos,newspaper_issues',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'order' => 'required|integer|min:0',
            'items_count' => 'required|integer|min:1|max:50',
            'is_active' => 'boolean',
            'template_style' => 'nullable|in:default,grid,featured,list,magazine',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Add template to settings if provided
        $settings = [];
        if ($request->has('template_style')) {
            $settings['template'] = $request->input('template_style');
        }
        $validated['settings'] = $settings;

        $section = $this->homepageSectionService->createSection($validated);

        if (!$section) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة القسم');
        }

        return redirect()
            ->route('admin.homepage-sections.index')
            ->with('success', 'تم إضافة القسم بنجاح');
    }

    /**
     * Show the form for editing the specified section.
     */
    public function edit(HomepageSection $homepageSection)
    {
        $types = $this->homepageSectionService->getAvailableTypes();
        $categories = Category::all();

        return view('admin.homepage-sections.edit', compact('homepageSection', 'types', 'categories'));
    }

    /**
     * Update the specified section in storage.
     */
    public function update(Request $request, HomepageSection $homepageSection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:homepage_sections,slug,' . $homepageSection->id,
            'type' => 'required|string',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'order' => 'required|integer|min:0',
            'items_count' => 'required|integer|min:1|max:50',
            'is_active' => 'boolean',
            'template_style' => 'nullable|in:default,grid,featured,list,magazine',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Merge template with existing settings
        $settings = $homepageSection->settings ?? [];
        if ($request->has('template_style')) {
            $settings['template'] = $request->input('template_style');
        }
        $validated['settings'] = $settings;

        $success = $this->homepageSectionService->updateSection($homepageSection, $validated);

        if (!$success) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث القسم');
        }

        return redirect()
            ->route('admin.homepage-sections.index')
            ->with('success', 'تم تحديث القسم بنجاح');
    }

    /**
     * Remove the specified section from storage.
     */
    public function destroy(HomepageSection $homepageSection)
    {
        $success = $this->homepageSectionService->deleteSection($homepageSection);

        if (!$success) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف القسم');
        }

        return redirect()
            ->route('admin.homepage-sections.index')
            ->with('success', 'تم حذف القسم بنجاح');
    }

    /**
     * Toggle section active status.
     */
    public function toggleStatus(HomepageSection $homepageSection)
    {
        $success = $this->homepageSectionService->toggleSectionStatus($homepageSection);

        if (!$success) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة القسم');
        }

        $status = $homepageSection->fresh()->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()
            ->route('admin.homepage-sections.index')
            ->with('success', "تم {$status} القسم بنجاح");
    }

    /**
     * Update sections order.
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:homepage_sections,id',
            'sections.*.order' => 'required|integer|min:0',
        ]);

        $success = $this->homepageSectionService->updateSectionsOrder($validated['sections']);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الترتيب'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الترتيب بنجاح'
        ]);
    }
}
