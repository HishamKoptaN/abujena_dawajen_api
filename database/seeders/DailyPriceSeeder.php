<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductDailyPrice;
use App\Models\Product;
use Carbon\Carbon;

class DailyPriceSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');

            foreach ($products as $product) {
                $existingPricesCount = ProductDailyPrice::where('product_id', $product->id)
                    ->whereDate('created_at', $dateString)
                    ->count();
                if ($existingPricesCount >= 2) {
                    continue; 
                }
                $needed = 2 - $existingPricesCount;
                for ($j = 0; $j < $needed; $j++) {
                    ProductDailyPrice::create([
                        'product_id' => $product->id,
                        'price' => $product->name === 'تسمين' 
                            ? (75.00 + rand(-5, 5)) 
                            : (120.00 + rand(-10, 10)),
                        'created_at' => $date->copy()->startOfDay()->addHours($j == 0 ? 8 : 16),
                        'updated_at' => $date->copy()->startOfDay()->addHours($j == 0 ? 8 : 16),
                    ]);
                }
            }
        }
    }
}