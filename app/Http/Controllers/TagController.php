<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /**
     * Перелік тегів із кількістю дописів.
     */
    public function index(): JsonResponse
    {
        $tags = Tag::query()->withCount('posts')->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'count' => $tags->count(),
            'data' => $tags,
        ], 200);
    }

    /**
     * Тег із опублікованими дописами (зворотний бік звʼязку M:M).
     */
    public function show(string $id): JsonResponse
    {
        $tag = Tag::with([
            'posts' => fn ($query) => $query
                ->where('status', 'published')
                ->latest('published_at')
                ->select('posts.id', 'posts.title', 'posts.slug', 'posts.published_at'),
        ])->find((int) $id);

        if ($tag === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Тег із зазначеним ідентифікатором не знайдено.',
            ], 404);
        }

        return response()->json(['status' => 'success', 'data' => $tag], 200);
    }
}
