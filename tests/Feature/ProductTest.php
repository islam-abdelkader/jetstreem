<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_products_index_contains_empty_table(): void
    {
        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee(__('products.empty'));
    }

    public function test_products_index_contains_non_empty_table(): void
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertDontSee(__('product.empty'));
        $response->assertSee($product->name);
        $response->assertViewHas('products', function ($collection) use ($product) {
            return $collection->contains($product);
        });

    }

    public function test_paginated_products_table_doesnt_contains_nth_record(): void
    {
        $products = Product::factory(config('app.paginate') + 1)->create();
        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertStatus(200);

        $response->assertViewHas('products', function ($collection) use ($products) {
            return !$collection->contains($products->last());
        });
    }

}
