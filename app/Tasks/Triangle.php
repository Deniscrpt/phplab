<?php

declare(strict_types=1);

namespace App\Tasks;

use InvalidArgumentException;

/**
 * Табл. 4.4, задача 2: інкапсуляція алгоритмів для трикутника.
 */
class Triangle
{
    public function __construct(
        private readonly float $a,
        private readonly float $b,
        private readonly float $c,
    ) {
        if ($a <= 0 || $b <= 0 || $c <= 0) {
            throw new InvalidArgumentException('Сторони трикутника мають бути додатними');
        }

        if ($a + $b <= $c || $a + $c <= $b || $b + $c <= $a) {
            throw new InvalidArgumentException('Трикутник з такими сторонами не існує');
        }
    }

    public function perimeter(): float
    {
        return $this->a + $this->b + $this->c;
    }

    /**
     * Площа за формулою Герона.
     */
    public function area(): float
    {
        $p = $this->perimeter() / 2;

        return sqrt($p * ($p - $this->a) * ($p - $this->b) * ($p - $this->c));
    }
}
