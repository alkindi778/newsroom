<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewspaperIssueResource;
use App\Models\NewspaperIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewspaperIssueController extends Controller
{
    /**
     * Get all published newspaper issues (Frontend)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->per_page ?? 12;
            $sort = $request->sort ?? 'recent';

            $query = NewspaperIssue::published();

            // البحث
            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('newspaper_name', 'LIKE', "%{$request->search}%")
                      ->orWhere('description', 'LIKE', "%{$request->search}%");
                });
            }

            // الترتيب
            switch ($sort) {
                case 'views':
                    $query->orderBy('views', 'desc');
                    break;
                case 'downloads':
                    $query->orderBy('downloads', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('publication_date', 'asc');
                    break;
                default:
                    $query->orderBy('publication_date', 'desc');
            }

            $issues = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => NewspaperIssueResource::collection($issues->items()),
                'pagination' => [
                    'total' => $issues->total(),
                    'per_page' => $issues->perPage(),
                    'current_page' => $issues->currentPage(),
                    'last_page' => $issues->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching newspaper issues',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured newspaper issues
     */
    public function featured(Request $request)
    {
        try {
            $limit = $request->limit ?? 6;
            $issues = NewspaperIssue::published()
                ->featured()
                ->recent()
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => NewspaperIssueResource::collection($issues)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching featured issues',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single newspaper issue by slug
     */
    public function show($slug)
    {
        try {
            $issue = NewspaperIssue::where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();

            // Increment views
            $issue->incrementViews();

            return response()->json([
                'success' => true,
                'data' => new NewspaperIssueResource($issue)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Newspaper issue not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new newspaper issue (Admin)
     */
    public function store(Request $request)
    {
        // التحقق من الصلاحية
        if (!auth()->user()->can('create_newspaper_issue')) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإنشاء إصدارات الصحف'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'newspaper_name' => 'required|string|max:255',
            'issue_number' => 'required|integer',
            'description' => 'nullable|string',
            'pdf_url' => 'required|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'publication_date' => 'required|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['slug'] = Str::slug($data['newspaper_name'] . '-' . $data['issue_number']);
            $data['user_id'] = auth()->id();

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('newspapers/covers', 'public');
            }

            $issue = NewspaperIssue::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الإصدار بنجاح',
                'data' => new NewspaperIssueResource($issue)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء الإصدار',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update newspaper issue (Admin)
     */
    public function update(Request $request, $id)
    {
        // التحقق من الصلاحية
        if (!auth()->user()->can('edit_newspaper_issue')) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل إصدارات الصحف'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'newspaper_name' => 'string|max:255',
            'issue_number' => 'integer',
            'description' => 'nullable|string',
            'pdf_url' => 'url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'publication_date' => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $issue = NewspaperIssue::findOrFail($id);
            $data = $validator->validated();

            // Update slug if newspaper name or issue number changed
            if (isset($data['newspaper_name']) || isset($data['issue_number'])) {
                $name = $data['newspaper_name'] ?? $issue->newspaper_name;
                $number = $data['issue_number'] ?? $issue->issue_number;
                $data['slug'] = Str::slug($name . '-' . $number);
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                if ($issue->cover_image) {
                    \Storage::disk('public')->delete($issue->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('newspapers/covers', 'public');
            }

            $issue->update($data);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الإصدار بنجاح',
                'data' => new NewspaperIssueResource($issue)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث الإصدار',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete newspaper issue (Admin)
     */
    public function destroy($id)
    {
        // التحقق من الصلاحية
        if (!auth()->user()->can('delete_newspaper_issue')) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف إصدارات الصحف'
            ], 403);
        }

        try {
            $issue = NewspaperIssue::findOrFail($id);

            if ($issue->cover_image) {
                \Storage::disk('public')->delete($issue->cover_image);
            }

            $issue->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الإصدار بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف الإصدار',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Increment downloads
     */
    public function incrementDownload($id)
    {
        try {
            $issue = NewspaperIssue::findOrFail($id);
            $issue->incrementDownloads();

            return response()->json([
                'success' => true,
                'downloads' => $issue->downloads
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error incrementing downloads'
            ], 500);
        }
    }

    /**
     * Toggle featured status (Admin)
     */
    public function toggleFeatured($id)
    {
        // التحقق من الصلاحية
        if (!auth()->user()->can('feature_newspaper_issue')) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل حالة الإصدار'
            ], 403);
        }

        try {
            $issue = NewspaperIssue::findOrFail($id);
            $issue->update(['is_featured' => !$issue->is_featured]);

            return response()->json([
                'success' => true,
                'message' => $issue->is_featured ? 'تم تمييز الإصدار' : 'تم إلغاء تمييز الإصدار',
                'data' => new NewspaperIssueResource($issue)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تعديل حالة الإصدار',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
