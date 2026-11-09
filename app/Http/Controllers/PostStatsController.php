<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;

/**
 * Службовий контролер статистики блогу (Use Case «Переглянути статистику»).
 * Показники рахуються агрегатними запитами до БД.
 */
class PostStatsController extends Controller
{
    public function summary(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_posts' => Post::count(),
                'published' => Post::where('status', 'published')->count(),
                'drafts' => Post::where('status', 'draft')->count(),
                'total_views' => (int) Post::sum('views_count'),
            ],
        ], 200);
    }
}
