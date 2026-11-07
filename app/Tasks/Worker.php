<?php

declare(strict_types=1);

namespace App\Tasks;

class Worker extends Employee
{
    public function __construct(
        string $name,
        float $hourlyRate,
        protected int $hoursWorked,
    ) {
        parent::__construct($name, $hourlyRate);
    }

    public function calculateSalary(): float
    {
        return $this->hourlyRate * $this->hoursWorked;
    }

    public function getPosition(): string
    {
        return 'Робітник';
    }
}
