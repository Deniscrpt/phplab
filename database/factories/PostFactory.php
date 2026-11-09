<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(4), '.');
        $isPublished = fake()->boolean(80);

        return [
            // Рубрика створюється автоматично, якщо не передана явно
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'body' => fake()->paragraphs(3, true),
            'status' => $isPublished ? 'published' : 'draft',
            'views_count' => $isPublished ? fake()->numberBetween(0, 5000) : 0,
            'published_at' => $isPublished ? fake()->dateTimeBetween('-3 months') : null,
        ];
    }

    /**
     * Стан «опублікований допис».
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
    }

    /**
     * Стан «чернетка».
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'draft',
            'views_count' => 0,
            'published_at' => null,
        ]);
    }
}
