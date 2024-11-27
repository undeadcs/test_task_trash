<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Тестирование отдачи товаров
 */
class ProductsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_listing_first_page(): void
    {
        $products = Product::factory()
            ->count(30)
            ->create()
            ->slice(0, 15)
            ->values()
            ->toArray();

        $this->getJson('products')
            ->assertStatus(200)
            ->assertJson(['data' => $products]);
    }

    public function test_listing_second_page(): void
    {
        $products = Product::factory()
            ->count(30)
            ->create()
            ->slice(15, 15)
            ->values()
            ->toArray();

        $this->getJson('products/?page=2')
            ->assertStatus(200)
            ->assertJson(['data' => $products]);
    }

    public function test_entity_not_found(): void
    {
        $this->getJson('products/1')->assertStatus(404);
    }

    public function test_entity_get(): void
    {
        $product = Product::factory()->create();

        $this->getJson('products/'.$product->id)
            ->assertStatus(200)
            ->assertJson(['data' => $product->toArray()]);
    }

    public function test_find_by_title(): void
    {
        $title = $this->faker->name();
        $products = Product::factory()
            ->state(['title' => $title])
            ->count(30)
            ->create()
            ->slice(0, 15)
            ->values()
            ->toArray();

        $this->getJson('products/find-by-title?'.http_build_query(['title' => mb_substr($title, 0, 3)]))
            ->assertStatus(200)
            ->assertJson(['data' => $products]);
    }

    public function test_find_by_price(): void
    {
        $price1 = 42.11;
        $price2 = 73.22;
        $price3 = 128.0;

        $products1 = Product::factory()
            ->state(['price' => $price1])
            ->count(10)
            ->create();

        $products2 = Product::factory()
            ->state(['price' => $price2])
            ->count(10)
            ->create();

        $products3 = Product::factory()
            ->state(['price' => $price3])
            ->count(10)
            ->create();

        $this->getJson('products/find-by-price?'.http_build_query(['price_from' => 0, 'price_to' => $price1]))
            ->assertStatus(200)
            ->assertJson(['data' => $products1->toArray()]);

        $this->getJson('products/find-by-price?'.http_build_query(['price_from' => $price1 + 1, 'price_to' => $price2]))
            ->assertStatus(200)
            ->assertJson(['data' => $products2->toArray()]);

        $this->getJson('products/find-by-price?'.http_build_query(['price_from' => $price2 + 1, 'price_to' => $price3]))
            ->assertStatus(200)
            ->assertJson(['data' => $products3->toArray()]);
    }
}
