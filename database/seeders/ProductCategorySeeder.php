<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Matic'],
            ['name' => 'Bebek / Gigi'],
            ['name' => 'Sport'],
            ['name' => 'Cub / Trail'],
            ['name' => 'EV'],
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}