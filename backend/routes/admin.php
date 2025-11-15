<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\WriterController;
use App\Http\Controllers\Admin\OpinionController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\NewspaperIssueController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\SocialMediaController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Fortify handles authentication routes automatically with /admin prefix
*/

// Authenticated admin routes
Route::middleware(['auth', App\Http\Middleware\AdminMiddleware::class])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');
    });
    
    // Logout is handled by Fortify
    
    // Articles Management
    // ملاحظة: routes المحددة يجب أن تأتي قبل resource routes
    
    // Article Approval Routes (يجب أن تأتي قبل resource routes!)
    Route::middleware(['permission:عرض المقالات المعلقة'])->group(function () {
        Route::get('articles/pending', [ArticleController::class, 'pending'])->name('articles.pending');
    });
    
    Route::middleware(['permission:create_articles'])->group(function () {
        Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
    });
    Route::middleware(['permission:view_articles'])->group(function () {
        Route::resource('articles', ArticleController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit_articles'])->group(function () {
        Route::get('articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
        Route::patch('articles/{article}', [ArticleController::class, 'update']);
    });
    Route::middleware(['permission:delete_articles'])->group(function () {
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    });
    
    // Additional Article Routes
    Route::middleware(['permission:publish_articles'])->group(function () {
        Route::patch('articles/{article}/toggle-status', [ArticleController::class, 'toggleStatus'])->name('articles.toggle-status');
    });
    Route::middleware(['permission:manage_articles'])->group(function () {
        Route::prefix('articles')->name('articles.')->group(function () {
            Route::post('bulk-action', [ArticleController::class, 'bulkAction'])->name('bulk-action');
            Route::post('{article}/duplicate', [ArticleController::class, 'duplicate'])->name('duplicate');
            Route::get('search', [ArticleController::class, 'search'])->name('search');
        });
    });
    
    // More Approval Routes
    Route::middleware(['permission:تقديم المقالات للموافقة'])->group(function () {
        Route::post('articles/{article}/submit-for-approval', [ArticleController::class, 'submitForApproval'])->name('articles.submit-for-approval');
    });
    Route::middleware(['permission:الموافقة على المقالات'])->group(function () {
        Route::post('articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    });
    Route::middleware(['permission:رفض المقالات'])->group(function () {
        Route::post('articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');
    });
    
    // Categories Management
    // ملاحظة: routes المحددة يجب أن تأتي قبل resource routes
    Route::middleware(['permission:create_categories'])->group(function () {
        Route::get('categories/create', function() {
            \Log::info('Route: وصول لـ categories/create', [
                'user_id' => auth()->id(),
                'can_create' => auth()->user()->can('create_categories'),
                'middleware_passed' => true
            ]);
            return app(CategoryController::class)->create();
        })->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    });
    Route::middleware(['permission:view_categories'])->group(function () {
        Route::resource('categories', CategoryController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit_categories'])->group(function () {
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::patch('categories/{category}', [CategoryController::class, 'update']);
    });
    Route::middleware(['permission:delete_categories'])->group(function () {
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
    
    // Additional Category Routes
    Route::middleware(['permission:edit_categories'])->group(function () {
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::patch('{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('bulk-action', [CategoryController::class, 'bulkAction'])->name('bulk-action');
            Route::post('update-order', [CategoryController::class, 'updateOrder'])->name('update-order');
            Route::get('search', [CategoryController::class, 'search'])->name('search');
            Route::get('statistics', [CategoryController::class, 'getStatistics'])->name('statistics');
            Route::get('{category}/insights', [CategoryController::class, 'getInsights'])->name('insights');
            Route::get('list', [CategoryController::class, 'getCategories'])->name('list');
        });
    });
    
    // Trash Management (Soft Deleted Articles, Videos & Opinions)
    Route::middleware(['permission:manage_trash|delete_articles'])->group(function () {
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\TrashController::class, 'index'])->name('index');
            Route::get('/{id}/restore', [\App\Http\Controllers\Admin\TrashController::class, 'restore'])->name('restore');
            Route::post('/bulk-restore', [\App\Http\Controllers\Admin\TrashController::class, 'bulkRestore'])->name('bulk-restore');
            // Video restore
            Route::get('/video/{id}/restore', [\App\Http\Controllers\Admin\TrashController::class, 'restoreVideo'])->name('video.restore');
            // Opinion restore
            Route::get('/opinion/{id}/restore', [\App\Http\Controllers\Admin\TrashController::class, 'restoreOpinion'])->name('opinion.restore');
        });
    });
    
    // Force Delete Operations - Require higher permissions
    Route::middleware(['permission:force_delete_articles|manage_trash'])->group(function () {
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::get('/{id}/force-delete', [\App\Http\Controllers\Admin\TrashController::class, 'forceDelete'])->name('force-delete');
            Route::post('/bulk-force-delete', [\App\Http\Controllers\Admin\TrashController::class, 'bulkForceDelete'])->name('bulk-force-delete');
            Route::post('/empty', [\App\Http\Controllers\Admin\TrashController::class, 'emptyTrash'])->name('empty');
            // Video force delete
            Route::get('/video/{id}/force-delete', [\App\Http\Controllers\Admin\TrashController::class, 'forceDeleteVideo'])->name('video.force-delete');
            // Opinion force delete
            Route::get('/opinion/{id}/force-delete', [\App\Http\Controllers\Admin\TrashController::class, 'forceDeleteOpinion'])->name('opinion.force-delete');
        });
    });
    
    // Users Management
        Route::middleware(['permission:create_users'])->group(function () {
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    });
    Route::middleware(['permission:view_users'])->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'show']);
    });
    Route::middleware(['permission:edit_users'])->group(function () {
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update']);
    });
    Route::middleware(['permission:delete_users'])->group(function () {
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    });
    Route::middleware(['permission:manage_users'])->group(function () {
        Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/bulk-action', [\App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('users.bulk-action');
    });
    
    // Roles & Permissions Management
    Route::middleware(['permission:manage_roles'])->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    });
    Route::middleware(['permission:manage_permissions'])->group(function () {
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    });

    // Writers Management (كُتاب الرأي)
    // NOTE: ضع مسار الإنشاء قبل المسار البارامتري لتجنب التقاط "create" كـ {writer}
    Route::middleware(['permission:إنشاء كُتاب الرأي'])->group(function () {
        Route::get('writers/create', [WriterController::class, 'create'])->name('writers.create');
        Route::post('writers', [WriterController::class, 'store'])->name('writers.store');
    });
    Route::middleware(['permission:عرض كُتاب الرأي'])->group(function () {
        Route::get('writers', [WriterController::class, 'index'])->name('writers.index');
        Route::get('writers/{writer}', [WriterController::class, 'show'])->name('writers.show');
        Route::get('writers/{writer}/opinions', [WriterController::class, 'opinions'])->name('writers.opinions');
    });
    Route::middleware(['permission:تعديل كُتاب الرأي'])->group(function () {
        Route::get('writers/{writer}/edit', [WriterController::class, 'edit'])->name('writers.edit');
        Route::put('writers/{writer}', [WriterController::class, 'update'])->name('writers.update');
        Route::patch('writers/{writer}', [WriterController::class, 'update']);
    });
    Route::middleware(['permission:حذف كُتاب الرأي'])->group(function () {
        Route::delete('writers/{writer}', [WriterController::class, 'destroy'])->name('writers.destroy');
    });
    Route::middleware(['permission:إدارة كُتاب الرأي'])->group(function () {
        Route::patch('writers/{writer}/toggle-status', [WriterController::class, 'toggleStatus'])->name('writers.toggle-status');
        Route::post('writers/bulk-action', [WriterController::class, 'bulkAction'])->name('writers.bulk-action');
    });

    // Opinions Management (مقالات الرأي)
    // NOTE: ضع مسار الإنشاء قبل المسار البارامتري لتجنب التقاط "create" كـ {opinion}
    Route::middleware(['permission:إنشاء مقالات الرأي'])->group(function () {
        Route::get('opinions/create', [OpinionController::class, 'create'])->name('opinions.create');
        Route::post('opinions', [OpinionController::class, 'store'])->name('opinions.store');
    });
    Route::middleware(['permission:عرض مقالات الرأي'])->group(function () {
        Route::get('opinions', [OpinionController::class, 'index'])->name('opinions.index');
        Route::get('opinions/{opinion}', [OpinionController::class, 'show'])->name('opinions.show');
    });
    
    // 🧪 TEST ROUTE - بدون middleware
    Route::get('test-opinions-create', [OpinionController::class, 'create'])->name('test.opinions.create');
    Route::middleware(['permission:تعديل مقالات الرأي'])->group(function () {
        Route::get('opinions/{opinion}/edit', [OpinionController::class, 'edit'])->name('opinions.edit');
        Route::put('opinions/{opinion}', [OpinionController::class, 'update'])->name('opinions.update');
        Route::patch('opinions/{opinion}', [OpinionController::class, 'update']);
    });
    Route::middleware(['permission:حذف مقالات الرأي'])->group(function () {
        Route::delete('opinions/{opinion}', [OpinionController::class, 'destroy'])->name('opinions.destroy');
        Route::post('opinions/{id}/restore', [OpinionController::class, 'restore'])->name('opinions.restore');
        Route::delete('opinions/{id}/force-delete', [OpinionController::class, 'forceDelete'])->name('opinions.force-delete');
    });
    Route::middleware(['permission:نشر مقالات الرأي'])->group(function () {
        Route::patch('opinions/{opinion}/toggle-status', [OpinionController::class, 'toggleStatus'])->name('opinions.toggle-status');
    });
    Route::middleware(['permission:إدارة مقالات الرأي'])->group(function () {
        Route::patch('opinions/{opinion}/toggle-featured', [OpinionController::class, 'toggleFeatured'])->name('opinions.toggle-featured');
        Route::post('opinions/bulk-action', [OpinionController::class, 'bulkAction'])->name('opinions.bulk-action');
    });

    // Media Library Routes
    Route::prefix('media')->name('media.')->group(function () {
        // Main media library page
        Route::get('/', [MediaLibraryController::class, 'index'])->name('index');
        
        // API endpoint for media picker
        Route::get('/api', [MediaLibraryController::class, 'api'])->name('api');
        
        // Media picker modal (WordPress style)
        Route::get('/picker', [MediaLibraryController::class, 'picker'])->name('picker');
        
        // Upload new media
        Route::post('/upload', [MediaLibraryController::class, 'upload'])->name('upload');
        
        // Media management
        Route::get('/{media}', [MediaLibraryController::class, 'show'])->name('show');
        Route::put('/{media}', [MediaLibraryController::class, 'update'])->name('update');
        Route::delete('/{media}', [MediaLibraryController::class, 'destroy'])->name('destroy');
        
        // Bulk actions
        Route::post('/bulk-action', [MediaLibraryController::class, 'bulkAction'])->name('bulk-action');
    });

    // Videos Management
    Route::prefix('videos')->name('videos.')->group(function () {
        // List and create
        Route::get('/', [VideoController::class, 'index'])->name('index');
        Route::get('/create', [VideoController::class, 'create'])->name('create');
        Route::post('/', [VideoController::class, 'store'])->name('store');
        
        // Update section title
        Route::post('/update-section-title', [VideoController::class, 'updateSectionTitle'])->name('update-section-title');
        
        // Fetch video info from URL
        Route::post('/fetch-info', [VideoController::class, 'fetchVideoInfo'])->name('fetch-info');
        
        // Show, edit, update, delete
        Route::get('/{video}', [VideoController::class, 'show'])->name('show');
        Route::get('/{video}/edit', [VideoController::class, 'edit'])->name('edit');
        Route::put('/{video}', [VideoController::class, 'update'])->name('update');
        Route::delete('/{video}', [VideoController::class, 'destroy'])->name('destroy');
        
        // Publishing actions
        Route::put('/{video}/publish', [VideoController::class, 'publish'])->name('publish');
        Route::put('/{video}/unpublish', [VideoController::class, 'unpublish'])->name('unpublish');
        Route::put('/{video}/toggle-featured', [VideoController::class, 'toggleFeatured'])->name('toggle-featured');
        
        // Bulk actions
        Route::post('/bulk-action', [VideoController::class, 'bulkAction'])->name('bulk-action');
    });

    // Newspaper Issues Management
    Route::prefix('newspaper-issues')->name('newspaper-issues.')->group(function () {
        // List and create
        Route::get('/', [NewspaperIssueController::class, 'index'])->name('index');
        Route::get('/create', [NewspaperIssueController::class, 'create'])->name('create');
        Route::post('/', [NewspaperIssueController::class, 'store'])->name('store');

        // Show single issue
        Route::get('/{issue}', [NewspaperIssueController::class, 'show'])->name('show');

        // Edit & update
        Route::get('/{issue}/edit', [NewspaperIssueController::class, 'edit'])->name('edit');
        Route::put('/{issue}', [NewspaperIssueController::class, 'update'])->name('update');

        // Toggle featured
        Route::put('/{issue}/toggle-featured', [NewspaperIssueController::class, 'toggleFeatured'])->name('toggle-featured');

        // Delete
        Route::delete('/{issue}', [NewspaperIssueController::class, 'destroy'])->name('destroy');
    });
    
    // Security Settings (2FA, Password, etc.)
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [SecurityController::class, 'index'])->name('index');
        Route::put('/password', [SecurityController::class, 'updatePassword'])->name('update-password');
        
        // Two Factor Authentication
        Route::post('/2fa/enable', [SecurityController::class, 'enableTwoFactor'])->name('enable-2fa');
        Route::post('/2fa/confirm', [SecurityController::class, 'confirmTwoFactor'])->name('confirm-2fa');
        Route::delete('/2fa/disable', [SecurityController::class, 'disableTwoFactor'])->name('disable-2fa');
        Route::get('/2fa/qr-code', [SecurityController::class, 'showQrCode'])->name('qr-code');
        Route::get('/2fa/recovery-codes', [SecurityController::class, 'showRecoveryCodes'])->name('recovery-codes');
        Route::post('/2fa/recovery-codes/regenerate', [SecurityController::class, 'regenerateRecoveryCodes'])->name('regenerate-recovery-codes');
    });

    // Site Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'index'])->name('index');
        Route::get('/{key}', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'show'])->name('show');
        Route::post('/', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'store'])->name('store');
        Route::put('/{key}', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'update'])->name('update');
        Route::delete('/{key}', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-update', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // Homepage Sections Management
    Route::middleware(['permission:view_homepage_sections'])->group(function () {
        Route::get('homepage-sections', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'index'])->name('homepage-sections.index');
    });
    
    Route::middleware(['permission:create_homepage_sections'])->group(function () {
        Route::get('homepage-sections/create', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'create'])->name('homepage-sections.create');
        Route::post('homepage-sections', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'store'])->name('homepage-sections.store');
    });
    
    Route::middleware(['permission:edit_homepage_sections'])->group(function () {
        Route::get('homepage-sections/{homepageSection}/edit', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'edit'])->name('homepage-sections.edit');
        Route::put('homepage-sections/{homepageSection}', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'update'])->name('homepage-sections.update');
        Route::patch('homepage-sections/{homepageSection}/toggle-status', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'toggleStatus'])->name('homepage-sections.toggle-status');
    });
    
    Route::middleware(['permission:delete_homepage_sections'])->group(function () {
        Route::delete('homepage-sections/{homepageSection}', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'destroy'])->name('homepage-sections.destroy');
    });
    
    Route::middleware(['permission:manage_homepage_sections'])->group(function () {
        Route::post('homepage-sections/update-order', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'updateOrder'])->name('homepage-sections.update-order');
    });

    // Advertisements Management
    Route::middleware(['permission:view_advertisements'])->group(function () {
        Route::get('advertisements', [AdvertisementController::class, 'index'])->name('advertisements.index');
        Route::get('advertisements/statistics/overview', [AdvertisementController::class, 'statistics'])->name('advertisements.statistics');
    });
    
    Route::middleware(['permission:create_advertisements'])->group(function () {
        Route::get('advertisements/create', [AdvertisementController::class, 'create'])->name('advertisements.create');
        Route::post('advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store');
    });
    
    Route::middleware(['permission:edit_advertisements'])->group(function () {
        Route::get('advertisements/{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('advertisements.edit');
        Route::put('advertisements/{advertisement}', [AdvertisementController::class, 'update'])->name('advertisements.update');
        Route::patch('advertisements/{advertisement}/toggle-status', [AdvertisementController::class, 'toggleStatus'])->name('advertisements.toggle-status');
        Route::patch('advertisements/{advertisement}/priority', [AdvertisementController::class, 'updatePriority'])->name('advertisements.update-priority');
    });
    
    Route::middleware(['permission:delete_advertisements'])->group(function () {
        Route::delete('advertisements/{advertisement}', [AdvertisementController::class, 'destroy'])->name('advertisements.destroy');
    });
    
    Route::middleware(['permission:manage_advertisements'])->group(function () {
        Route::post('advertisements/bulk-action', [AdvertisementController::class, 'bulkAction'])->name('advertisements.bulk-action');
    });
    
    Route::middleware(['permission:view_advertisements'])->group(function () {
        Route::get('advertisements/{advertisement}', [AdvertisementController::class, 'show'])->name('advertisements.show');
    });
    
    // Contact Messages Management
    Route::prefix('contact-messages')->name('contact-messages.')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index'])->name('index');
        
        // مراجعة الرسائل (للمدراء) - يجب أن تكون قبل /{id}
        Route::prefix('review')->name('review.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ContactMessageReviewController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Admin\ContactMessageReviewController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\ContactMessageReviewController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\ContactMessageReviewController::class, 'reject'])->name('reject');
        });
        
        Route::post('/mark-as-read', [ContactMessageController::class, 'markAsRead'])->name('mark-as-read');
        Route::get('/statistics/data', [ContactMessageController::class, 'statistics'])->name('statistics');
        
        // Dynamic routes يجب أن تكون في النهاية
        Route::get('/{id}', [ContactMessageController::class, 'show'])->name('show');
        Route::put('/{id}', [ContactMessageController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContactMessageController::class, 'destroy'])->name('destroy');
    });

    // Social Media Management
    Route::prefix('social-media')->name('social-media.')->group(function () {
        Route::get('/settings', [SocialMediaController::class, 'settings'])->name('settings');
        Route::post('/settings', [SocialMediaController::class, 'updateSettings'])->name('update-settings');
        Route::get('/posts', [SocialMediaController::class, 'posts'])->name('posts');
        Route::post('/articles/{article}/publish', [SocialMediaController::class, 'publishArticle'])->name('publish-article');
        Route::post('/posts/{post}/retry', [SocialMediaController::class, 'retryPost'])->name('retry-post');
        Route::delete('/posts/{post}', [SocialMediaController::class, 'deletePost'])->name('delete-post');
    });
});
