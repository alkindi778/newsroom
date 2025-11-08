<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/v1/articles');
});

// Global login route (required by Laravel auth middleware)
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin.php';
});
