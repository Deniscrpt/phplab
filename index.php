<?php

declare(strict_types=1);
use Faker\Factory;

require_once 'vendor/autoload.php';
require_once 'app/Services/EntityService.php';
require_once 'app/Services/DataProcessor.php';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Лабораторна робота №3 — Інтернет-блог</title>
</head>
<body>
<h1>Лабораторна робота №3. Інтернет-блог</h1>

<h2>Завдання 1: масиви</h2>
<?php require __DIR__.'/app/Models/EntityArray.php'; ?>

<h2>Завдання 2: користувацька функція</h2>
<p>calculateEngagementRate(1250, 50) = <?= calculateEngagementRate(1250, 50) ?>%</p>

<h2>Завдання 3: Faker, array_filter, closure</h2>
<?php
$faker = Factory::create();
$views = [];
for ($i = 0; $i < 10; $i++) {
    $views[] = $faker->numberBetween(50, 3000);
}
$minThreshold = 500;
$popular = array_filter($views, function ($count) use ($minThreshold) {
    return $count > $minThreshold;
});
?>
<p>Кількість переглядів 10 дописів: <?= implode(', ', $views) ?></p>
<p>Популярні дописи (більше <?= $minThreshold ?> переглядів): <?= implode(', ', $popular) ?></p>

<h2>Завдання 4: винятки</h2>
<pre>
<?php
try {
    echo applyDiscount(1000, 150);
} catch (Exception $e) {
    echo 'Помилка: '.$e->getMessage();
} finally {
    echo "\nЗавершення спроби розрахунку.";
}
?>
</pre>

<h2>Завдання 5: DataProcessor (варіант 10)</h2>
<?php
$numbers = [3, 7, 1, 9, 4];
$k = 10;
$text = 'Laravel це потужний PHP фреймворк для вебу';
?>
<h3>Табл. 3.2, задача 4</h3>
<p>Масив: [<?= implode(', ', $numbers) ?>], K = <?= $k ?></p>
<p>Усі елементи менші за K: <?= var_export(allLessThan($numbers, $k), true) ?></p>

<h3>Табл. 3.3, задача 4</h3>
<p>Було: <?= htmlspecialchars($text) ?></p>
<p>Стало: <?= htmlspecialchars(swapFirstAndLastWord($text)) ?></p>

<h3>Табл. 3.4, задача 3</h3>
<p>Було: <?= htmlspecialchars($text) ?></p>
<p>Перемішано: <?= htmlspecialchars(shuffleWords($text)) ?></p>
</body>
</html>
