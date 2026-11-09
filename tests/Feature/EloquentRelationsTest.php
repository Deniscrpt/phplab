<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Перевірка Active Record та звʼязків 1:M і M:M на рівні моделей
 * (автоматизований аналог сценаріїв artisan tinker).
 */
class EloquentRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_model_has_no_id_until_saved(): void
    {
        $category = new Category(['name' => 'Мережі', 'slug' => 'networks']);

        $this->assertNull($category->id);

        $category->save();

        $this->assertNotNull($category->id);
        $this->assertDatabaseHas('categories', ['slug' => 'networks']);
    }

    public function test_one_to_many_through_parent_relation(): void
    {
        $category = Category::factory()->create();

        $category->posts()->create([
            'title' => 'Перший допис',
            'slug' => 'first',
            'body' => 'Текст першого допису.',
        ]);
        $category->posts()->create([
            'title' => 'Другий допис',
            'slug' => 'second',
            'body' => 'Текст другого допису.',
        ]);

        $this->assertCount(2, $category->fresh()->posts);

        $post = Post::with('category')->where('slug', 'first')->firstOrFail();
        $this->assertSame($category->id, $post->category->id);
    }

    public function test_many_to_many_attach_sync_detach(): void
    {
        $post = Post::factory()->create();
        [$promo, $top] = Tag::factory()->count(2)->create()->all();

        $post->tags()->attach([$promo->id, $top->id]);
        $this->assertCount(2, $post->fresh()->tags);

        // зворотний бік звʼязку
        $this->assertTrue($promo->fresh()->posts->contains($post));

        $post->tags()->sync([$promo->id]);
        $this->assertSame([$promo->id], $post->fresh()->tags->pluck('id')->all());

        $post->tags()->detach($promo->id);
        $this->assertCount(0, $post->fresh()->tags);
    }

    public function test_create_tag_through_post_relation(): void
    {
        $post = Post::factory()->create();

        $post->tags()->create(['name' => 'Новинка', 'slug' => 'new']);

        $this->assertDatabaseHas('tags', ['slug' => 'new']);
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id]);
    }

    public function test_casts_are_applied(): void
    {
        $post = Post::factory()->published()->create(['views_count' => 7]);

        $fresh = Post::findOrFail($post->id);

        $this->assertIsInt($fresh->views_count);
        $this->assertInstanceOf(Carbon::class, $fresh->published_at);
    }
}
