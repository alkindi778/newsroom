<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\NewspaperIssueController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Api\ManifestController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\RssFeedController;
use App\Http\Controllers\Api\SmartSummaryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::prefix('v1')->group(function () {
    // Articles
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/featured', [ArticleController::class, 'featured']);
    Route::get('/articles/latest', [ArticleController::class, 'latest']);
    Route::get('/articles/popular', [ArticleController::class, 'popular']);
    Route::get('/articles/slider', [ArticleController::class, 'slider']);
    Route::get('/articles/breaking-news', [ArticleController::class, 'breakingNews']);
    Route::get('/articles/search', [ArticleController::class, 'search']);
    Route::post('/articles/{id}/view', [ArticleController::class, 'incrementView'])->whereNumber('id');
    Route::get('/articles/{article}/similar', [SearchController::class, 'similar']);
    Route::get('/articles/{article}/check-duplicates', [SearchController::class, 'checkDuplicates']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);
    
    // Smart Summary for Articles
    Route::post('/articles/{articleId}/smart-summary', [SmartSummaryController::class, 'generateSummary']);
    Route::get('/articles/{articleId}/smart-summary', [SmartSummaryController::class, 'getCachedSummary']);
    Route::delete('/articles/{articleId}/smart-summary', [SmartSummaryController::class, 'clearCache']);
    Route::post('/articles/smart-summary/batch', [SmartSummaryController::class, 'batchGenerate']);
    Route::get('/smart-summary/stats', [SmartSummaryController::class, 'getSummaryStats']);
    
    // Smart Summary Cache System (New)
    Route::prefix('smart-summaries')->group(function () {
        Route::get('/get/{hash}', [\App\Http\Controllers\SmartSummaryController::class, 'getSummary']);
        Route::post('/store', [\App\Http\Controllers\SmartSummaryController::class, 'storeSummary']);
        Route::get('/stats', [\App\Http\Controllers\SmartSummaryController::class, 'getStats']);
        Route::get('/recent', [\App\Http\Controllers\SmartSummaryController::class, 'getRecent']);
        Route::delete('/cleanup', [\App\Http\Controllers\SmartSummaryController::class, 'cleanup']);
    });
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/categories/{slug}/articles', [CategoryController::class, 'articles']);
    
    // Users (Public endpoints)
    Route::get('/authors', [\App\Http\Controllers\Api\UserController::class, 'authors']);
    Route::get('/authors/{id}', [\App\Http\Controllers\Api\UserController::class, 'author']);
    Route::get('/authors/{id}/articles', [\App\Http\Controllers\Api\UserController::class, 'authorArticles']);
    
    // Search (Legacy - redirects to articles/search)
    Route::get('/search', [SearchController::class, 'search']);

    // Writers (Public)
    Route::get('/writers', [\App\Http\Controllers\Api\WriterController::class, 'index']);
    Route::get('/writers/{slugOrId}', [\App\Http\Controllers\Api\WriterController::class, 'show']);
    Route::get('/writers/{slugOrId}/opinions', [\App\Http\Controllers\Api\WriterController::class, 'opinions']);

    // Opinions (Public)
    Route::get('/opinions', [\App\Http\Controllers\Api\OpinionController::class, 'index']);
    Route::get('/opinions/featured', [\App\Http\Controllers\Api\OpinionController::class, 'featured']);
    Route::get('/opinions/{slugOrId}', [\App\Http\Controllers\Api\OpinionController::class, 'show']);
    Route::post('/opinions/{id}/like', [\App\Http\Controllers\Api\OpinionController::class, 'like'])
        ->whereNumber('id');

    // Videos (Public)
    Route::get('/videos', [VideoController::class, 'index']);
    Route::get('/videos/featured', [VideoController::class, 'featured']);
    Route::get('/videos/search', [VideoController::class, 'search']);
    Route::post('/videos/{id}/view', [VideoController::class, 'incrementView'])->whereNumber('id');
    Route::post('/videos/{id}/like', [VideoController::class, 'like'])->whereNumber('id');
    Route::get('/videos/{slug}', [VideoController::class, 'show']);

    // RSS Feeds (Public)
    Route::get('/rss-feeds', [RssFeedController::class, 'index']);

    // Newspaper Issues (Public)
    Route::get('/newspaper-issues', [NewspaperIssueController::class, 'index']);
    Route::get('/newspaper-issues/featured', [NewspaperIssueController::class, 'featured']);
    Route::post('/newspaper-issues/{id}/download', [NewspaperIssueController::class, 'incrementDownload'])->whereNumber('id');
    Route::get('/newspaper-issues/{slug}', [NewspaperIssueController::class, 'show']);

    // Site Settings (Public)
    Route::get('/settings', [\App\Http\Controllers\Api\SiteSettingsController::class, 'index']);
    Route::get('/settings/group/{group}', [\App\Http\Controllers\Api\SiteSettingsController::class, 'getByGroup']);
    Route::get('/settings/{key}', [\App\Http\Controllers\Api\SiteSettingsController::class, 'show']);

    // Homepage Sections (Public)
    Route::get('/homepage-sections', [\App\Http\Controllers\Api\HomepageSectionController::class, 'index']);
    Route::get('/homepage-sections/{slug}', [\App\Http\Controllers\Api\HomepageSectionController::class, 'show']);

    // Advertisements (Public)
    Route::get('/advertisements/position/{position}', [AdvertisementController::class, 'getByPosition']);
    Route::get('/advertisements/page/{page}', [AdvertisementController::class, 'getForPage']);
    Route::get('/advertisements/after-section/{sectionId}', [AdvertisementController::class, 'getAfterSection'])->whereNumber('sectionId');
    Route::post('/advertisements/{id}/view', [AdvertisementController::class, 'trackView'])->whereNumber('id');
    Route::post('/advertisements/{id}/click', [AdvertisementController::class, 'trackClick'])->whereNumber('id');

    // Push Notifications (Public)
    Route::get('/push/public-key', [PushSubscriptionController::class, 'getPublicKey']);
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe']);
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);
    Route::post('/push/update-preferences', [PushSubscriptionController::class, 'updatePreferences']);
    Route::post('/push/test', [PushSubscriptionController::class, 'sendTestNotification']);
    
    // PWA Manifest (Public)
    Route::get('/manifest', [ManifestController::class, 'getManifest']);
    
    // Contact Messages (Public)
    Route::post('/contact-messages', [ContactMessageController::class, 'store']);

    // Social Media (Public)
    Route::get('/social-media/statistics', [SocialMediaController::class, 'getStatistics']);
    Route::get('/social-media/posts', [SocialMediaController::class, 'listPosts']);
    Route::get('/articles/{article}/social-media-status', [SocialMediaController::class, 'getPostStatus']);

    // Admin Routes (Protected)
    Route::middleware('auth:sanctum')->group(function () {
        // Newspaper Issues (Admin)
        Route::post('/newspaper-issues', [NewspaperIssueController::class, 'store']);
        Route::put('/newspaper-issues/{id}', [NewspaperIssueController::class, 'update'])->whereNumber('id');
        Route::delete('/newspaper-issues/{id}', [NewspaperIssueController::class, 'destroy'])->whereNumber('id');
        Route::patch('/newspaper-issues/{id}/toggle-featured', [NewspaperIssueController::class, 'toggleFeatured'])->whereNumber('id');
    });
});

// CORS is handled automatically by config/cors.php
// No need for duplicate routes
