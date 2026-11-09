<?php

declare(strict_types=1);

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostStatsController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// Службові (нересурсні) маршрути
Route::get('/health', fn () => response()->json([
    'status' => 'OK',
    'timestamp' => now(),
]))->name('health');

Route::get('/about', fn () => response()->json([
    'project' => 'Інтернет-блог',
    'laboratory' => 6,
    'author' => 'Гошовський Д. М., КН-41',
]))->name('about');

// Ресурсна група сутності Post (БД через Eloquent)
Route::prefix('posts')->name('posts.')->group(function (): void {
    Route::get('/stats', [PostStatsController::class, 'summary'])->name('stats');
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::post('/', [PostController::class, 'store'])
        ->middleware('post.access')
        ->name('store');
    Route::get('/{id}', [PostController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
    Route::put('/{id}', [PostController::class, 'update'])
        ->middleware('post.access')
        ->whereNumber('id')
        ->name('update');
    Route::delete('/{id}', [PostController::class, 'destroy'])
        ->middleware('post.access')
        ->whereNumber('id')
        ->name('destroy');
});

// Рубрики (1:M) — перегляд
Route::prefix('categories')->name('categories.')->group(function (): void {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{id}', [CategoryController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
});

// Теги (M:M) — перегляд
Route::prefix('tags')->name('tags.')->group(function (): void {
    Route::get('/', [TagController::class, 'index'])->name('index');
    Route::get('/{id}', [TagController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
});
