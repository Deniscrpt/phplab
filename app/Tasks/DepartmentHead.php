<?php

declare(strict_types=1);

namespace App\Tasks;

class DepartmentHead extends WorkshopManager
{
    private const BONUS_PER_WORKSHOP = 3000.0;

    public function __construct(
        string $name,
        float $hourlyRate,
        int $hoursWorked,
        int $teamSize,
        protected int $workshopCount,
    ) {
        parent::__construct($name, $hourlyRate, $hoursWorked, $teamSize);
    }

    public function calculateSalary(): float
    {
        return parent::calculateSalary() + $this->workshopCount * self::BONUS_PER_WORKSHOP;
    }

    public function getPosition(): string
    {
        return 'Начальник відділу';
    }
}
