<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 1000; $i++) {
            Product::create([
                'name' => 'Product ' . $i,
                'description' => 'Description for product ' . $i,
                'price' => rand(100, 1000),
            ]);
        }
    }
}
