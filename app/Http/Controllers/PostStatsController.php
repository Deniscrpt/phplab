<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PostMockStore;
use Illuminate\Http\JsonResponse;

/**
 * Допоміжний контролер звітності (Use Case «Переглянути статистику»).
 */
class PostStatsController extends Controller
{
    public function __construct(private readonly PostMockStore $store) {}

    public function summary(): JsonResponse
    {
        $posts = $this->store->all();

        $published = array_filter(
            $posts,
            fn (array $post): bool => $post['status'] === 'published'
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => count($posts),
                'published' => count($published),
                'drafts' => count($posts) - count($published),
                'total_views' => array_sum(array_column($posts, 'views_count')),
            ],
        ], 200);
    }
}
