<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Тимчасове in-memory сховище дописів (Mock Data Store).
 * У ЛР №6 буде замінене на модель Eloquent Post.
 */
class PostMockStore
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Вступ до Laravel',
                'slug' => 'vstup-do-laravel',
                'body' => 'Laravel — PHP-фреймворк, що базується на архітектурі MVC.',
                'category_id' => 1,
                'status' => 'published',
                'views_count' => 1250,
                'published_at' => '2026-03-01 10:00:00',
            ],
            [
                'id' => 2,
                'title' => 'Маршрутизація у Laravel',
                'slug' => 'marshrutyzatsiia-u-laravel',
                'body' => 'Router зіставляє URI та HTTP-метод із правилами у routes/web.php.',
                'category_id' => 2,
                'status' => 'published',
                'views_count' => 870,
                'published_at' => '2026-03-05 12:30:00',
            ],
            [
                'id' => 3,
                'title' => 'Middleware на практиці',
                'slug' => 'middleware-na-praktytsi',
                'body' => 'Чернетка допису про конвеєр проміжного програмного забезпечення.',
                'category_id' => 2,
                'status' => 'draft',
                'views_count' => 0,
                'published_at' => null,
            ],
        ];
    }
}
