<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/health')
            ->assertStatus(200)
            ->assertJson(['status' => 'OK']);
    }

    public function test_index_returns_list_of_published_posts(): void
    {
        Post::factory()->published()->count(3)->create();
        Post::factory()->draft()->create();

        $this->getJson('/posts')
            ->assertStatus(200)
            ->assertJson(['status' => 'success', 'count' => 3]);
    }

    public function test_index_filters_by_category(): void
    {
        $first = Category::factory()->create();
        $second = Category::factory()->create();
        Post::factory()->published()->count(2)->create(['category_id' => $first->id]);
        Post::factory()->published()->create(['category_id' => $second->id]);

        $this->getJson('/posts?category_id='.$first->id)
            ->assertStatus(200)
            ->assertJson(['count' => 2]);
    }

    public function test_show_returns_existing_post(): void
    {
        $post = Post::factory()->published()->create(['title' => 'Існуючий допис']);

        $this->getJson('/posts/'.$post->id)
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Існуючий допис')
            ->assertJsonPath('data.category.id', $post->category_id);
    }

    public function test_show_returns_404_for_missing_post(): void
    {
        $this->getJson('/posts/999')
            ->assertStatus(404)
            ->assertJson(['status' => 'error']);
    }

    public function test_non_numeric_id_is_not_routed(): void
    {
        $this->getJson('/posts/abc')->assertStatus(404);
    }

    public function test_stats_endpoint_is_not_swallowed_by_id_route(): void
    {
        Post::factory()->published()->create(['views_count' => 100]);
        Post::factory()->draft()->create();

        $this->getJson('/posts/stats')
            ->assertStatus(200)
            ->assertJsonPath('data.total_posts', 2)
            ->assertJsonPath('data.published', 1)
            ->assertJsonPath('data.drafts', 1)
            ->assertJsonPath('data.total_views', 100);
    }

    public function test_categories_and_tags_endpoints_work(): void
    {
        $category = Category::factory()->has(Post::factory()->published()->count(2))->create();

        $this->getJson('/categories')
            ->assertStatus(200)
            ->assertJsonPath('data.0.posts_count', 2);

        $this->getJson('/categories/'.$category->id)
            ->assertStatus(200)
            ->assertJsonCount(2, 'data.posts');

        $this->getJson('/tags')->assertStatus(200);
        $this->getJson('/tags/999')->assertStatus(404);
    }
}
