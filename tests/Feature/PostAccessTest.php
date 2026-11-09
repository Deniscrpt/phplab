<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'title' => 'Новий допис про Laravel',
            'body' => 'Текст допису достатньої довжини для валідації.',
            'category_id' => Category::factory()->create()->id,
            'published_at' => '2026-11-10 09:00:00',
        ];
    }

    public function test_request_without_role_header_is_blocked(): void
    {
        $this->postJson('/posts', $this->validPayload())
            ->assertStatus(403)
            ->assertJson(['status' => 'access_denied']);
    }

    public function test_reader_role_is_forbidden(): void
    {
        $this->withHeaders(['X-Author-Role' => 'reader'])
            ->postJson('/posts', $this->validPayload())
            ->assertStatus(403)
            ->assertJson(['status' => 'forbidden_role']);
    }

    public function test_author_role_passes_and_gets_security_header(): void
    {
        $response = $this->withHeaders(['X-Author-Role' => 'author'])
            ->postJson('/posts', $this->validPayload());

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Новий допис про Laravel')
            ->assertJsonPath('data.status', 'published');
        $response->assertHeader('X-Security-Policy', 'Enforced');
    }

    public function test_invalid_data_returns_validation_error(): void
    {
        $this->withHeaders(['X-Author-Role' => 'admin'])
            ->postJson('/posts', ['body' => 'коротко'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'body', 'category_id']);
    }

    public function test_delete_requires_author_role(): void
    {
        $this->deleteJson('/posts/1')->assertStatus(403);
    }
}
