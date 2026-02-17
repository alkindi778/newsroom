<?php

namespace App\Services;

use App\Models\Article;
use App\Models\SiteSetting;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Helpers\MediaHelper;
use App\Jobs\GenerateArticleEmbeddingJob;
use App\Services\NotificationService;
use App\Services\EmbeddingService;
use Illuminate\Support\Facades\Log;

class ArticleService
{
    protected $articleRepository;
    protected $notificationService;
    protected $embeddingService;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        NotificationService $notificationService,
        EmbeddingService $embeddingService
    ) {
        $this->articleRepository = $articleRepository;
        $this->notificationService = $notificationService;
        $this->embeddingService = $embeddingService;
    }

    /**
     * Get articles with filters for admin panel
     */
    public function getArticlesForAdmin(Request $request)
    {
        return $this->articleRepository->getAllWithFilters($request);
    }

    /**
     * Create new article
     */
    public function createArticle(array $data, Request $request): Article
    {
        // Process the data
        $processedData = $this->processArticleData($data, $request);
        
        // Convert status to is_published initially
        if (isset($processedData['status'])) {
            $processedData['is_published'] = ($processedData['status'] === 'published');
            unset($processedData['status']);
        }
        
        // Set user ID
        $processedData['user_id'] = auth()->id();
        
        // تحديد حالة الموافقة بناءً على دور المستخدم
        $isReporter = auth()->user()->hasRole('مراسل صحفي');
        
        // Handle publish action
        if ($request->get('action') === 'publish') {
            // Check if user is a reporter
            if ($isReporter) {
                // المراسل لا يملك صلاحية النشر - يُرسل للموافقة تلقائياً
                $processedData['is_published'] = false;
                $processedData['published_at'] = null;
                $processedData['approval_status'] = 'pending_approval';
                
                // Log the attempt
                \Log::info('ArticleService: مراسل قدّم مقال للموافقة تلقائياً', [
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'article_title' => $data['title'] ?? 'غير محدد',
                    'approval_status' => 'pending_approval'
                ]);
            } else {
                // المدير/المحرر ينشر مباشرة
                $processedData['is_published'] = true;
                $processedData['approval_status'] = 'approved';
                $processedData['approved_at'] = now();
                $processedData['approved_by'] = auth()->id();
                if (empty($processedData['published_at'])) {
                    $processedData['published_at'] = now();
                }
            }
        } else {
            // إذا لم يضغط على "نشر"، يُحفظ كـ draft
            $processedData['is_published'] = false;
            $processedData['published_at'] = null;
            $processedData['approval_status'] = 'draft';
        }

        // Remove temporary keys before saving
        unset($processedData['_media_image_path']);
        
        $article = $this->articleRepository->create($processedData);

        // If permalink style is ID-based, set slug to article ID after creation
        $permalinkStyle = \App\Models\SiteSetting::get('article_permalink_style', 'arabic');
        if ($permalinkStyle === 'id' && empty($article->slug)) {
            $article->slug = (string) $article->id;
            $article->save();
        }
        
        // Handle image upload after article creation
        $this->handleImageUpload($article, $request);
        
        // Generate embedding for the article
        $this->generateArticleEmbedding($article);
        
        // Generate SEO data automatically via Job Queue
        if (empty($article->meta_description) || empty($article->keywords)) {
            \App\Jobs\GenerateArticleSeoJob::dispatch($article);
        }
        
        // إرسال إشعارات
        if ($isReporter && $processedData['approval_status'] === 'pending_approval') {
            // إشعار المحررين بمقال جديد بانتظار الموافقة
            $this->notificationService->notifyArticlePending(
                $article->id,
                $article->title,
                auth()->id()
            );
        } elseif ($processedData['approval_status'] === 'approved' && $processedData['is_published']) {
            // إشعار بمقال جديد منشور
            $this->notificationService->notifyArticleCreated(
                $article->id,
                $article->title,
                auth()->id()
            );
            
            // إطلاق Event لإرسال Push Notifications
            event(new \App\Events\ArticlePublished($article));
            
            // نشر على السوشيال ميديا باستخدام Jobs
            \App\Jobs\ShareToAllPlatforms::dispatch('article', $article->id);
        }

        return $article;
    }

    /**
     * Update existing article
     */
    public function updateArticle(Article $article, array $data, Request $request): bool
    {
        // Process the data
        $processedData = $this->processArticleData($data, $request, $article);
        
        // Handle image removal using Media Library
        if ($request->boolean('remove_image')) {
            MediaHelper::deleteImage($article, MediaHelper::COLLECTION_ARTICLES);
        }

        // Convert status to is_published initially
        if (isset($processedData['status'])) {
            $processedData['is_published'] = ($processedData['status'] === 'published');
            unset($processedData['status']);
        }

        // تحديد حالة الموافقة بناءً على دور المستخدم
        $isReporter = auth()->user()->hasRole('مراسل صحفي');
        
        // Handle publish action
        if ($request->get('action') === 'publish') {
            // Check if user is a reporter
            if ($isReporter) {
                // المراسل لا يملك صلاحية النشر - يُرسل للموافقة تلقائياً
                $processedData['is_published'] = false;
                $processedData['published_at'] = null;
                $processedData['approval_status'] = 'pending_approval';
                
                // Log the attempt
                \Log::info('ArticleService: مراسل قدّم مقال للموافقة بعد التعديل', [
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'article_id' => $article->id,
                    'article_title' => $article->title,
                    'approval_status' => 'pending_approval'
                ]);
            } else {
                // المدير/المحرر ينشر مباشرة
                $processedData['is_published'] = true;
                $processedData['approval_status'] = 'approved';
                $processedData['approved_at'] = now();
                $processedData['approved_by'] = auth()->id();
                if (empty($processedData['published_at']) && !$article->is_published) {
                    $processedData['published_at'] = now();
                }
            }
        }

        // Track if article is being published for the first time
        $wasUnpublished = !$article->is_published;
        
        // Remove temporary keys before saving
        unset($processedData['_media_image_path']);
        
        $result = $this->articleRepository->update($article, $processedData);
        
        // Handle image upload after article update
        if ($result) {
            $this->handleImageUpload($article->fresh(), $request);
            
            // Update embedding for the article
            $this->generateArticleEmbedding($article->fresh());
            
            // Generate SEO data automatically via Job Queue if missing
            $freshArticle = $article->fresh();
            if (empty($freshArticle->meta_description) || empty($freshArticle->keywords)) {
                \App\Jobs\GenerateArticleSeoJob::dispatch($freshArticle);
            }
            
            // إطلاق Event إذا تم نشر المقال للمرة الأولى
            $article->refresh();
            if ($wasUnpublished && $article->is_published && $article->approval_status === 'approved') {
                event(new \App\Events\ArticlePublished($article));
                
                // نشر على السوشيال ميديا
                \App\Jobs\ShareToAllPlatforms::dispatch('article', $article->id);
            }
        }
        
        return $result;
    }

    /**
     * Soft delete article (move to trash)
     */
    public function deleteArticle(Article $article): bool
    {
        \Log::info('ArticleService: نقل خبر إلى سلة المهملات', [
            'article_id' => $article->id,
            'article_title' => $article->title,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name
        ]);

        // Don't delete image on soft delete - keep it for potential restore
        return $article->delete(); // This will soft delete
    }

    /**
     * Force delete article (permanently delete)
     */
    public function forceDeleteArticle(Article $article): bool
    {
        \Log::info('ArticleService: حذف خبر نهائياً', [
            'article_id' => $article->id,
            'article_title' => $article->title,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name
        ]);

        // Delete associated media on force delete
        MediaHelper::clearCollection($article, MediaHelper::COLLECTION_ARTICLES);
        
        // Also delete old storage image if exists (backward compatibility)
        if ($article->image) {
            $this->deleteImage($article->image);
        }

        return $article->forceDelete();
    }

    /**
     * Toggle article status
     */
    public function toggleStatus(Article $article): bool
    {
        // Check if user is trying to publish without permission
        if ($article->status === 'draft' && !auth()->user()->can('publish_articles')) {
            \Log::warning('ArticleService: محاولة تفعيل نشر بدون صلاحية', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'article_id' => $article->id,
                'article_title' => $article->title,
                'current_status' => $article->status
            ]);
            
            // Don't allow toggle to published
            return false;
        }
        
        // Track if article is being published
        $wasUnpublished = !$article->is_published;
        
        $result = $this->articleRepository->toggleStatus($article);
        
        // إطلاق Event إذا تم نشر المقال
        if ($result && $wasUnpublished) {
            $article->refresh();
            if ($article->is_published) {
                event(new \App\Events\ArticlePublished($article));
            }
        }
        
        return $result;
    }

    /**
     * Get article statistics
     */
    public function getStatistics(): array
    {
        return $this->articleRepository->getStatistics();
    }

    /**
     * Search articles
     */
    public function searchArticles(string $query)
    {
        return $this->articleRepository->search($query);
    }

    /**
     * Get recent articles for dashboard
     */
    public function getRecentForDashboard(int $limit = 5)
    {
        return $this->articleRepository->getRecent($limit);
    }

    /**
     * Get published articles for frontend
     */
    public function getPublishedArticles()
    {
        return $this->articleRepository->getPublished();
    }

    /**
     * Get article by slug for frontend
     */
    public function getBySlug(string $slug): ?Article
    {
        return $this->articleRepository->findBySlug($slug);
    }

    /**
     * Get articles by category
     */
    public function getByCategory(int $categoryId)
    {
        return $this->articleRepository->getByCategory($categoryId);
    }

    /**
     * Process article data before saving
     */
    protected function processArticleData(array $data, Request $request, ?Article $existingArticle = null): array
    {
        $permalinkStyle = SiteSetting::get('article_permalink_style', 'arabic');

        // Generate slug if not provided (except for ID style which uses article ID)
        if ($permalinkStyle !== 'id') {
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $existingArticle);
            } else {
                // Ensure slug is unique
                $data['slug'] = $this->ensureUniqueSlug($data['slug'], $existingArticle);
            }
        }

        // Handle image field
        // الصورة قد تكون: URL كامل، مسار نسبي من المكتبة، أو ملف مرفوع
        // نحفظ المسار النسبي مؤقتاً في _media_image_path لاستخدامه في handleImageUpload
        \Log::info('🖼️ processArticleData - معالجة حقل الصورة', [
            'image_isset' => isset($data['image']),
            'image_value' => $data['image'] ?? 'غير موجود',
            'image_type' => isset($data['image']) ? gettype($data['image']) : 'N/A',
            'has_file_upload' => $request->hasFile('image'),
            'request_filled_image' => $request->filled('image'),
            'request_input_image' => $request->input('image', 'غير موجود'),
        ]);

        if (isset($data['image'])) {
            $imageValue = $data['image'];
            
            if (filter_var($imageValue, FILTER_VALIDATE_URL)) {
                // URL كامل - لا نحفظه في حقل image
                \Log::info('🖼️ processArticleData - صورة URL كامل، سيتم حذفها', ['url' => $imageValue]);
                unset($data['image']);
            } elseif (!empty($imageValue) && is_string($imageValue)) {
                // مسار نسبي من مكتبة الوسائط - نحفظه مؤقتاً لربطه بالمقال لاحقاً
                \Log::info('🖼️ processArticleData - مسار نسبي من المكتبة', ['path' => $imageValue]);
                $data['_media_image_path'] = $imageValue;
                unset($data['image']);
            }
        }
        
        // Handle image upload using Media Library
        if ($request->hasFile('image')) {
            // سيتم التعامل مع الصورة في handleImageUpload
            // لا نحتاج لحفظها في الـ data array
            \Log::info('🖼️ processArticleData - ملف مرفوع، سيتم معالجته في handleImageUpload');
            unset($data['image']);
        }

        // Process keywords
        if (!empty($data['keywords'])) {
            $data['keywords'] = $this->processKeywords($data['keywords']);
        }

        // Removed simple meta description generation to rely on AI Job
        // if (empty($data['meta_description']) && !empty($data['content'])) {
        //     $data['meta_description'] = $this->generateMetaDescription($data['content']);
        // }

        return $data;
    }

    protected function generateArabicSlug(string $text): string
    {
        $slug = trim($text);
        $slug = preg_replace('/\s+/u', ' ', $slug);
        $slug = str_replace([' ', '_'], '-', $slug);
        $slug = preg_replace('/[^\p{Arabic}0-9A-Za-z\-]+/u', '', $slug);
        $slug = preg_replace('/-+/u', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    protected function generateSlugBase(string $text): string
    {
        $style = SiteSetting::get('article_permalink_style', 'arabic');

        if ($style === 'english') {
            return Str::slug($text);
        }

        return $this->generateArabicSlug($text);
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug(string $title, ?Article $existingArticle = null): string
    {
        $baseSlug = $this->generateSlugBase($title);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $existingArticle)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Ensure slug is unique
     */
    protected function ensureUniqueSlug(string $slug, ?Article $existingArticle = null): string
    {
        $baseSlug = $this->generateSlugBase($slug);
        $finalSlug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($finalSlug, $existingArticle)) {
            $finalSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $finalSlug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists(string $slug, ?Article $existingArticle = null): bool
    {
        $query = Article::where('slug', $slug);
        
        if ($existingArticle) {
            $query->where('id', '!=', $existingArticle->id);
        }
        
        return $query->exists();
    }

    /**
     * Handle image upload using Media Library
     */
    protected function handleImageUpload(Article $article, Request $request): void
    {
        \Log::info('🖼️ handleImageUpload - بدء معالجة الصورة', [
            'article_id' => $article->id,
            'has_file' => $request->hasFile('image'),
            'request_filled_image' => $request->filled('image'),
            'request_input_image' => $request->input('image', 'غير موجود'),
            'is_string' => is_string($request->input('image')),
        ]);

        if ($request->hasFile('image')) {
            // رفع ملف مباشرة
            \Log::info('🖼️ handleImageUpload - رفع ملف مباشر', [
                'file_name' => $request->file('image')->getClientOriginalName(),
                'file_size' => $request->file('image')->getSize(),
                'mime_type' => $request->file('image')->getMimeType(),
            ]);
            MediaHelper::addImage(
                $article,
                $request->file('image'),
                MediaHelper::COLLECTION_ARTICLES,
                [
                    'alt' => $article->title,
                    'title' => $article->title
                ]
            );
            \Log::info('✅ handleImageUpload - تم رفع الملف بنجاح');
        } elseif ($request->filled('image') && is_string($request->input('image'))) {
            // اختيار صورة من مكتبة الوسائط عبر المسار النسبي
            $imagePath = $request->input('image');
            \Log::info('🖼️ handleImageUpload - ربط صورة من المكتبة', ['path' => $imagePath]);
            $this->attachMediaLibraryImage($article, $imagePath);
        } else {
            \Log::info('⚠️ handleImageUpload - لا يوجد صورة للمعالجة');
        }
    }

    /**
     * ربط صورة من مكتبة الوسائط بالمقال
     */
    protected function attachMediaLibraryImage(Article $article, string $imagePath): void
    {
        try {
            // البحث عن الصورة في جدول media بناءً على file_name
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('file_name', $imagePath)
                ->orWhere('file_name', 'LIKE', '%' . basename($imagePath))
                ->orderBy('created_at', 'desc')
                ->first();

            if ($media) {
                // حذف الصورة القديمة من المقال
                MediaHelper::clearCollection($article, MediaHelper::COLLECTION_ARTICLES);

                // نسخ الصورة من المكتبة وربطها بالمقال
                $fullPath = storage_path('app/public/' . $imagePath);
                
                // إذا كان الملف موجود في storage
                if (file_exists($fullPath)) {
                    $article->addMedia($fullPath)
                        ->preservingOriginal() // نحتفظ بالملف الأصلي في المكتبة
                        ->withCustomProperties([
                            'alt' => $article->title,
                            'title' => $article->title
                        ])
                        ->toMediaCollection(MediaHelper::COLLECTION_ARTICLES);
                    
                    Log::info('تم ربط صورة من المكتبة بالمقال', [
                        'article_id' => $article->id,
                        'media_path' => $imagePath,
                        'original_media_id' => $media->id
                    ]);
                } else {
                    Log::warning('ملف الصورة غير موجود في المسار المتوقع', [
                        'article_id' => $article->id,
                        'expected_path' => $fullPath,
                        'media_path' => $imagePath
                    ]);
                }
            } else {
                // إذا لم نجد الصورة في جدول media، نحاول إضافتها مباشرة من المسار
                $fullPath = storage_path('app/public/' . $imagePath);
                if (file_exists($fullPath)) {
                    MediaHelper::clearCollection($article, MediaHelper::COLLECTION_ARTICLES);
                    
                    $article->addMedia($fullPath)
                        ->preservingOriginal()
                        ->withCustomProperties([
                            'alt' => $article->title,
                            'title' => $article->title
                        ])
                        ->toMediaCollection(MediaHelper::COLLECTION_ARTICLES);
                    
                    Log::info('تم إضافة صورة مباشرة من المسار للمقال', [
                        'article_id' => $article->id,
                        'image_path' => $imagePath
                    ]);
                } else {
                    Log::warning('لم يتم العثور على الصورة في المكتبة أو المسار', [
                        'article_id' => $article->id,
                        'image_path' => $imagePath
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('خطأ في ربط صورة من المكتبة بالمقال: ' . $e->getMessage(), [
                'article_id' => $article->id,
                'image_path' => $imagePath,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Delete image from storage (kept for backward compatibility)
     */
    protected function deleteImage(string $imagePath): bool
    {
        return Storage::disk('public')->delete($imagePath);
    }

    /**
     * Process keywords string
     */
    protected function processKeywords(string $keywords): string
    {
        // Clean up keywords: remove extra spaces, ensure consistent comma separation
        $keywordArray = array_map('trim', explode(',', $keywords));
        $keywordArray = array_filter($keywordArray); // Remove empty values
        
        return implode(', ', $keywordArray);
    }

    /**
     * Generate meta description from content
     */
    protected function generateMetaDescription(string $content): string
    {
        $cleanContent = strip_tags($content);
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        
        // Limit to 160 characters max (validation allows 500 but SEO best practice is 160)
        return Str::limit(trim($cleanContent), 160);
    }

    /**
     * Bulk operations
     */
    public function bulkStatusUpdate(array $articleIds, string $status): int
    {
        // Check permission for publishing
        if ($status === 'published' && !auth()->user()->can('publish_articles')) {
            \Log::warning('ArticleService: محاولة نشر جماعي بدون صلاحية', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'action' => 'bulk_publish_without_permission',
                'article_count' => count($articleIds)
            ]);
            
            // Force to draft status
            $status = 'draft';
        }
        
        $updateData = ['is_published' => ($status === 'published')];
        
        if ($status === 'published') {
            $updateData['published_at'] = now();
        } else {
            $updateData['published_at'] = null;
        }
        
        return Article::whereIn('id', $articleIds)->update($updateData);
    }

    /**
     * Bulk soft delete (move to trash)
     */
    public function bulkDelete(array $articleIds): int
    {
        \Log::info('ArticleService: نقل أخبار متعددة إلى سلة المهملات', [
            'article_ids' => $articleIds,
            'count' => count($articleIds),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name
        ]);

        // Don't delete images on soft delete - keep them for potential restore
        return Article::whereIn('id', $articleIds)->delete(); // This will soft delete
    }

    /**
     * Bulk force delete (permanently delete)
     */
    public function bulkForceDelete(array $articleIds): int
    {
        \Log::info('ArticleService: حذف أخبار متعددة نهائياً', [
            'article_ids' => $articleIds,
            'count' => count($articleIds),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name
        ]);

        // Get articles to delete their images
        $articles = Article::onlyTrashed()->whereIn('id', $articleIds)->get();
        
        // Delete images on force delete
        foreach ($articles as $article) {
            if ($article->image) {
                $this->deleteImage($article->image);
            }
        }
        
        return Article::onlyTrashed()->whereIn('id', $articleIds)->forceDelete();
    }

    /**
     * Duplicate article
     */
    public function duplicateArticle(Article $article): Article
    {
        $newArticle = $article->replicate();
        $newArticle->title = $article->title . ' - نسخة';
        $newArticle->slug = $this->generateUniqueSlug($newArticle->title);
        $newArticle->status = 'draft';
        $newArticle->published_at = null;
        $newArticle->user_id = auth()->id();
        $newArticle->save();
        
        return $newArticle;
    }

    /**
     * Submit article for approval (for reporters)
     */
    public function submitForApproval(Article $article): bool
    {
        \Log::info('ArticleService: تقديم مقال للموافقة', [
            'article_id' => $article->id,
            'article_title' => $article->title,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name
        ]);

        $updated = $article->update([
            'approval_status' => 'pending_approval',
            'is_published' => false, // لا ينشر حتى تتم الموافقة
            'published_at' => null,
        ]);

        if ($updated) {
            \Log::info('ArticleService: تم تقديم المقال للموافقة بنجاح', [
                'article_id' => $article->id
            ]);
            
            // إشعار المحررين بمقال جديد بانتظار الموافقة
            $this->notificationService->notifyArticlePending(
                $article->id,
                $article->title,
                auth()->id()
            );
        }

        return $updated;
    }

    /**
     * Approve article and publish it
     */
    public function approveArticle(Article $article): bool
    {
        if (!auth()->user()->can('الموافقة على المقالات')) {
            \Log::warning('ArticleService: محاولة الموافقة بدون صلاحية', [
                'user_id' => auth()->id(),
                'article_id' => $article->id
            ]);
            return false;
        }

        \Log::info('ArticleService: الموافقة على مقال', [
            'article_id' => $article->id,
            'article_title' => $article->title,
            'approved_by' => auth()->id(),
            'approved_by_name' => auth()->user()->name
        ]);

        $updated = $article->update([
            'approval_status' => 'approved',
            'is_published' => true,
            'published_at' => now(),
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
            'rejected_at' => null,
            'rejected_by' => null,
        ]);

        if ($updated) {
            \Log::info('ArticleService: تم نشر المقال بعد الموافقة', [
                'article_id' => $article->id
            ]);
            
            // إشعار الكاتب بالموافقة على مقاله
            $this->notificationService->notifyArticleApproved(
                $article->id,
                $article->title,
                $article->user_id,
                auth()->id()
            );
        }

        return $updated;
    }

    /**
     * Reject article with reason
     */
    public function rejectArticle(Article $article, string $reason): bool
    {
        if (!auth()->user()->can('رفض المقالات')) {
            \Log::warning('ArticleService: محاولة الرفض بدون صلاحية', [
                'user_id' => auth()->id(),
                'article_id' => $article->id
            ]);
            return false;
        }

        \Log::info('ArticleService: رفض مقال', [
            'article_id' => $article->id,
            'article_title' => $article->title,
            'rejected_by' => auth()->id(),
            'rejected_by_name' => auth()->user()->name,
            'reason' => $reason
        ]);

        $updated = $article->update([
            'approval_status' => 'rejected',
            'is_published' => false,
            'published_at' => null,
            'rejection_reason' => $reason,
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        if ($updated) {
            \Log::info('ArticleService: تم رفض المقال', [
                'article_id' => $article->id
            ]);
            
            // إشعار الكاتب برفض مقاله
            $this->notificationService->notifyArticleRejected(
                $article->id,
                $article->title,
                $article->user_id,
                auth()->id(),
                $reason
            );
        }

        return $updated;
    }

    /**
     * Get pending approval articles
     */
    public function getPendingArticles()
    {
        return $this->articleRepository->getPendingApproval();
    }

    /**
     * Check if user can edit article
     */
    public function canUserEditArticle(Article $article, $user): bool
    {
        // Super Admin و المدراء يمكنهم التعديل دائماً
        if ($user->can('manage_articles')) {
            return true;
        }

        // صاحب المقال يمكنه التعديل فقط إذا كان pending أو draft
        if ($article->user_id === $user->id) {
            return in_array($article->approval_status, ['draft', 'pending_approval', 'rejected']);
        }

        return false;
    }

    /**
     * Check if user can delete article
     */
    public function canUserDeleteArticle(Article $article, $user): bool
    {
        // Super Admin و المدراء يمكنهم الحذف دائماً
        if ($user->can('manage_articles')) {
            return true;
        }

        // المراسل لا يمكنه حذف المقالات المنشورة
        if ($article->is_published || $article->isApproved()) {
            return false;
        }

        // يمكنه حذف مقالاته الخاصة إذا لم تكن منشورة
        return $article->user_id === $user->id;
    }

    /**
     * Generate embedding for an article
     */
    /**
     * Generate embedding for an article
     */
    private function generateArticleEmbedding(Article $article): void
    {
        try {
            // Try using EmbeddingService directly since the Job class may not exist
            if ($this->embeddingService) {
                $this->embeddingService->generateEmbedding($article);
            }
        } catch (\Exception $e) {
            // Don't let embedding failures prevent article creation
            \Log::warning('فشل توليد embedding للمقال', [
                'article_id' => $article->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
