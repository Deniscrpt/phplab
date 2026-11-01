# CI/CD Lab: PHP Calculator

[![PHP CI Application](https://github.com/Deniscrpt/phplab/actions/workflows/php-ci.yml/badge.svg)](https://github.com/Deniscrpt/phplab/actions/workflows/php-ci.yml)

Мінімальний PHP-проєкт для демонстрації безперервної інтеграції (CI) на GitHub Actions.
Автор: Гошовський Денис, група КН-41.

## Функціонал
- Додавання двох чисел
- Віднімання двох чисел
- Автоматичне лінтування коду (Laravel Pint)
- Автоматичне тестування (PHPUnit)

## Запуск тестів
```bash
composer install
./vendor/bin/pint --test
./vendor/bin/phpunit tests/Unit
```