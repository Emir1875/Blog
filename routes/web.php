<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Pelanggan\ArticlesController;

/**
 * Route untuk halaman utama (/)
 * Redirect ke dashboard sesuai role jika sudah login
 * Redirect ke login jika belum login
 */
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        }

        if ($user->isPelanggan()) {
            return redirect()->route('pelanggan.dashboard');
        }
    }

    return redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::any('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Admin Routes
    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin Articles CRUD
    Route::get('/admin/articles/manage', [App\Http\Controllers\Admin\ArticleController::class, 'manage'])->name('admin.articles.manage');
    Route::get('/admin/articles', [App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('admin.articles');
    Route::get('/admin/articles/create', [App\Http\Controllers\Admin\ArticleController::class, 'create'])->name('admin.articles.create');
    Route::post('/admin/articles', [App\Http\Controllers\Admin\ArticleController::class, 'store'])->name('admin.articles.store');
    Route::get('/admin/articles/{slug}', [App\Http\Controllers\Admin\ArticleController::class, 'show'])->name('admin.articles.show');
    Route::get('/admin/articles/{slug}/edit', [App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('admin.articles.edit');
    Route::put('/admin/articles/{slug}', [App\Http\Controllers\Admin\ArticleController::class, 'update'])->name('admin.articles.update');
    Route::delete('/admin/articles/{slug}', [App\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('admin.articles.destroy');

    // Admin Categories CRUD
    Route::get('/admin/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories');
    Route::get('/admin/categories/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/admin/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/admin/categories/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Admin Comments
    Route::post('/admin/articles/{slug}/comments', [App\Http\Controllers\Admin\ArticleController::class, 'storeComment'])->name('admin.articles.comments.store');
    Route::delete('/admin/comments/{id}', [App\Http\Controllers\Admin\ArticleController::class, 'destroyComment'])->name('admin.comments.destroy');

    // Admin Like/Bookmark
    Route::post('/admin/articles/{slug}/like', [App\Http\Controllers\Admin\ArticleController::class, 'toggleLike'])->name('admin.articles.like');
    Route::post('/admin/articles/{slug}/bookmark', [App\Http\Controllers\Admin\ArticleController::class, 'toggleBookmark'])->name('admin.articles.bookmark');

    // Admin Profile
    Route::get('/admin/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('admin.profile');
    Route::put('/admin/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/admin/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('admin.profile.password');

    // Admin Users CRUD
    Route::resource('admin/users', App\Http\Controllers\Admin\UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show', // Not typically needed if handled by index/edit but good to keep standard
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    // Staff Routes
    Route::get('/staff/dashboard', [App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/articles', [App\Http\Controllers\Staff\ArticleController::class, 'index'])->name('staff.articles');
    Route::get('/staff/articles/manage', [App\Http\Controllers\Staff\ArticleController::class, 'manage'])->name('staff.articles.manage');
    Route::get('/staff/articles/create', [App\Http\Controllers\Staff\ArticleController::class, 'create'])->name('staff.articles.create');
    Route::post('/staff/articles', [App\Http\Controllers\Staff\ArticleController::class, 'store'])->name('staff.articles.store');
    Route::get('/staff/articles/{slug}', [App\Http\Controllers\Staff\ArticleController::class, 'show'])->name('staff.articles.show');
    Route::get('/staff/articles/{slug}/edit', [App\Http\Controllers\Staff\ArticleController::class, 'edit'])->name('staff.articles.edit');
    Route::put('/staff/articles/{slug}', [App\Http\Controllers\Staff\ArticleController::class, 'update'])->name('staff.articles.update');
    Route::delete('/staff/articles/{slug}', [App\Http\Controllers\Staff\ArticleController::class, 'destroy'])->name('staff.articles.destroy');
    Route::post('/staff/articles/{slug}/comments', [App\Http\Controllers\Staff\ArticleController::class, 'storeComment'])->name('staff.articles.comments.store');
    Route::post('/staff/articles/{slug}/like', [App\Http\Controllers\Staff\ArticleController::class, 'toggleLike'])->name('staff.articles.like');
    Route::post('/staff/articles/{slug}/bookmark', [App\Http\Controllers\Staff\ArticleController::class, 'toggleBookmark'])->name('staff.articles.bookmark');
    Route::post('/staff/articles/{slug}/publish', [App\Http\Controllers\Staff\ArticleController::class, 'publish'])->name('staff.articles.publish');
    Route::post('/staff/articles/{slug}/unpublish', [App\Http\Controllers\Staff\ArticleController::class, 'unpublish'])->name('staff.articles.unpublish');
    Route::get('/staff/profile', [App\Http\Controllers\Staff\ProfileController::class, 'index'])->name('staff.profile');
    Route::put('/staff/profile', [App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('staff.profile.update');
    Route::put('/staff/profile/password', [App\Http\Controllers\Staff\ProfileController::class, 'updatePassword'])->name('staff.profile.password');

    // Pelanggan Routes
    Route::get('/pelanggan/dashboard', [App\Http\Controllers\Pelanggan\DashboardController::class, 'index'])->name('pelanggan.dashboard');
    Route::get('/pelanggan/profile', [App\Http\Controllers\Pelanggan\ProfileController::class, 'index'])->name('pelanggan.profile');
    Route::put('/pelanggan/profile', [App\Http\Controllers\Pelanggan\ProfileController::class, 'update'])->name('pelanggan.profile.update');
    Route::put('/pelanggan/profile/password', [App\Http\Controllers\Pelanggan\ProfileController::class, 'updatePassword'])->name('pelanggan.profile.password');

    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        }
        if ($user->isPelanggan()) {
            return redirect()->route('pelanggan.dashboard');
        }
    })->name('dashboard');
});

// Pelanggan Articles
Route::get('/pelanggan/articles', [App\Http\Controllers\Pelanggan\ArticleController::class, 'index'])->name('pelanggan.articles');
Route::get('/pelanggan/articles/{slug}', [App\Http\Controllers\Pelanggan\ArticleController::class, 'show'])->name('pelanggan.articles.show');
Route::post('/pelanggan/articles/{slug}/comments', [App\Http\Controllers\Pelanggan\ArticleController::class, 'storeComment'])->name('pelanggan.articles.comments.store');
Route::post('/pelanggan/articles/{slug}/like', [App\Http\Controllers\Pelanggan\ArticleController::class, 'toggleLike'])->name('pelanggan.articles.like');
Route::post('/pelanggan/articles/{slug}/bookmark', [App\Http\Controllers\Pelanggan\ArticleController::class, 'toggleBookmark'])->name('pelanggan.articles.bookmark');