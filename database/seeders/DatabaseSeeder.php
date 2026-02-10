<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $canon = \App\Models\Brand::create(['name' => 'Canon', 'slug' => 'canon']);
        $nikon = \App\Models\Brand::create(['name' => 'Nikon', 'slug' => 'nikon']);
        $sony = \App\Models\Brand::create(['name' => 'Sony', 'slug' => 'sony']);

        $cameras = \App\Models\Category::create(['name' => 'Cameras', 'slug' => 'cameras']);
        $lenses = \App\Models\Category::create(['name' => 'Lenses', 'slug' => 'lenses']);

        \App\Models\Product::create([
            'name' => 'Canon EOS R5',
            'slug' => 'canon-eos-r5',
            'category_id' => $cameras->id,
            'brand_id' => $canon->id,
            'price' => 3899.00,
            'description' => 'Professional mirrorless camera.',
            'stock' => 10,
            'image' => 'https://placehold.co/600x400?text=Canon+EOS+R5'
        ]);

        \App\Models\Product::create([
            'name' => 'Sony A7 IV',
            'slug' => 'sony-a7-iv',
            'category_id' => $cameras->id,
            'brand_id' => $sony->id,
            'price' => 2499.00,
            'description' => 'Hybrid full-frame camera.',
            'stock' => 15,
            'image' => 'https://placehold.co/600x400?text=Sony+A7+IV'
        ]);

        \App\Models\Product::create([
            'name' => 'Nikon Z9',
            'slug' => 'nikon-z9',
            'category_id' => $cameras->id,
            'brand_id' => $nikon->id,
            'price' => 5499.00,
            'description' => 'Flagship mirrorless camera.',
            'stock' => 5,
            'image' => 'https://placehold.co/600x400?text=Nikon+Z9'
        ]);
    }
}
