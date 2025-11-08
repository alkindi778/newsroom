<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AdvertisementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    protected $advertisementService;

    public function __construct(AdvertisementService $advertisementService)
    {
        $this->advertisementService = $advertisementService;
    }

    /**
     * Display a listing of advertisements
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'type' => $request->get('type'),
            'position' => $request->get('position'),
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => 10,
        ];

        $advertisements = $this->advertisementService->getAllAdvertisements($filters);
        $statistics = $this->advertisementService->getStatistics();

        return view('admin.advertisements.index', compact('advertisements', 'statistics'));
    }

    /**
     * Show the form for creating a new advertisement
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $homepageSections = \App\Models\HomepageSection::orderBy('order')->get();
        
        $types = [
            'banner' => 'بانر',
            'sidebar' => 'شريط جانبي',
            'popup' => 'نافذة منبثقة',
            'inline' => 'إعلان مضمن',
            'floating' => 'إعلان عائم',
        ];

        $positions = [
            'header' => 'أعلى الصفحة',
            'footer' => 'أسفل الصفحة',
            'sidebar_right' => 'الشريط الجانبي الأيمن',
            'sidebar_left' => 'الشريط الجانبي الأيسر',
            'article_top' => 'أعلى الخبر',
            'article_bottom' => 'أسفل الخبر',
            'article_middle' => 'وسط الخبر',
            'homepage_top' => 'أعلى الصفحة الرئيسية',
            'homepage_bottom' => 'أسفل الصفحة الرئيسية',
            'between_articles' => 'بين الأخبار',
            'between_sections' => 'بين أقسام الصفحة الرئيسية',
        ];

        $pages = [
            'home' => 'الصفحة الرئيسية',
            'article' => 'صفحة الخبر',
            'opinion' => 'صفحة مقال الرأي',
            'category' => 'صفحة القسم',
            'articles' => 'صفحة الأخبار',
            'opinions' => 'صفحة مقالات الرأي',
            'videos' => 'صفحة الفيديوهات',
        ];

        $devices = [
            'desktop' => 'سطح المكتب',
            'tablet' => 'التابلت',
            'mobile' => 'الموبايل',
        ];

        return view('admin.advertisements.create', compact('categories', 'homepageSections', 'types', 'positions', 'pages', 'devices'));
    }

    /**
     * Store a newly created advertisement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:banner,sidebar,popup,inline,floating',
            'position' => 'required|in:header,footer,sidebar_right,sidebar_left,article_top,article_bottom,article_middle,homepage_top,homepage_bottom,between_articles,between_sections',
            'link' => 'nullable|url',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
            'priority' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->except('_token');
            
            // Handle checkboxes
            $data['is_active'] = $request->has('is_active');
            $data['open_new_tab'] = $request->has('open_new_tab');

            // Handle arrays
            $data['target_pages'] = $request->input('target_pages', []);
            $data['target_categories'] = $request->input('target_categories', []);
            $data['target_devices'] = $request->input('target_devices', []);

            $advertisement = $this->advertisementService->createAdvertisement($data);

            return redirect()->route('admin.advertisements.index')
                ->with('success', 'تم إضافة الإعلان بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة الإعلان: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified advertisement
     */
    public function show($id)
    {
        $advertisement = $this->advertisementService->getAdvertisementById($id);

        if (!$advertisement) {
            return redirect()->route('admin.advertisements.index')
                ->with('error', 'الإعلان غير موجود');
        }

        return view('admin.advertisements.show', compact('advertisement'));
    }

    /**
     * Show the form for editing the specified advertisement
     */
    public function edit($id)
    {
        $advertisement = $this->advertisementService->getAdvertisementById($id);

        if (!$advertisement) {
            return redirect()->route('admin.advertisements.index')
                ->with('error', 'الإعلان غير موجود');
        }

        $categories = Category::orderBy('name')->get();
        $homepageSections = \App\Models\HomepageSection::orderBy('order')->get();
        
        $types = [
            'banner' => 'بانر',
            'sidebar' => 'شريط جانبي',
            'popup' => 'نافذة منبثقة',
            'inline' => 'إعلان مضمن',
            'floating' => 'إعلان عائم',
        ];

        $positions = [
            'header' => 'أعلى الصفحة',
            'footer' => 'أسفل الصفحة',
            'sidebar_right' => 'الشريط الجانبي الأيمن',
            'sidebar_left' => 'الشريط الجانبي الأيسر',
            'article_top' => 'أعلى الخبر',
            'article_bottom' => 'أسفل الخبر',
            'article_middle' => 'وسط الخبر',
            'homepage_top' => 'أعلى الصفحة الرئيسية',
            'homepage_bottom' => 'أسفل الصفحة الرئيسية',
            'between_articles' => 'بين الأخبار',
            'between_sections' => 'بين أقسام الصفحة الرئيسية',
        ];

        $pages = [
            'home' => 'الصفحة الرئيسية',
            'article' => 'صفحة الخبر',
            'opinion' => 'صفحة مقال الرأي',
            'category' => 'صفحة القسم',
            'articles' => 'صفحة الأخبار',
            'opinions' => 'صفحة مقالات الرأي',
            'videos' => 'صفحة الفيديوهات',
        ];

        $devices = [
            'desktop' => 'سطح المكتب',
            'tablet' => 'التابلت',
            'mobile' => 'الموبايل',
        ];

        return view('admin.advertisements.edit', compact('advertisement', 'categories', 'homepageSections', 'types', 'positions', 'pages', 'devices'));
    }

    /**
     * Update the specified advertisement
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:banner,sidebar,popup,inline,floating',
            'position' => 'required|in:header,footer,sidebar_right,sidebar_left,article_top,article_bottom,article_middle,homepage_top,homepage_bottom,between_articles,between_sections',
            'link' => 'nullable|url',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'width' => 'nullable|integer|min:1',
            'height' => 'nullable|integer|min:1',
            'priority' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->except('_token', '_method');
            
            // Handle checkboxes
            $data['is_active'] = $request->has('is_active');
            $data['open_new_tab'] = $request->has('open_new_tab');

            // Handle arrays
            $data['target_pages'] = $request->input('target_pages', []);
            $data['target_categories'] = $request->input('target_categories', []);
            $data['target_devices'] = $request->input('target_devices', []);

            $advertisement = $this->advertisementService->updateAdvertisement($id, $data);

            if (!$advertisement) {
                return redirect()->route('admin.advertisements.index')
                    ->with('error', 'الإعلان غير موجود');
            }

            return redirect()->route('admin.advertisements.index')
                ->with('success', 'تم تحديث الإعلان بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الإعلان: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified advertisement
     */
    public function destroy($id)
    {
        try {
            $result = $this->advertisementService->deleteAdvertisement($id);

            if (!$result) {
                return redirect()->route('admin.advertisements.index')
                    ->with('error', 'الإعلان غير موجود');
            }

            return redirect()->route('admin.advertisements.index')
                ->with('success', 'تم حذف الإعلان بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('admin.advertisements.index')
                ->with('error', 'حدث خطأ أثناء حذف الإعلان: ' . $e->getMessage());
        }
    }

    /**
     * Toggle advertisement status
     */
    public function toggleStatus($id)
    {
        try {
            $result = $this->advertisementService->toggleStatus($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'الإعلان غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تغيير حالة الإعلان بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة الإعلان'
            ], 500);
        }
    }

    /**
     * Update advertisement priority
     */
    public function updatePriority(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'priority' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة'
            ], 422);
        }

        try {
            $result = $this->advertisementService->updatePriority($id, $request->priority);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'الإعلان غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث أولوية الإعلان بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث أولوية الإعلان'
            ], 500);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:advertisements,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'بيانات غير صحيحة')
                ->withErrors($validator);
        }

        try {
            $count = $this->advertisementService->bulkAction($request->action, $request->ids);

            $messages = [
                'activate' => "تم تفعيل {$count} إعلان بنجاح",
                'deactivate' => "تم تعطيل {$count} إعلان بنجاح",
                'delete' => "تم حذف {$count} إعلان بنجاح",
            ];

            return redirect()->route('admin.advertisements.index')
                ->with('success', $messages[$request->action]);
        } catch (\Exception $e) {
            return redirect()->route('admin.advertisements.index')
                ->with('error', 'حدث خطأ أثناء تنفيذ العملية: ' . $e->getMessage());
        }
    }

    /**
     * Show statistics
     */
    public function statistics()
    {
        $statistics = $this->advertisementService->getStatistics();
        $expired = $this->advertisementService->getExpired();
        $scheduled = $this->advertisementService->getScheduled();

        return view('admin.advertisements.statistics', compact('statistics', 'expired', 'scheduled'));
    }
}
