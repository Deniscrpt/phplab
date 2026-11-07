<?php

declare(strict_types=1);

// Індексний масив: назви 5 сутностей предметної галузі «Інтернет-блог»
$entities = ['User', 'Post', 'Comment', 'Category', 'Tag'];
sort($entities);

// Асоціативний масив: один допис (клас Post з UML-діаграми)
$post = [
    'id' => 1,
    'title' => 'Вступ до Laravel',
    'slug' => 'vstup-do-laravel',
    'body' => 'Laravel — PHP-фреймворк, що базується на архітектурі MVC.',
    'status' => 'published',
    'viewsCount' => 1250,
    'publishedAt' => '2026-03-01 10:00:00',
];
?>
<h3>Сутності блогу (відсортовано за алфавітом)</h3>
<ul>
    <?php foreach ($entities as $entity) { ?>
        <li><?= htmlspecialchars($entity) ?></li>
    <?php } ?>
</ul>

<h3>Об'єкт Post</h3>
<ul>
    <?php foreach ($post as $key => $value) { ?>
        <li><?= htmlspecialchars($key) ?>: <?= htmlspecialchars((string) $value) ?></li>
    <?php } ?>
</ul>
