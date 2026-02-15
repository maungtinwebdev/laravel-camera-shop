<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_page_1_returns_200()
    {
        Cache::flush();
        
        // Seed data
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
        $brand = Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 100,
            'stock' => 10,
        ]);

        $response = $this->getJson('/api/products?page=1');

        $response->assertStatus(200);
    }
}
