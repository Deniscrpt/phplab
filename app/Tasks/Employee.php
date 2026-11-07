<?php

declare(strict_types=1);

namespace App\Tasks;

/**
 * Табл. 4.3, задача 1: вершина ієрархії працівників.
 * Абстрактний метод calculateSalary() перевизначається в кожному класі.
 */
abstract class Employee
{
    public function __construct(
        protected string $name,
        protected float $hourlyRate,
    ) {}

    abstract public function calculateSalary(): float;

    abstract public function getPosition(): string;

    public function getName(): string
    {
        return $this->name;
    }
}
