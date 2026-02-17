<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\ArticleService;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Validation\Rule;
use App\Services\GeminiService;

class ArticleController extends Controller
{
    protected $articleService;
    protected $articleRepository;
    protected $geminiService;

    public function __construct(
        ArticleService $articleService,
        ArticleRepositoryInterface $articleRepository,
        GeminiService $geminiService
    ) {
        $this->articleService = $articleService;
        $this->articleRepository = $articleRepository;
        $this->geminiService = $geminiService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $articles = $this->articleService->getArticlesForAdmin($request);
        $categories = Category::all();
        $users = User::all();
        
        // عدد المقالات المعلقة (للمدراء فقط)
        $pendingCount = 0;
        if (auth()->user()->can('عرض المقالات المعلقة')) {
            $pendingCount = Article::where('approval_status', 'pending_approval')->count();
        }

        return view('admin.news.index', compact('articles', 'categories', 'users', 'pendingCount'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.news.create', compact('categories'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'image' => 'nullable|string|max:500',
            'image_alt' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'show_in_slider' => 'nullable|boolean',
            'is_breaking_news' => 'nullable|boolean',
        ]);

        // Handle checkboxes - they don't send value when unchecked
        $validated['show_in_slider'] = $request->has('show_in_slider') ? 1 : 0;
        $validated['is_breaking_news'] = $request->has('is_breaking_news') ? 1 : 0;

        try {
            // 🔍 DEBUG: تتبع بيانات الصورة
            \Log::info('📝 ArticleController@store - بداية إنشاء الخبر', [
                'has_image_field' => isset($validated['image']),
                'image_value' => $validated['image'] ?? 'غير موجود',
                'has_file_upload' => $request->hasFile('image'),
                'file_upload_valid' => $request->hasFile('image') ? $request->file('image')->isValid() : false,
                'file_size' => $request->hasFile('image') ? $request->file('image')->getSize() : 0,
                'request_image_input' => $request->input('image', 'غير موجود'),
                'all_files' => array_keys($request->allFiles()),
                'content_type' => $request->header('Content-Type'),
            ]);

            $article = $this->articleService->createArticle($validated, $request);
            
            // 🔍 DEBUG: بعد الإنشاء
            \Log::info('✅ ArticleController@store - تم إنشاء الخبر', [
                'article_id' => $article->id,
                'article_image_column' => $article->getAttributes()['image'] ?? 'فارغ',
                'has_media' => $article->hasMedia(\App\Helpers\MediaHelper::COLLECTION_ARTICLES),
                'media_url' => $article->getFirstMediaUrl(\App\Helpers\MediaHelper::COLLECTION_ARTICLES) ?: 'لا يوجد',
                'image_path_accessor' => $article->image_path ?? 'فارغ',
            ]);

            $successMessage = 'تم إنشاء الخبر بنجاح!';
            
            // تحديد الرسالة بناءً على حالة المقال
            if (($request->get('status') === 'published' || $request->get('action') === 'publish')) {
                if (auth()->user()->hasRole('مراسل صحفي')) {
                    // المراسل قدّم المقال للموافقة
                    $successMessage = 'تم تقديم الخبر للموافقة بنجاح! ✅ سيتم مراجعته من قبل المدير قبل النشر.';
                } else {
                    // المدير نشر المقال مباشرة
                    $successMessage = 'تم نشر الخبر بنجاح! 🎉';
                }
            } else {
                // تم الحفظ كمسودة
                $successMessage = 'تم حفظ الخبر كمسودة بنجاح! 📝';
            }
            
            return redirect()->route('admin.articles.index')
                           ->with('success', $successMessage);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'حدث خطأ أثناء إنشاء الخبر: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return view('admin.news.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $categories = Category::all();
        return view('admin.news.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('articles', 'slug')->ignore($article->id)
            ],
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'image' => 'nullable|string|max:500', // URL من المكتبة أو مسار الملف
            'image_alt' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'remove_image' => 'nullable|boolean',
            'show_in_slider' => 'nullable|boolean',
            'is_breaking_news' => 'nullable|boolean',
        ]);

        // Handle checkboxes - they don't send value when unchecked
        $validated['show_in_slider'] = $request->has('show_in_slider') ? 1 : 0;
        $validated['is_breaking_news'] = $request->has('is_breaking_news') ? 1 : 0;

        try {
            $this->articleService->updateArticle($article, $validated, $request);
            
            $successMessage = 'تم تحديث الخبر بنجاح!';
            
            // تحديد الرسالة بناءً على حالة المقال
            if (($request->get('status') === 'published' || $request->get('action') === 'publish')) {
                if (auth()->user()->hasRole('مراسل صحفي')) {
                    // المراسل قدّم المقال للموافقة
                    $successMessage = 'تم تقديم الخبر للموافقة بنجاح! ✅ سيتم مراجعته من قبل المدير قبل النشر.';
                } else {
                    // المدير نشر المقال مباشرة
                    $successMessage = 'تم نشر الخبر بنجاح! 🎉';
                }
            } else {
                // تم الحفظ كمسودة
                $successMessage = 'تم تحديث الخبر وحفظه كمسودة بنجاح! 📝';
            }
            
            return redirect()->route('admin.articles.index')
                           ->with('success', $successMessage);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'حدث خطأ أثناء تحديث الخبر: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        try {
            $this->articleService->deleteArticle($article);
            
            return redirect()->route('admin.articles.index')
                           ->with('success', 'تم نقل الخبر إلى سلة المهملات بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء حذف الخبر: ' . $e->getMessage());
        }
    }

    /**
     * Toggle article status (AJAX endpoint)
     */
    public function toggleStatus(Article $article)
    {
        try {
            $result = $this->articleService->toggleStatus($article);
            
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك نشر الخبر - لا تملك صلاحية نشر الأخبار'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم تغيير حالة الخبر بنجاح',
                'new_status' => $article->fresh()->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة الخبر'
            ], 500);
        }
    }

    /**
     * Bulk actions for articles
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,draft',
            'articles' => 'required|array',
            'articles.*' => 'exists:articles,id'
        ]);

        try {
            $count = 0;
            
            switch ($request->action) {
                case 'delete':
                    $count = $this->articleService->bulkDelete($request->articles);
                    $message = "تم نقل {$count} خبر إلى سلة المهملات بنجاح";
                    break;
                    
                case 'publish':
                    if (!auth()->user()->can('publish_articles')) {
                        return redirect()->back()
                                       ->with('error', 'لا تملك صلاحية نشر الأخبار - تم حفظ المقالات كمسودات');
                    }
                    $count = $this->articleService->bulkStatusUpdate($request->articles, 'published');
                    $message = "تم نشر {$count} خبر بنجاح";
                    break;
                    
                case 'draft':
                    $count = $this->articleService->bulkStatusUpdate($request->articles, 'draft');
                    $message = "تم تحويل {$count} خبر إلى مسودة";
                    break;
            }
            
            return redirect()->route('admin.articles.index')
                           ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء تنفيذ العملية: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate article
     */
    public function duplicate(Article $article)
    {
        try {
            $newArticle = $this->articleService->duplicateArticle($article);
            
            return redirect()->route('admin.articles.edit', $newArticle)
                           ->with('success', 'تم نسخ الخبر بنجاح! يمكنك الآن تحريره.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء نسخ الخبر: ' . $e->getMessage());
        }
    }

    /**
     * Search articles (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        try {
            $articles = $this->articleService->searchArticles($request->q);
            
            return response()->json([
                'success' => true,
                'articles' => $articles->map(function ($article) {
                    return [
                        'id' => $article->id,
                        'title' => $article->title,
                        'slug' => $article->slug,
                        'status' => $article->status,
                        'category' => $article->category->name ?? 'بدون قسم',
                        'created_at' => $article->created_at->diffForHumans()
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
     * Show pending approval articles
     */
    public function pending()
    {
        $articles = $this->articleService->getPendingArticles();
        return view('admin.news.pending', compact('articles'));
    }

    /**
     * Submit article for approval
     */
    public function submitForApproval(Article $article)
    {
        // التأكد من أن المستخدم هو صاحب المقال
        if ($article->user_id !== auth()->id() && !auth()->user()->can('manage_articles')) {
            return redirect()->back()->with('error', 'غير مصرح لك بتقديم هذا المقال للموافقة');
        }

        try {
            $this->articleService->submitForApproval($article);
            return redirect()->back()->with('success', 'تم تقديم المقال للموافقة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تقديم المقال: ' . $e->getMessage());
        }
    }

    /**
     * Approve article
     */
    public function approve(Article $article)
    {
        if (!auth()->user()->can('الموافقة على المقالات')) {
            abort(403, 'غير مصرح لك بالموافقة على المقالات');
        }

        try {
            $this->articleService->approveArticle($article);
            return redirect()->back()->with('success', 'تم الموافقة على المقال ونشره بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الموافقة: ' . $e->getMessage());
        }
    }

    /**
     * Reject article
     */
    public function reject(Article $article, Request $request)
    {
        if (!auth()->user()->can('رفض المقالات')) {
            abort(403, 'غير مصرح لك برفض المقالات');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ], [
            'rejection_reason.required' => 'يجب كتابة سبب الرفض'
        ]);

        try {
            $this->articleService->rejectArticle($article, $validated['rejection_reason']);
            return redirect()->back()->with('success', 'تم رفض المقال وإبلاغ المراسل بالسبب');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الرفض: ' . $e->getMessage());
        }
    }

    /**
     * Generate SEO data using AI (AJAX)
     */
    public function generateSeo(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string'
        ]);

        try {
            $seoData = $this->geminiService->generateSeoData(
                $request->title,
                $request->content
            );
            
            if (empty($seoData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل توليد البيانات. يرجى المحاولة مرة أخرى.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $seoData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء المعالجة: ' . $e->getMessage()
            ], 500);
        }
    }
}
