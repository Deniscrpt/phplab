<?php

declare(strict_types=1);

/**
 * Табл. 3.2, задача 4: чи всі елементи масиву менші за K.
 *
 * @param  int[]  $numbers
 */
function allLessThan(array $numbers, int $k): bool
{
    return count(array_filter($numbers, fn (int $n) => $n >= $k)) === 0;
}

/**
 * Табл. 3.3, задача 4: поміняти місцями перше й останнє слово рядка.
 */
function swapFirstAndLastWord(string $text): string
{
    $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

    if (count($words) < 2) {
        return $text;
    }

    $last = count($words) - 1;
    [$words[0], $words[$last]] = [$words[$last], $words[0]];

    return implode(' ', $words);
}

/**
 * Табл. 3.4, задача 3: перемішати слова рядка у випадковому порядку.
 */
function shuffleWords(string $text): string
{
    $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
    shuffle($words);

    return implode(' ', $words);
}
