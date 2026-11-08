<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class PostRoutesTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/health')->assertStatus(200)->assertJson(['status' => 'OK']);
    }

    public function test_index_returns_list_of_posts(): void
    {
        $this->getJson('/posts')
            ->assertStatus(200)
            ->assertJsonPath('count', 3);
    }

    public function test_index_filters_by_category(): void
    {
        $this->getJson('/posts?category_id=1')
            ->assertStatus(200)
            ->assertJsonPath('count', 1);
    }

    public function test_show_returns_existing_post(): void
    {
        $this->getJson('/posts/1')
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Вступ до Laravel');
    }

    public function test_show_returns_404_for_missing_post(): void
    {
        $this->getJson('/posts/999')->assertStatus(404)->assertJson(['status' => 'error']);
    }

    public function test_non_numeric_id_is_not_routed(): void
    {
        $this->getJson('/posts/abc')->assertStatus(404);
    }

    public function test_stats_endpoint_is_not_swallowed_by_id_route(): void
    {
        $this->getJson('/posts/stats')
            ->assertStatus(200)
            ->assertJsonPath('data.total', 3)
            ->assertJsonPath('data.published', 2);
    }
}
