<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\AbstractEntity;
use App\Models\Product;
use App\Models\User;
use InvalidArgumentException;

/**
 * Патерн Factory Method: централізоване створення сутностей.
 */
class EntityFactory
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function createEntity(string $type, array $data): AbstractEntity
    {
        $createdAt = (string) ($data['createdAt'] ?? date('Y-m-d H:i:s'));

        return match ($type) {
            'product' => new Product(
                (int) $data['id'],
                $createdAt,
                (string) $data['name'],
                (float) $data['price'],
            ),
            'user' => new User(
                (int) $data['id'],
                $createdAt,
                (string) $data['name'],
                (string) $data['email'],
                (string) ($data['role'] ?? 'reader'),
            ),
            default => throw new InvalidArgumentException("Невідомий тип сутності: {$type}"),
        };
    }
}
