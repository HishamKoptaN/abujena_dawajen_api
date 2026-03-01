<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductDailyPrice;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        $products = Product::all();
        for ($i = 30; $i >= 0; $i--) {
            $transactionDate = Carbon::now()->subDays($i);
            
            // اختيار 95% من العملاء للعمليات اليومية
            $dailyCustomers = $customers->random(rand(5, ceil($customers->count() * 0.95)));
            
            foreach ($dailyCustomers as $customer) {
                $transaction = Transaction::create([
                    'customer_id' => $customer->id,
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ]);
                $transactionProducts = $products->random(rand(1, 2));
                foreach ($transactionProducts as $product) {
                    $priceAtTime = $this->getProductPriceAtDate($product->id, $transactionDate);
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 100) + (rand(0, 9) / 10), 
                        'cage' => rand(1, 5) + (rand(0, 9) / 10), 
                        'price_at_time' => $priceAtTime,
                        'discount' => rand(0, 5) + (rand(0, 9) / 10),
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ]);
                }
            }
        }
    }
    
    private function getProductPriceAtDate($productId, $created_at)
    {
        $dailyPrice = ProductDailyPrice::where('product_id', $productId)
            ->whereDate('created_at', '<=', $created_at)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($dailyPrice) {
            return $dailyPrice->price;
        }
        $product = Product::find($productId);
        return $product->name === 'تسمين' 
            ? (75.00 + rand(-5, 5)) 
            : (120.00 + rand(-10, 10));
    }
}
