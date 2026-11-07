<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\EntityFactory;
use App\Models\Product;
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use App\Services\FileLogger;
use App\Tasks\CompanyDirector;
use App\Tasks\DepartmentHead;
use App\Tasks\Store;
use App\Tasks\Triangle;
use App\Tasks\Worker;
use App\Tasks\WorkshopManager;

require_once 'vendor/autoload.php';

if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

echo '=== Лабораторна робота №4. ООП в PHP (Інтернет-блог) ==='.PHP_EOL.PHP_EOL;

// ---------- Завдання 3: Singleton ----------
echo '--- Singleton: Database ---'.PHP_EOL;
$db = Database::getInstance();
$dbAgain = Database::getInstance();
$same = $db === $dbAgain ? 'так' : 'ні';
echo "Це той самий екземпляр: {$same}".PHP_EOL;
echo $db->getConnectionInfo().PHP_EOL;

try {
    $copy = clone $db;
} catch (Error $e) {
    echo 'Клонування заборонено: '.$e->getMessage().PHP_EOL;
}
echo PHP_EOL;

// ---------- Завдання 3: Factory ----------
echo '--- Factory: EntityFactory ---'.PHP_EOL;
$product = EntityFactory::createEntity('product', [
    'id' => 1,
    'createdAt' => '2026-03-01 10:00:00',
    'name' => 'Преміум-підписка на блог',
    'price' => 199.99,
]);
$user = EntityFactory::createEntity('user', [
    'id' => 10,
    'createdAt' => '2026-02-15 09:30:00',
    'name' => 'Денис Гошовський',
    'email' => 'denys019gos@gmail.com',
    'role' => 'author',
]);
echo $product->getDetails().PHP_EOL;
echo $user->getDetails().PHP_EOL;

try {
    EntityFactory::createEntity('comment', []);
} catch (InvalidArgumentException $e) {
    echo 'Помилка фабрики: '.$e->getMessage().PHP_EOL;
}
echo PHP_EOL;

// ---------- Завдання 2: інтерфейс + трейт ----------
echo '--- Експорт у JSON (JsonExportTrait) ---'.PHP_EOL;
echo $product->exportToJson().PHP_EOL.PHP_EOL;

// ---------- Завдання 4-5: Dependency Injection ----------
echo '--- Dependency Injection: EntityService ---'.PHP_EOL;
$service = new EntityService(new ConsoleLogger);
$service->registerEntity($product);
$service->registerEntity($user);
$service->calculateEngagementRate(1250, 50);
$service->applyDiscount(199.99, 25);

try {
    $service->applyDiscount(199.99, 150);
} catch (InvalidArgumentException $e) {
    echo 'Виняток: '.$e->getMessage().PHP_EOL;
}

$fileService = new EntityService(new FileLogger);
$fileService->registerEntity($user);
echo PHP_EOL;

// ---------- Завдання 6, табл. 4.2 №1: Store + Product ----------
echo '--- Склад продукції (Store) ---'.PHP_EOL;
$store = new Store(1, 'Магазин блогу');
$store->addProduct(new Product(1, '2026-03-01', 'Преміум-підписка', 199.99));
$store->addProduct(new Product(2, '2026-03-02', 'Електронна книга з PHP', 149.5));
$store->addProduct(new Product(3, '2026-03-03', 'Преміум-курс Laravel', 499.0));

echo 'Продуктів на складі: '.count($store->getProducts()).PHP_EOL;
echo 'Пошук "преміум":'.PHP_EOL;
foreach ($store->searchProduct('преміум') as $found) {
    echo ' - '.$found->getName().PHP_EOL;
}
$removed = $store->removeProduct(2) ? 'видалено' : 'не знайдено';
echo "Продукт #2: {$removed}".PHP_EOL;
echo 'Продуктів після видалення: '.count($store->getProducts()).PHP_EOL.PHP_EOL;

// ---------- Завдання 6, табл. 4.3 №1: ієрархія працівників ----------
echo '--- Ієрархія працівників ---'.PHP_EOL;
$employees = [
    new Worker('Іван Петренко', 150, 160),
    new WorkshopManager('Марія Коваль', 200, 160, 10),
    new DepartmentHead('Олег Бондар', 250, 160, 30, 3),
    new CompanyDirector('Андрій Мельник', 400, 160, 120, 5, 5000000),
];
foreach ($employees as $employee) {
    $salary = number_format($employee->calculateSalary(), 2, '.', ' ');
    echo "{$employee->getPosition()} {$employee->getName()}: {$salary} грн".PHP_EOL;
}
echo PHP_EOL;

// ---------- Завдання 6, табл. 4.4 №2: Triangle ----------
echo '--- Трикутник (формула Герона) ---'.PHP_EOL;
$triangle = new Triangle(3, 4, 5);
echo 'Сторони 3, 4, 5. Периметр: '.$triangle->perimeter().', площа: '.$triangle->area().PHP_EOL;

try {
    new Triangle(1, 2, 10);
} catch (InvalidArgumentException $e) {
    echo 'Сторони 1, 2, 10: '.$e->getMessage().PHP_EOL;
}
