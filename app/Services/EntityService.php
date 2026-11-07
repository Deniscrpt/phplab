<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ExportableInterface;
use App\Interfaces\LoggerInterface;
use App\Models\AbstractEntity;
use InvalidArgumentException;

/**
 * Бізнес-логіка сутностей. Залежність від логера отримується ззовні (DI).
 */
class EntityService
{
    public function __construct(private LoggerInterface $logger) {}

    public function registerEntity(AbstractEntity $entity): void
    {
        $this->logger->log('Зареєстровано сутність: '.$entity->getDetails());
    }

    public function exportEntity(ExportableInterface $entity): string
    {
        $this->logger->log('Виконується експорт у JSON');

        return $entity->exportToJson();
    }

    /**
     * Рівень залучення допису (%): коментарі відносно переглядів.
     */
    public function calculateEngagementRate(int $viewsCount, int $commentsCount): float
    {
        if ($viewsCount === 0) {
            $this->logger->log('Переглядів немає, рівень залучення = 0');

            return 0.0;
        }

        $rate = round($commentsCount / $viewsCount * 100, 2);
        $this->logger->log("Рівень залучення допису: {$rate}%");

        return $rate;
    }

    /**
     * Ціна зі знижкою у відсотках (0-100).
     *
     * @throws InvalidArgumentException якщо знижка поза діапазоном
     */
    public function applyDiscount(float $price, float $discount): float
    {
        if ($discount < 0 || $discount > 100) {
            $this->logger->log("Помилка: некоректна знижка {$discount}");

            throw new InvalidArgumentException('Неприпустиме значення знижки!');
        }

        $result = $price - ($price * ($discount / 100));
        $this->logger->log("Ціна {$price} зі знижкою {$discount}% = {$result}");

        return $result;
    }
}
