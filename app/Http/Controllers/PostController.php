<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PostMockStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Ресурсний контролер головної сутності Post (діаграма класів ЛР №2).
 */
class PostController extends Controller
{
    public function __construct(private readonly PostMockStore $store) {}

    public function index(Request $request): JsonResponse
    {
        $posts = $this->store->all();

        // Необов'язковий фільтр за категорією: GET /posts?category_id=2
        if ($request->has('category_id')) {
            $categoryId = $request->integer('category_id');
            $posts = array_values(array_filter(
                $posts,
                fn (array $post): bool => $post['category_id'] === $categoryId
            ));
        }

        return response()->json([
            'status' => 'success',
            'count' => count($posts),
            'data' => $posts,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        // Атрибути класу Post з UML-діаграми (ЛР №2)
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'body' => ['required', 'string', 'min:10'],
            'category_id' => ['required', 'integer', 'min:1'],
            'published_at' => ['nullable', 'date'],
        ]);

        $publishedAt = $validated['published_at'] ?? null;

        $newPost = [
            'id' => count($this->store->all()) + 1,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'body' => $validated['body'],
            'category_id' => (int) $validated['category_id'],
            'status' => $publishedAt === null ? 'draft' : 'published',
            'views_count' => 0,
            'published_at' => $publishedAt,
        ];

        return response()->json([
            'status' => 'created',
            'data' => $newPost,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $postId = (int) $id;

        foreach ($this->store->all() as $post) {
            if ($post['id'] === $postId) {
                return response()->json(['status' => 'success', 'data' => $post], 200);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Допис із зазначеним ідентифікатором не знайдено.',
        ], 404);
    }
}
