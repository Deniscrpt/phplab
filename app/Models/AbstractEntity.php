<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Базовий абстрактний клас усіх сутностей предметної галузі «Інтернет-блог».
 */
abstract class AbstractEntity
{
    public function __construct(
        protected int $id,
        protected string $createdAt,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    abstract public function getDetails(): string;
}
