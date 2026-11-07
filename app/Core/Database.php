<?php

declare(strict_types=1);

namespace App\Core;

use LogicException;

/**
 * Патерн Singleton: у пам'яті існує лише один екземпляр підключення до БД.
 */
final class Database
{
    private static ?self $instance = null;

    private function __construct()
    {
        echo 'Підключення до БД створено'.PHP_EOL;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function getConnectionInfo(): string
    {
        return 'mysql:host=localhost;dbname=blog (екземпляр #'.spl_object_id($this).')';
    }

    private function __clone() {}

    public function __wakeup(): void
    {
        throw new LogicException('Десеріалізація Singleton заборонена.');
    }
}
