<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Admin panel
Route::get('/kcg5025/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/kcg5025/login', [AdminController::class, 'login'])->middleware('throttle:10,1');
Route::post('/kcg5025/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware('auth')->prefix('kcg5025')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/posts', [AdminController::class, 'posts'])->name('posts');
    Route::get('/posts/create', [AdminController::class, 'createPost'])->name('posts.create');
    Route::post('/posts', [AdminController::class, 'storePost'])->name('posts.store');
    Route::get('/posts/{id}/edit', [AdminController::class, 'editPost'])->name('posts.edit');
    Route::put('/posts/{id}', [AdminController::class, 'updatePost'])->name('posts.update');
    Route::delete('/posts/{id}', [AdminController::class, 'deletePost'])->name('posts.delete');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
    Route::get('/tags', [AdminController::class, 'tags'])->name('tags');
    Route::post('/tags', [AdminController::class, 'storeTag'])->name('tags.store');
    Route::delete('/tags/{id}', [AdminController::class, 'deleteTag'])->name('tags.delete');
    Route::get('/media', [AdminController::class, 'media'])->name('media');
    Route::post('/media', [AdminController::class, 'uploadMedia'])->name('media.upload');
    
    Route::get('/analytics', function () {
        $stats = [
            'total_views' => \App\Models\AnalyticsEntry::count(),
            'unique_visitors' => \App\Models\AnalyticsEntry::distinct('ip_address')->get()->count(),
            'mobile_users' => \App\Models\AnalyticsEntry::where('device', 'mobile')->count(),
            'desktop_users' => \App\Models\AnalyticsEntry::where('device', 'desktop')->count(),
        ];
        
        $recent = \App\Models\AnalyticsEntry::orderBy('created_at', 'desc')->limit(50)->get();

        return view('admin.analytics', compact('stats', 'recent'));
    })->name('analytics');
});
