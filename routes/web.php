<?php

declare(strict_types=1);

use App\Http\Controllers\PostController;
use App\Http\Controllers\PostStatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// Службові (нересурсні) маршрути
Route::get('/health', fn () => response()->json([
    'status' => 'OK',
    'timestamp' => now(),
]))->name('health');

Route::get('/about', fn () => response()->json([
    'project' => 'Інтернет-блог',
    'laboratory' => 5,
    'author' => 'Гошовський Д. М., КН-41',
]))->name('about');

// Ресурсна група сутності Post
Route::prefix('posts')->name('posts.')->group(function (): void {
    Route::get('/stats', [PostStatsController::class, 'summary'])->name('stats');
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::post('/', [PostController::class, 'store'])
        ->middleware('post.access')
        ->name('store');
    Route::get('/{id}', [PostController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
});
