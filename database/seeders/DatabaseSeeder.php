<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Створюємо 10 тегів
        $tags = Tag::factory()->count(10)->create();

        // 2. Створюємо 5 рубрик, для кожної генеруємо по 8 дописів
        Category::factory()
            ->count(5)
            ->has(Post::factory()->count(8))
            ->create();

        // 3. Кожному допису привʼязуємо від 1 до 3 випадкових тегів (M:M)
        Post::query()->each(function (Post $post) use ($tags): void {
            $post->tags()->attach($tags->random(random_int(1, 3))->pluck('id'));
        });
    }
}
