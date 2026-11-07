<?php

declare(strict_types=1);

/**
 * Рівень залучення допису (%): коментарі відносно переглядів.
 */
function calculateEngagementRate(int $viewsCount, int $commentsCount): float
{
    if ($viewsCount === 0) {
        return 0.0;
    }

    return round($commentsCount / $viewsCount * 100, 2);
}

/**
 * Ціна (наприклад, преміум-підписки на блог) з урахуванням знижки у відсотках.
 *
 * @throws Exception якщо знижка поза діапазоном 0–100
 */
function applyDiscount(float $price, float $discount): float
{
    if ($discount < 0 || $discount > 100) {
        throw new Exception('Неприпустиме значення знижки!');
    }

    return $price - ($price * ($discount / 100));
}
