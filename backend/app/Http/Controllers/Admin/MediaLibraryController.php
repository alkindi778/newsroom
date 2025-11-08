<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Helpers\MediaHelper;
use App\Models\Article;
use App\Models\Writer;
use App\Models\Opinion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class MediaLibraryController extends Controller
{
    /**
     * Display the media library (Grid & List views)
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'grid'); // grid or list
        $search = $request->get('search', '');
        $type = $request->get('type', 'all'); // image, video, audio, document
        $collection = $request->get('collection', 'all');
        $perPage = $request->get('per_page', 20);

        // Build query
        $query = Media::query()
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('file_name', 'LIKE', "%{$search}%")
                  ->orWhereJsonContains('custom_properties->alt', $search)
                  ->orWhereJsonContains('custom_properties->title', $search);
            });
        }

        // Type filter
        if ($type !== 'all') {
            switch ($type) {
                case 'image':
                    $query->where('mime_type', 'LIKE', 'image/%');
                    break;
                case 'video':
                    $query->where('mime_type', 'LIKE', 'video/%');
                    break;
                case 'audio':
                    $query->where('mime_type', 'LIKE', 'audio/%');
                    break;
                case 'document':
                    $query->whereNotIn('mime_type', function($subQuery) {
                        $subQuery->select('mime_type')
                                 ->from('media')
                                 ->where('mime_type', 'LIKE', 'image/%')
                                 ->orWhere('mime_type', 'LIKE', 'video/%')
                                 ->orWhere('mime_type', 'LIKE', 'audio/%');
                    });
                    break;
            }
        }

        // Collection filter
        if ($collection !== 'all') {
            $query->where('collection_name', $collection);
        }

        $media = $query->paginate($perPage);

        // Add fixed URLs to each media item
        $media->getCollection()->transform(function($item) {
            $item->fixed_url = $this->buildMediaUrl($item->file_name, $item->collection_name);
            return $item;
        });

        // Get statistics
        $stats = $this->getMediaStats();

        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats,
                'view' => 'admin.media-library.partials.' . $view
            ]);
        }

        return view('admin.media-library.index', compact('media', 'stats', 'view'));
    }

    /**
     * API endpoint for media picker
     */
    public function api(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search', '');
            $type = $request->get('type', 'all');
            $collection = $request->get('collection', 'all');
            $perPage = min($request->get('per_page', 10), 50); // Default 10
            $page = $request->get('page', 1);

            // Build query
            $query = Media::query()
                ->orderBy('created_at', 'desc');

            // Search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('file_name', 'LIKE', "%{$search}%");
                });
            }

            // Type filter
            if ($type !== 'all') {
                switch ($type) {
                    case 'image':
                        $query->where('mime_type', 'LIKE', 'image/%');
                        break;
                    case 'video':
                        $query->where('mime_type', 'LIKE', 'video/%');
                        break;
                    case 'audio':
                        $query->where('mime_type', 'LIKE', 'audio/%');
                        break;
                    case 'document':
                        $query->where('mime_type', 'NOT LIKE', 'image/%')
                              ->where('mime_type', 'NOT LIKE', 'video/%')
                              ->where('mime_type', 'NOT LIKE', 'audio/%');
                        break;
                }
            }

            // Collection filter
            if ($collection !== 'all') {
                $query->where('collection_name', $collection);
            }

            // Use pagination instead of limit
            $mediaPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            // Format media data for API
            $mediaData = $mediaPaginated->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'file_name' => $item->file_name,
                    'mime_type' => $item->mime_type,
                    'size' => $item->size,
                    'human_readable_size' => $this->formatBytes($item->size),
                    'collection_name' => $item->collection_name,
                    'url' => $this->buildMediaUrl($item->file_name, $item->collection_name),
                    'thumbnail_url' => $this->buildMediaUrl($item->file_name, $item->collection_name),
                    'custom_properties' => $item->custom_properties,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'media' => $mediaData,
                'pagination' => [
                    'current_page' => $mediaPaginated->currentPage(),
                    'last_page' => $mediaPaginated->lastPage(),
                    'per_page' => $mediaPaginated->perPage(),
                    'total' => $mediaPaginated->total(),
                    'from' => $mediaPaginated->firstItem(),
                    'to' => $mediaPaginated->lastItem()
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب الوسائط',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display media picker modal
     */
    public function picker(Request $request)
    {
        $mode = $request->get('mode', 'select'); // select or upload
        $collection = $request->get('collection', 'all');
        $multiple = $request->boolean('multiple', false);
        $field = $request->get('field', 'image'); // field name to update

        $media = Media::query()
            ->when($collection !== 'all', function($q) use ($collection) {
                $q->where('collection_name', $collection);
            })
            ->where('mime_type', 'LIKE', 'image/%')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.media-library.picker', compact('media', 'mode', 'collection', 'multiple', 'field'));
    }

    /**
     * Upload new media file
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|max:10240|mimes:jpeg,png,jpg,gif,webp,svg,pdf,doc,docx,txt', // 10MB max per file
            'collection' => 'sometimes|string',
            'alt' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $files = $request->file('files');
            $collection = $request->get('collection', 'library');
            $alt = $request->get('alt', '');
            $title = $request->get('title', '');
            $customName = $request->get('name', ''); // اسم مخصص للصورة
            
            $uploadedMedia = [];

            foreach ($files as $index => $file) {
                // Store file using Laravel's storage system first
                $path = $file->store('media/' . $collection, 'public');
                $filename = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $size = $file->getSize();

                // بيانات الضغط
                $compressionData = null;
                
                // ضغط الصورة مباشرة بعد الحفظ
                if (str_starts_with($mimeType, 'image/')) {
                    $fullPath = storage_path('app/public/' . $path);
                    $originalSize = filesize($fullPath);
                    
                    $optimizer = app(\App\Services\ImageOptimizerService::class);
                    if ($optimizer->optimizeImage($fullPath)) {
                        $size = filesize($fullPath); // تحديث الحجم بعد الضغط
                        $newSize = $size;
                        $saved = $originalSize - $newSize;
                        $reduction = $originalSize > 0 ? round((($originalSize - $newSize) / $originalSize) * 100, 2) : 0;
                        
                        $compressionData = [
                            'original_size' => $originalSize,
                            'new_size' => $newSize,
                            'saved' => $saved,
                            'reduction' => $reduction,
                            'compressed' => true
                        ];
                    }
                }

                // استخدام الاسم المخصص أو title أو الاسم الأصلي
                $mediaName = $customName ?: ($title ?: pathinfo($filename, PATHINFO_FILENAME));
                $mediaAlt = $alt ?: $mediaName;
                $mediaTitle = $title ?: $mediaName;

                // Create media record directly in database
                $media = Media::create([
                    'model_type' => 'App\\Models\\TempMedia',
                    'model_id' => 0, // No specific model
                    'collection_name' => $collection,
                    'name' => $mediaName, // استخدام الاسم المخصص
                    'file_name' => $path, // المسار الكامل بدلاً من basename فقط
                    'mime_type' => $mimeType,
                    'disk' => 'public',
                    'conversions_disk' => 'public',
                    'size' => $size,
                    'manipulations' => [],
                    'custom_properties' => [
                        'alt' => $mediaAlt,
                        'title' => $mediaTitle,
                        'uploaded_by' => auth()->id(),
                        'uploaded_at' => now()->toISOString()
                    ],
                    'generated_conversions' => [],
                    'responsive_images' => [],
                    'order_column' => 1,
                    'uuid' => Str::uuid()->toString(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $mediaData = [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'human_readable_size' => $this->formatBytes($media->size),
                    'url' => $this->buildMediaUrl($media->file_name, $media->collection_name),
                    'thumbnail_url' => $this->buildMediaUrl($media->file_name, $media->collection_name),
                    'custom_properties' => $media->custom_properties,
                    'created_at' => $media->created_at->format('Y-m-d H:i:s')
                ];
                
                // إضافة بيانات الضغط إذا كانت موجودة
                if ($compressionData) {
                    $mediaData['compression'] = [
                        'original_size' => $this->formatBytes($compressionData['original_size']),
                        'new_size' => $this->formatBytes($compressionData['new_size']),
                        'saved' => $this->formatBytes($compressionData['saved']),
                        'reduction' => $compressionData['reduction'] . '%',
                        'compressed' => true
                    ];
                }
                
                $uploadedMedia[] = $mediaData;
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedMedia) > 1 ? 'تم رفع ' . count($uploadedMedia) . ' ملفات بنجاح' : 'تم رفع الملف بنجاح',
                'media' => $uploadedMedia
            ]);

        } catch (Exception $e) {
            \Log::error('MediaLibrary Upload: خطأ في الرفع', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطأ في رفع الملف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get media details
     */
    public function show($id): JsonResponse
    {
        try {
            $media = Media::findOrFail($id);
            
            $details = [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'human_readable_size' => $media->humanReadableSize,
                'collection_name' => $media->collection_name,
                'url' => $media->getUrl(),
                'custom_properties' => $media->custom_properties,
                'created_at' => $media->created_at,
                'updated_at' => $media->updated_at,
                'conversions' => []
            ];

            // Add conversion URLs if they exist
            $conversions = ['thumbnail', 'medium', 'large', 'hero'];
            foreach ($conversions as $conversion) {
                if ($media->hasGeneratedConversion($conversion)) {
                    $details['conversions'][$conversion] = $media->getUrl($conversion);
                }
            }

            // Get usage information
            $details['usage'] = $this->getMediaUsage($media);

            return response()->json([
                'success' => true,
                'media' => $details
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب تفاصيل الملف'
            ], 404);
        }
    }

    /**
     * Update media details
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'alt' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $media = Media::findOrFail($id);
            
            // Update basic fields
            if ($request->has('name')) {
                $media->name = $request->get('name');
            }

            // Update custom properties
            $customProperties = $media->custom_properties;
            
            if ($request->has('alt')) {
                $customProperties['alt'] = $request->get('alt');
            }
            
            if ($request->has('title')) {
                $customProperties['title'] = $request->get('title');
            }
            
            if ($request->has('description')) {
                $customProperties['description'] = $request->get('description');
            }

            $media->custom_properties = $customProperties;
            $media->save();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الملف بنجاح',
                'media' => $media
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث الملف'
            ], 500);
        }
    }

    /**
     * Delete media file
     */
    public function destroy($id): JsonResponse
    {
        try {
            $media = Media::findOrFail($id);
            
            // Check if media is being used
            $usage = $this->getMediaUsage($media);
            if (!empty($usage)) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف الملف لأنه مستخدم في: ' . implode(', ', array_keys($usage))
                ], 400);
            }

            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الملف بنجاح'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف الملف'
            ], 500);
        }
    }

    /**
     * Bulk actions on media files
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,update_collection',
            'media_ids' => 'required|array',
            'media_ids.*' => 'integer|exists:media,id',
            'collection' => 'required_if:action,update_collection|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $action = $request->get('action');
            $mediaIds = $request->get('media_ids');
            $results = [];

            foreach ($mediaIds as $mediaId) {
                $media = Media::find($mediaId);
                if (!$media) continue;

                switch ($action) {
                    case 'delete':
                        $usage = $this->getMediaUsage($media);
                        if (empty($usage)) {
                            $media->delete();
                            $results['deleted'][] = $mediaId;
                        } else {
                            $results['skipped'][] = $mediaId;
                        }
                        break;

                    case 'update_collection':
                        $media->collection_name = $request->get('collection');
                        $media->save();
                        $results['updated'][] = $mediaId;
                        break;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تنفيذ العملية بنجاح',
                'results' => $results
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تنفيذ العملية'
            ], 500);
        }
    }

    /**
     * Get media statistics
     */
    private function getMediaStats(): array
    {
        return [
            'total' => Media::count(),
            'images' => Media::where('mime_type', 'LIKE', 'image/%')->count(),
            'videos' => Media::where('mime_type', 'LIKE', 'video/%')->count(),
            'audio' => Media::where('mime_type', 'LIKE', 'audio/%')->count(),
            'documents' => Media::whereNotIn('mime_type', function($query) {
                $query->select('mime_type')
                      ->from('media')
                      ->where('mime_type', 'LIKE', 'image/%')
                      ->orWhere('mime_type', 'LIKE', 'video/%')
                      ->orWhere('mime_type', 'LIKE', 'audio/%');
            })->count(),
            'collections' => Media::selectRaw('collection_name, COUNT(*) as count')
                                 ->groupBy('collection_name')
                                 ->pluck('count', 'collection_name')
                                 ->toArray()
        ];
    }

    /**
     * Get media usage across models
     */
    private function getMediaUsage(Media $media): array
    {
        $usage = [];

        // Check in Articles
        $articles = Article::whereHas('media', function($q) use ($media) {
            $q->where('id', $media->id);
        })->get(['id', 'title']);
        
        if ($articles->count() > 0) {
            $usage['مقالات'] = $articles->pluck('title')->toArray();
        }

        // Check in Writers
        $writers = Writer::whereHas('media', function($q) use ($media) {
            $q->where('id', $media->id);
        })->get(['id', 'name']);

        if ($writers->count() > 0) {
            $usage['كُتاب'] = $writers->pluck('name')->toArray();
        }

        // Check in Opinions
        $opinions = Opinion::whereHas('media', function($q) use ($media) {
            $q->where('id', $media->id);
        })->get(['id', 'title']);

        if ($opinions->count() > 0) {
            $usage['مقالات رأي'] = $opinions->pluck('title')->toArray();
        }

        // Check in Users
        $users = User::whereHas('media', function($q) use ($media) {
            $q->where('id', $media->id);
        })->get(['id', 'name']);

        if ($users->count() > 0) {
            $usage['مستخدمين'] = $users->pluck('name')->toArray();
        }

        return $usage;
    }

    /**
     * Build media URL based on file path structure
     */
    private function buildMediaUrl($fileName, $collection = null)
    {
        // إذا كان المسار يحتوي على "/" فهو مسار كامل (الصور الجديدة)
        if (str_contains($fileName, '/')) {
            return rtrim(url('/'), '/') . '/storage/' . $fileName;
        }
        
        // إذا لم يحتوي على "/" فهو اسم ملف فقط (الصور القديمة)
        // نبحث عن الصورة في المجلدات المرقمة
        $storagePath = public_path('storage');
        
        for ($i = 1; $i <= 20; $i++) {
            $fullPath = $storagePath . '/' . $i . '/' . $fileName;
            
            if (file_exists($fullPath)) {
                return rtrim(url('/'), '/') . '/storage/' . $i . '/' . $fileName;
            }
        }
        
        // إذا لم نجدها، نفترض أنها في collection folder
        if ($collection && $collection !== 'default') {
            return rtrim(url('/'), '/') . '/storage/media/' . $collection . '/' . $fileName;
        }
        
        // الافتراضي
        return rtrim(url('/'), '/') . '/storage/' . $fileName;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) 
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
