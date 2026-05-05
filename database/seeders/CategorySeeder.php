<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Utama', 'slug' => 'makanan-utama'],
            ['name' => 'Minuman', 'slug' => 'minuman'],
            ['name' => 'Cemilan', 'slug' => 'cemilan'],
            ['name' => 'Dessert', 'slug' => 'dessert'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'business_id' => 1, // Asumsi business_id 1
                    'name' => $category['name'],
                    'is_active' => true
                ]
            );
        }
    }
}
