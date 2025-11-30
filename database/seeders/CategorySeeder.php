<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Takoyaki',
                'description' => 'Bola gurita berisi topping pilihan',
                'is_active' => true,
            ],
            [
                'name' => 'Okonomiyaki',
                'description' => 'Pancake Jepang dengan berbagai topping',
                'is_active' => true,
            ],
            [
                'name' => 'Minuman',
                'description' => 'Minuman panas dan dingin',
                'is_active' => true,
            ],
            [
                'name' => 'Snack',
                'description' => 'Camilan dan makanan ringan',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
