<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/v1/articles');
});

// Global login route (redirect to admin - no name to avoid conflict with Fortify)
Route::get('/login', function () {
    return redirect('/admin/login');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin.php';
});
