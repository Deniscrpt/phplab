<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ExportableInterface;
use App\Traits\JsonExportTrait;

/**
 * Продукт блогу (наприклад, преміум-підписка або електронна книга).
 */
class Product extends AbstractEntity implements ExportableInterface
{
    use JsonExportTrait;

    public function __construct(
        int $id,
        string $createdAt,
        protected string $name,
        protected float $price,
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDetails(): string
    {
        return sprintf(
            'Продукт #%d "%s", ціна: %.2f грн, створено: %s',
            $this->id,
            $this->name,
            $this->price,
            $this->createdAt
        );
    }
}
