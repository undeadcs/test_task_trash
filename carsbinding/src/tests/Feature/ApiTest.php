<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Car;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Добавление пользователя авто
     */
    public function test_customer_add()
    {
        $customer = Customer::factory()->make(); // просто создать объект, без сохранения в базу

        // правильный запрос
        $response = $this->postJson('/api/customers', $customer->toArray());
        $response->assertStatus(200)
            ->assertJson($customer->toArray());

        // кривой запрос
        $response = $this->postJson('/api/customers', ['field1' => 'value1']);
        $response->assertStatus(400);
    }

    /**
     * Список пользователей авто
     *
     * @return void
     */
    public function test_customer_list()
    {
        $customers = Customer::factory()->count(5)->create(); // наполним базу

        // должна быть та же коллекция
        $response = $this->getJson('/api/customers');
        $response->assertStatus(200)
            ->assertJson($customers->toArray());
    }

    /**
     * Детализация пользователя авто
     */
    public function test_customer_details()
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson('/api/customers/'.$customer->id);
        $response->assertStatus(200)
            ->assertJson($customer->toArray());

        // запрос на несуществующий экземпляр
        $response = $this->getJson('/api/customers/4242');
        $response->assertStatus(404);
    }

    /**
     * Обновление пользователя авто
     */
    public function test_customer_update()
    {
        $customer = Customer::factory()->create();

        // меняем значение и отправляем, в базе оно старое
        $customer->phone = 'phone-'.sprintf('%04d', mt_rand(1, 1000));

        // после вызова апи должно вернуться новое
        $response = $this->putJson('/api/customers/'.$customer->id, $customer->toArray());
        $response->assertStatus(200)
            ->assertJson($customer->toArray());

        // запрос на не существующий экземпляр
        $response = $this->putJson('/api/customers/4242', []);
        $response->assertStatus(404);
    }

    /**
     * Добавление авто
     */
    public function test_car_add()
    {
        $car = Car::factory()->make();

        $response = $this->postJson('/api/cars', $car->toArray());
        $response->assertStatus(200)
            ->assertJson($car->toArray());

        $response = $this->postJson('/api/cars', ['field1' => 'value1']);
        $response->assertStatus(400);
    }

    /**
     * Список авто
     */
    public function test_car_list()
    {
        $cars = Car::factory()->count(5)->create(); // создать массив авто

        // должна быть та же коллекция
        $response = $this->getJson('/api/cars');
        $response->assertStatus(200)
            ->assertJson($cars->toArray());
    }

    /**
     * Детализация авто
     */
    public function test_car_details()
    {
        $car = Car::factory()->create();

        $response = $this->getJson('/api/cars/'.$car->id);
        $response->assertStatus(200)
            ->assertJson($car->toArray());

        // запрос на не существующий экземпляр
        $response = $this->getJson('/api/cars/8484');
        $response->assertStatus(404);
    }

    /**
     * Обновление авто
     */
    public function test_car_update()
    {
        $car = Car::factory()->create();

        // меняем значение и отправляем, в базе оно старое
        $car->number = 'num-'.sprintf('%04d', mt_rand(1000, 2000));

        // после вызова апи должно вернуться новое
        $response = $this->putJson('/api/cars/'.$car->id, $car->toArray());
        $response->assertStatus(200)
            ->assertJson($car->toArray());

        // запрос на не существующий экземпляр
        $response = $this->putJson('/api/cars/4242', []);
        $response->assertStatus(404);
    }

    /**
     * Удаление пользователя авто
     */
    public function test_customer_delete()
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson('/api/customers/'.$customer->id, $customer->toArray());
        $response->assertStatus(200)
            ->assertJson($customer->toArray());

        // кривой запрос
        $response = $this->deleteJson('/api/customers/4242');
        $response->assertStatus(404);
    }

    /**
     * Удаление авто
     */
    public function test_car_delete()
    {
        $car = Car::factory()->create();

        $response = $this->deleteJson('/api/cars/'.$car->id, $car->toArray());
        $response->assertStatus(200)
            ->assertJson($car->toArray());

        // кривой запрос
        $response = $this->deleteJson('/api/cars/8484');
        $response->assertStatus(404);
    }

    /**
     * Пользователь и его авто
     */
    public function test_relations()
    {
        // проверка наличия связей, которые есть в бд
        $customer = Customer::factory()->create();
        $car = Car::factory()->for($customer)->create();

        $response = $this->getJson('/api/customers/'.$customer->id.'/show-car');
        $response->assertStatus(200)
            ->assertJson($car->toArray());

        $response = $this->getJson('/api/cars/'.$car->id.'/show-customer');
        $response->assertStatus(200)
            ->assertJson($customer->toArray());

        // частная привязка
        $customer = Customer::factory()->create();
        $car = Car::factory()->create();

        $response = $this->postJson('/api/customers/'.$customer->id.'/assign-car/'.$car->id);
        $response->assertStatus(200);

        // проверяем, что в бд все как надо
        $customer = Customer::with('car')->find($customer->id);
        $car = Car::with('customer')->find($car->id);
        $this->assertNotNull($customer->car);
        $this->assertNotNull($car->customer);

        // отвязка
        $response = $this->postJson('/api/customers/'.$customer->id.'/unassign-car/');
        $response->assertStatus(200);

        // проверяем, что в бд все как надо
        $customer = Customer::with('car')->find($customer->id);
        $car = Car::with('customer')->find($car->id);
        $this->assertNull($customer->car);
        $this->assertNull($car->customer);

        // корявые запросы
        $response = $this->postJson('/api/customers/'.$customer->id.'/assign-car/8484');
        $response->assertStatus(404); // авто не найдено

        $response = $this->postJson('/api/customers/'.$customer->id.'/unassign-car/8484');
        $response->assertStatus(404); // авто не было привязано
    }
}
