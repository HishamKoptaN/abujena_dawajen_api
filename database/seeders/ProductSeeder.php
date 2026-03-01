<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => ' تسمين',
            ],
            [
                'name' =>'أمهات',
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
