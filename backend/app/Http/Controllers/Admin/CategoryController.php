<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\CategoryService;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $categoryRepository;

    public function __construct(
        CategoryService $categoryService,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryService = $categoryService;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $this->categoryRepository->getAllWithFilters($request);
        
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->categoryRepository->getAll();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = $this->categoryService->validateCategoryData($request->all());
        $validated = $request->validate($rules);

        try {
            $category = $this->categoryService->createCategory($validated);
            
            return redirect()->route('admin.categories.index')
                           ->with('success', 'تم إنشاء القسم بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'حدث خطأ أثناء إنشاء القسم: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $articles = $this->categoryService->getCategoryWithArticles($category, 10);
        
        return view('admin.categories.show', compact('category', 'articles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = $this->categoryRepository->getAll()->where('id', '!=', $category->id);

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $rules = $this->categoryService->validateCategoryData($request->all(), $category);
        $validated = $request->validate($rules);

        try {
            $this->categoryService->updateCategory($category, $validated);
            
            return redirect()->route('admin.categories.index')
                           ->with('success', 'تم تحديث القسم بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'حدث خطأ أثناء تحديث القسم: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $this->categoryService->deleteCategory($category);
            
            return redirect()->route('admin.categories.index')
                           ->with('success', 'تم حذف القسم بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle category status (AJAX endpoint)
     */
    public function toggleStatus(Category $category)
    {
        try {
            $this->categoryService->toggleStatus($category);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تغيير حالة القسم بنجاح',
                'new_status' => $category->fresh()->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة القسم'
            ], 500);
        }
    }

    /**
     * Get categories for API or AJAX requests
     */
    public function getCategories(Request $request)
    {
        try {
            $categories = $this->categoryService->getCategoriesForSelect();
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'categories' => $categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'color' => $category->color,
                        ];
                    })
                ]);
            }
            
            return $categories;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء جلب الأقسام'
                ], 500);
            }
            
            return collect();
        }
    }

    /**
     * Get category statistics (AJAX endpoint)
     */
    public function getStatistics()
    {
        try {
            $stats = $this->categoryService->getStatistics();
            
            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات'
            ], 500);
        }
    }

    /**
     * Search categories (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        try {
            $categories = $this->categoryService->searchCategories($request->q);
            
            return response()->json([
                'success' => true,
                'categories' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'color' => $category->color,
                        'is_active' => $category->is_active,
                        'created_at' => $category->created_at->diffForHumans()
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث'
            ], 500);
        }
    }

    /**
     * Bulk actions for categories
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);

        try {
            $status = $request->action === 'activate';
            $count = $this->categoryService->bulkStatusUpdate($request->categories, $status);
            
            $message = $status 
                ? "تم تفعيل {$count} قسم بنجاح"
                : "تم إلغاء تفعيل {$count} قسم بنجاح";
            
            return redirect()->route('admin.categories.index')
                           ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء تنفيذ العملية: ' . $e->getMessage());
        }
    }

    /**
     * Update categories order (AJAX endpoint)
     */
    public function updateOrder(Request $request)
    {
        try {
            $categories = $request->input('categories', []);
            
            foreach ($categories as $category) {
                Category::where('id', $category['id'])
                    ->update(['order' => $category['order']]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الترتيب بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الترتيب: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category insights (AJAX endpoint)
     */
    public function getInsights(Category $category)
    {
        try {
            $insights = $this->categoryService->getCategoryInsights($category);
            $seoAnalysis = $this->categoryService->getSEOAnalysis($category);
            
            return response()->json([
                'success' => true,
                'insights' => $insights,
                'seo_analysis' => $seoAnalysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب التحليلات'
            ], 500);
        }
    }
}
