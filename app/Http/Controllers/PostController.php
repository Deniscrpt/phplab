<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Ресурсний контролер головної сутності Post (діаграма класів ЛР №2).
 * Дані читаються та зберігаються через Eloquent ORM (замість PostMockStore із ЛР №5).
 */
class PostController extends Controller
{
    /**
     * Стрічка опублікованих дописів із рубрикою та тегами.
     * Eager Loading (with) → 4 запити замість 1 + 2N (проблема N+1).
     */
    public function index(Request $request): JsonResponse
    {
        $posts = Post::query()
            ->with(['category:id,name', 'tags:id,name'])
            ->where('status', 'published')
            ->when(
                $request->filled('category_id'),
                fn ($query) => $query->where('category_id', $request->integer('category_id'))
            )
            ->when(
                $request->filled('tag_id'),
                fn ($query) => $query->whereHas(
                    'tags',
                    fn ($tagQuery) => $tagQuery->where('tags.id', $request->integer('tag_id'))
                )
            )
            ->latest('published_at')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'count' => $posts->count(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
            'data' => $posts->items(),
        ], 200);
    }

    /**
     * Створення допису з привʼязкою до рубрики (1:M) та тегів (M:M).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'body' => ['required', 'string', 'min:10'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $publishedAt = $validated['published_at'] ?? null;

        $post = DB::transaction(function () use ($validated, $publishedAt): Post {
            $post = Post::create([
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'slug' => $this->uniqueSlug($validated['title']),
                'body' => $validated['body'],
                'status' => $publishedAt === null ? 'draft' : 'published',
                'views_count' => 0,
                'published_at' => $publishedAt,
            ]);

            // Синхронізація звʼязку M:M у проміжній таблиці post_tag
            if (! empty($validated['tags'])) {
                $post->tags()->sync($validated['tags']);
            }

            return $post;
        });

        $post->load(['category:id,name', 'tags:id,name']);

        return response()->json([
            'status' => 'created',
            'data' => $post,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $post = Post::with(['category:id,name', 'tags:id,name'])->find((int) $id);

        if ($post === null) {
            return $this->notFound();
        }

        return response()->json(['status' => 'success', 'data' => $post], 200);
    }

    /**
     * Оновлення допису; якщо передано масив tags — теги синхронізуються (sync).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $post = Post::find((int) $id);

        if ($post === null) {
            return $this->notFound();
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'body' => ['sometimes', 'required', 'string', 'min:10'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        DB::transaction(function () use ($post, $validated, $request): void {
            $data = collect($validated)->except('tags')->all();

            if (isset($data['title'])) {
                $data['slug'] = $this->uniqueSlug($data['title'], $post->id);
            }

            if ($request->has('published_at')) {
                $data['status'] = $data['published_at'] === null ? 'draft' : 'published';
            }

            $post->update($data);

            if (array_key_exists('tags', $validated)) {
                $post->tags()->sync($validated['tags']);
            }
        });

        $post->load(['category:id,name', 'tags:id,name']);

        return response()->json(['status' => 'updated', 'data' => $post], 200);
    }

    /**
     * Видалення допису; записи post_tag видаляються каскадно на рівні БД.
     */
    public function destroy(string $id): JsonResponse
    {
        $post = Post::find((int) $id);

        if ($post === null) {
            return $this->notFound();
        }

        $post->delete();

        return response()->json(['status' => 'deleted'], 200);
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Допис із зазначеним ідентифікатором не знайдено.',
        ], 404);
    }

    /**
     * Унікальний slug: за збігу додається числовий суфікс (my-post-2).
     */
    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $suffix = 2;

        while (
            Post::query()
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
