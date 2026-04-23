<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $matic = ProductCategory::where('name', 'Matic')->first();
        $bebek = ProductCategory::where('name', 'Bebek / Gigi')->first();
        $sport = ProductCategory::where('name', 'Sport')->first();
        $trail = ProductCategory::where('name', 'Cub / Trail')->first();
        $ev = ProductCategory::where('name', 'EV')->first();

        $products = [
            // Matic
            [
                'product_category_id' => $matic?->id,
                'name' => 'Honda BeAT CBS',
                'price' => 18750000,
                'stock' => 12,
            ],
            [
                'product_category_id' => $matic?->id,
                'name' => 'Honda BeAT Deluxe',
                'price' => 19300000,
                'stock' => 10,
            ],
            [
                'product_category_id' => $matic?->id,
                'name' => 'Honda Genio CBS',
                'price' => 19600000,
                'stock' => 8,
            ],
            [
                'product_category_id' => $matic?->id,
                'name' => 'Honda Scoopy Prestige',
                'price' => 22800000,
                'stock' => 7,
            ],
            [
                'product_category_id' => $matic?->id,
                'name' => 'Honda Vario 125 CBS',
                'price' => 23500000,
                'stock' => 9,
            ],
            [
                'product_category_id' => $matic?->id,
                'name' => 'Honda Vario 160 ABS',
                'price' => 29200000,
                'stock' => 6,
            ],
            [
                'product_category_id' => $matic?->id,
                'name' => 'Honda PCX 160 CBS',
                'price' => 32700000,
                'stock' => 5,
            ],
            [
                'product_category_id' => $matic?->id,
                'name' => 'Honda ADV 160 ABS',
                'price' => 39600000,
                'stock' => 4,
            ],

            // Bebek / Gigi
            [
                'product_category_id' => $bebek?->id,
                'name' => 'Honda Revo Fit',
                'price' => 17100000,
                'stock' => 10,
            ],
            [
                'product_category_id' => $bebek?->id,
                'name' => 'Honda Revo X',
                'price' => 18600000,
                'stock' => 8,
            ],
            [
                'product_category_id' => $bebek?->id,
                'name' => 'Honda Supra X 125 FI',
                'price' => 20300000,
                'stock' => 7,
            ],
            [
                'product_category_id' => $bebek?->id,
                'name' => 'Honda Supra GTR 150',
                'price' => 26800000,
                'stock' => 5,
            ],

            // Sport
            [
                'product_category_id' => $sport?->id,
                'name' => 'Honda CB150 Verza',
                'price' => 23700000,
                'stock' => 6,
            ],
            [
                'product_category_id' => $sport?->id,
                'name' => 'Honda CB150R Streetfire',
                'price' => 31600000,
                'stock' => 5,
            ],
            [
                'product_category_id' => $sport?->id,
                'name' => 'Honda CBR150R',
                'price' => 38300000,
                'stock' => 4,
            ],
            [
                'product_category_id' => $sport?->id,
                'name' => 'Honda CBR250RR',
                'price' => 63800000,
                'stock' => 3,
            ],

            // Cub / Trail
            [
                'product_category_id' => $trail?->id,
                'name' => 'Honda CRF150L',
                'price' => 37800000,
                'stock' => 4,
            ],
            [
                'product_category_id' => $trail?->id,
                'name' => 'Honda Monkey',
                'price' => 83000000,
                'stock' => 2,
            ],
            [
                'product_category_id' => $trail?->id,
                'name' => 'Honda CT125',
                'price' => 81900000,
                'stock' => 2,
            ],

            // EV
            [
                'product_category_id' => $ev?->id,
                'name' => 'Honda EM1 e:',
                'price' => 40000000,
                'stock' => 3,
            ],
        ];

        foreach ($products as $product) {
            if ($product['product_category_id']) {
                Product::updateOrCreate(
                    ['name' => $product['name']],
                    $product
                );
            }
        }
    }
}