<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_posts_with_relations(): void
    {
        // Підготовка тестових даних через фабрики
        $category = Category::factory()->create(['name' => 'Технології']);
        $tag = Tag::factory()->create(['name' => 'Laravel']);
        $post = Post::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Вступ до Eloquent',
        ]);
        $post->tags()->attach($tag->id);

        $response = $this->getJson('/posts');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Вступ до Eloquent'])
            ->assertJsonFragment(['name' => 'Технології'])
            ->assertJsonFragment(['name' => 'Laravel']);
    }

    public function test_can_create_post_with_category_and_tags_in_database(): void
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $payload = [
            'title' => 'Новий допис про міграції',
            'body' => 'Текст допису достатньої довжини для валідації.',
            'category_id' => $category->id,
            'published_at' => '2026-11-10 09:00:00',
            'tags' => $tags->pluck('id')->toArray(),
        ];

        $response = $this->withHeaders(['X-Author-Role' => 'author'])
            ->postJson('/posts', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'published');

        // Запис безпосередньо в таблиці СКБД
        $this->assertDatabaseHas('posts', [
            'title' => 'Новий допис про міграції',
            'category_id' => $category->id,
            'status' => 'published',
        ]);

        // Заповнення звʼязку M:M у pivot-таблиці
        $postId = $response->json('data.id');
        foreach ($tags as $tag) {
            $this->assertDatabaseHas('post_tag', [
                'post_id' => $postId,
                'tag_id' => $tag->id,
            ]);
        }
    }

    public function test_post_without_published_at_is_saved_as_draft(): void
    {
        $category = Category::factory()->create();

        $this->withHeaders(['X-Author-Role' => 'admin'])
            ->postJson('/posts', [
                'title' => 'Чернетка допису',
                'body' => 'Текст чернетки достатньої довжини.',
                'category_id' => $category->id,
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'draft');
    }

    public function test_store_rejects_unknown_category_and_tag(): void
    {
        $this->withHeaders(['X-Author-Role' => 'author'])
            ->postJson('/posts', [
                'title' => 'Допис без рубрики',
                'body' => 'Текст допису достатньої довжини.',
                'category_id' => 999,
                'tags' => [999],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['category_id', 'tags.0']);
    }

    public function test_duplicate_title_gets_unique_slug_suffix(): void
    {
        $category = Category::factory()->create();
        $payload = [
            'title' => 'Однаковий заголовок',
            'body' => 'Текст допису достатньої довжини.',
            'category_id' => $category->id,
        ];

        $first = $this->withHeaders(['X-Author-Role' => 'author'])->postJson('/posts', $payload);
        $second = $this->withHeaders(['X-Author-Role' => 'author'])->postJson('/posts', $payload);

        $first->assertStatus(201);
        $second->assertStatus(201);
        $this->assertSame($first->json('data.slug').'-2', $second->json('data.slug'));
    }

    public function test_update_syncs_tags_in_pivot_table(): void
    {
        $post = Post::factory()->create();
        [$tagA, $tagB, $tagC] = Tag::factory()->count(3)->create()->all();
        $post->tags()->attach([$tagA->id, $tagB->id]);

        $this->withHeaders(['X-Author-Role' => 'author'])
            ->putJson("/posts/{$post->id}", ['tags' => [$tagB->id, $tagC->id]])
            ->assertStatus(200);

        $this->assertDatabaseMissing('post_tag', ['post_id' => $post->id, 'tag_id' => $tagA->id]);
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tagB->id]);
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tagC->id]);
    }

    public function test_delete_post_removes_pivot_rows_cascade(): void
    {
        $post = Post::factory()->create();
        $tag = Tag::factory()->create();
        $post->tags()->attach($tag->id);

        $this->withHeaders(['X-Author-Role' => 'admin'])
            ->deleteJson("/posts/{$post->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseMissing('post_tag', ['post_id' => $post->id]);
    }

    public function test_deleting_category_cascades_to_posts(): void
    {
        $category = Category::factory()->has(Post::factory()->count(3))->create();

        $this->assertDatabaseCount('posts', 3);

        $category->delete();

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_pivot_unique_index_prevents_duplicate_pairs(): void
    {
        $post = Post::factory()->create();
        $tag = Tag::factory()->create();

        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => $tag->id]);

        $this->expectException(QueryException::class);

        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => $tag->id]);
    }

    public function test_index_uses_eager_loading_and_avoids_n_plus_one(): void
    {
        $tags = Tag::factory()->count(3)->create();
        Post::factory()->published()->count(10)->create()
            ->each(fn (Post $post) => $post->tags()->attach($tags->random(2)->pluck('id')));

        DB::enableQueryLog();

        $this->getJson('/posts')->assertStatus(200);

        // count + posts + categories + tags = 4 запити, незалежно від кількості дописів
        $this->assertLessThanOrEqual(4, count(DB::getQueryLog()));
    }
}
