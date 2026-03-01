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
        
        // نبدأ من 30 يوماً مضت وصولاً إلى اليوم
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            foreach ($products as $product) {
                // نستخدم updateOrCreate مع تحديد التاريخ والمنتج لمنع التكرار
                ProductDailyPrice::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'created_at' => $date->format('Y-m-d'),
                    ],
                    [
                        'price' => $product->name === 'تسمين' 
                            ? (75.00 + rand(-5, 5)) 
                            : (120.00 + rand(-10, 10)),
                        'notes' => 'سعر تلقائي لليوم: ' . $date->format('Y-m-d'),
                        'updated_at' => $date,
                    ]
                );
            }
        }
    }
}