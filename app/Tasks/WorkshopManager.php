<?php

declare(strict_types=1);

namespace App\Tasks;

class WorkshopManager extends Worker
{
    private const BONUS_PER_SUBORDINATE = 500.0;

    public function __construct(
        string $name,
        float $hourlyRate,
        int $hoursWorked,
        protected int $teamSize,
    ) {
        parent::__construct($name, $hourlyRate, $hoursWorked);
    }

    public function calculateSalary(): float
    {
        return parent::calculateSalary() + $this->teamSize * self::BONUS_PER_SUBORDINATE;
    }

    public function getPosition(): string
    {
        return 'Керівник цеху';
    }
}
