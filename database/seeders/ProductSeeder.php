<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the products table.
     */
    public function run(): void
    {
        $takoyaki = Category::where('name', 'Takoyaki')->first();
        $okonomiyaki = Category::where('name', 'Okonomiyaki')->first();
        $minuman = Category::where('name', 'Minuman')->first();
        $snack = Category::where('name', 'Snack')->first();

        $products = [
            // Takoyaki Products
            [
                'category_id' => $takoyaki->id,
                'name' => 'Takoyaki Spicy',
                'description' => 'Takoyaki dengan pedas level tinggi',
                'cost_price' => 8000,
                'selling_price' => 18000,
                'quantity_per_serving' => 6,
                'is_active' => true,
            ],
            [
                'category_id' => $takoyaki->id,
                'name' => 'Takoyaki Original',
                'description' => 'Takoyaki klasik dengan mayo dan saus okonomiyaki',
                'cost_price' => 7000,
                'selling_price' => 16000,
                'quantity_per_serving' => 6,
                'is_active' => true,
            ],
            [
                'category_id' => $takoyaki->id,
                'name' => 'Takoyaki Cheese',
                'description' => 'Takoyaki dengan keju mozzarella',
                'cost_price' => 9000,
                'selling_price' => 19000,
                'quantity_per_serving' => 6,
                'is_active' => true,
            ],
            [
                'category_id' => $takoyaki->id,
                'name' => 'Takoyaki Teriyaki',
                'description' => 'Takoyaki dengan saus teriyaki manis',
                'cost_price' => 8500,
                'selling_price' => 18500,
                'quantity_per_serving' => 6,
                'is_active' => true,
            ],

            // Okonomiyaki Products
            [
                'category_id' => $okonomiyaki->id,
                'name' => 'Okonomiyaki Ayam',
                'description' => 'Okonomiyaki dengan topping daging ayam',
                'cost_price' => 15000,
                'selling_price' => 35000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $okonomiyaki->id,
                'name' => 'Okonomiyaki Sapi',
                'description' => 'Okonomiyaki dengan topping daging sapi',
                'cost_price' => 18000,
                'selling_price' => 40000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $okonomiyaki->id,
                'name' => 'Okonomiyaki Seafood',
                'description' => 'Okonomiyaki dengan udang dan cumi',
                'cost_price' => 20000,
                'selling_price' => 45000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $okonomiyaki->id,
                'name' => 'Okonomiyaki Vegetarian',
                'description' => 'Okonomiyaki dengan sayuran pilihan',
                'cost_price' => 12000,
                'selling_price' => 30000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],

            // Minuman Products
            [
                'category_id' => $minuman->id,
                'name' => 'Es Teh Manis',
                'description' => 'Teh hitam dengan es batu',
                'cost_price' => 2000,
                'selling_price' => 8000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Es Jeruk',
                'description' => 'Minuman jeruk segar dengan es',
                'cost_price' => 2500,
                'selling_price' => 9000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Kopi Panas',
                'description' => 'Kopi espresso dengan susu',
                'cost_price' => 3000,
                'selling_price' => 10000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Cokelat Panas',
                'description' => 'Cokelat hangat yang lezat',
                'cost_price' => 3500,
                'selling_price' => 11000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],

            // Snack Products
            [
                'category_id' => $snack->id,
                'name' => 'Edamame',
                'description' => 'Kacang kedelai rebus dengan garam',
                'cost_price' => 4000,
                'selling_price' => 12000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $snack->id,
                'name' => 'Gyoza (6 pcs)',
                'description' => 'Pangsit Jepang dengan dipping sauce',
                'cost_price' => 6000,
                'selling_price' => 14000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $snack->id,
                'name' => 'Karaage Ayam',
                'description' => 'Ayam goreng gaya Jepang',
                'cost_price' => 5000,
                'selling_price' => 13000,
                'quantity_per_serving' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
