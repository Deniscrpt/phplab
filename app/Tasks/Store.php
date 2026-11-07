<?php

declare(strict_types=1);

namespace App\Tasks;

use App\Models\Product;
use InvalidArgumentException;

/**
 * Табл. 4.2, задача 1 (композиція): склад продукції.
 * Магазин (Store) складається з продуктів (Product).
 */
class Store
{
    public function __construct(
        private int $storeId,
        private string $name,
        private array $products = [],
    ) {}

    public function getStoreId(): int
    {
        return $this->storeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return array_values($this->products);
    }

    public function addProduct(Product $product): void
    {
        if (isset($this->products[$product->getId()])) {
            throw new InvalidArgumentException("Продукт з id {$product->getId()} уже є на складі");
        }

        $this->products[$product->getId()] = $product;
    }

    public function removeProduct(int $id): bool
    {
        if (! isset($this->products[$id])) {
            return false;
        }

        unset($this->products[$id]);

        return true;
    }

    /**
     * Пошук за частиною назви без урахування регістру.
     *
     * @return Product[]
     */
    public function searchProduct(string $name): array
    {
        return array_values(array_filter(
            $this->products,
            fn (Product $product): bool => mb_stripos($product->getName(), $name) !== false
        ));
    }
}
