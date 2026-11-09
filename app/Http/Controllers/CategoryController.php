<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Перелік рубрик із кількістю дописів (один запит завдяки withCount).
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()->withCount('posts')->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'count' => $categories->count(),
            'data' => $categories,
        ], 200);
    }

    /**
     * Рубрика з опублікованими дописами (зворотний бік звʼязку 1:M).
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::with([
            'posts' => fn ($query) => $query
                ->where('status', 'published')
                ->latest('published_at')
                ->select('id', 'category_id', 'title', 'slug', 'published_at'),
        ])->find((int) $id);

        if ($category === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Рубрику із зазначеним ідентифікатором не знайдено.',
            ], 404);
        }

        return response()->json(['status' => 'success', 'data' => $category], 200);
    }
}
