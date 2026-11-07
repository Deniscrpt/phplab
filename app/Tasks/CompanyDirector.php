<?php

declare(strict_types=1);

namespace App\Tasks;

class CompanyDirector extends DepartmentHead
{
    private const PROFIT_SHARE = 0.01;

    public function __construct(
        string $name,
        float $hourlyRate,
        int $hoursWorked,
        int $teamSize,
        int $workshopCount,
        protected float $companyProfit,
    ) {
        parent::__construct($name, $hourlyRate, $hoursWorked, $teamSize, $workshopCount);
    }

    public function calculateSalary(): float
    {
        return parent::calculateSalary() + $this->companyProfit * self::PROFIT_SHARE;
    }

    public function getPosition(): string
    {
        return 'Керівник підприємства';
    }
}
