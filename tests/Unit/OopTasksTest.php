<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\EntityFactory;
use App\Models\Product;
use App\Models\User;
use App\Tasks\CompanyDirector;
use App\Tasks\Store;
use App\Tasks\Triangle;
use App\Tasks\Worker;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OopTasksTest extends TestCase
{
    public function test_triangle_perimeter_and_area(): void
    {
        $triangle = new Triangle(3, 4, 5);

        $this->assertEqualsWithDelta(12.0, $triangle->perimeter(), 0.0001);
        $this->assertEqualsWithDelta(6.0, $triangle->area(), 0.0001);
    }

    public function test_triangle_rejects_impossible_sides(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Triangle(1, 2, 10);
    }

    public function test_store_add_search_and_remove(): void
    {
        $store = new Store(1, 'Тестовий склад');
        $store->addProduct(new Product(1, '2026-01-01', 'Преміум-підписка', 199.0));
        $store->addProduct(new Product(2, '2026-01-01', 'Електронна книга', 99.0));

        $this->assertCount(1, $store->searchProduct('преміум'));
        $this->assertTrue($store->removeProduct(1));
        $this->assertFalse($store->removeProduct(1));
        $this->assertCount(1, $store->getProducts());
    }

    public function test_salary_hierarchy(): void
    {
        $worker = new Worker('Іван', 150, 160);
        $director = new CompanyDirector('Андрій', 400, 160, 120, 5, 5000000);

        $this->assertEqualsWithDelta(24000.0, $worker->calculateSalary(), 0.0001);
        $this->assertEqualsWithDelta(189000.0, $director->calculateSalary(), 0.0001);
    }

    public function test_factory_creates_entities_and_rejects_unknown_type(): void
    {
        $user = EntityFactory::createEntity('user', ['id' => 1, 'name' => 'Денис', 'email' => 'd@example.com']);

        $this->assertInstanceOf(User::class, $user);

        $this->expectException(InvalidArgumentException::class);
        EntityFactory::createEntity('unknown', []);
    }

    public function test_json_export_trait(): void
    {
        $product = new Product(7, '2026-01-01', 'Курс PHP', 500.0);
        $data = json_decode($product->exportToJson(), true);

        $this->assertSame('Курс PHP', $data['name']);
        $this->assertSame(7, $data['id']);
    }
}
