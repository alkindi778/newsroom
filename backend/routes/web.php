<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/v1/articles');
});

// Global login route (redirect to admin - no name to avoid conflict with Fortify)
Route::get('/login', function () {
    return redirect('/admin/login');
});

// Sitemap Routes
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-refresh', [App\Http\Controllers\SitemapController::class, 'refresh'])->name('sitemap.refresh');

// RSS Feeds Routes
Route::get('/rss/{slug?}', [App\Http\Controllers\RssController::class, 'show'])->name('rss.show');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin.php';
});

// Fallback route for all other requests (return 404 instead of 403)
Route::fallback(function () {
    abort(404);
});
