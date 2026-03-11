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
            foreach ($customers as $customer) {
                if (rand(1, 100) <= 80) {
                    if (rand(1, 100) <= 50) {
                        $transaction = Transaction::create([
                            'customer_id' => $customer->id,
                            'created_at' => $transactionDate->copy()->addHours(rand(8, 18)),
                            'updated_at' => $transactionDate->copy()->addHours(rand(8, 18)),
                        ]);
                        $transactionProducts = $products->random(rand(1, 2));
                        foreach ($transactionProducts as $product) {
                            $priceAtTime = $this->getProductPriceAtDate($product->id, $transactionDate);
                            TransactionDetail::create([
                                'transaction_id' => $transaction->id,
                                'product_id' => $product->id,
                                'weight' => rand(1, 100) + (rand(0, 9) / 10), 
                                'cage' => rand(1, 5) + (rand(0, 9) / 10), 
                                'price_at_time' => $priceAtTime,
                                'discount' => rand(0, 5) + (rand(0, 9) / 10),
                                'created_at' => $transactionDate->copy()->addHours(rand(8, 18)),
                                'updated_at' => $transactionDate->copy()->addHours(rand(8, 18)),
                            ]);
                        }
                    } else {
                        $transaction1 = Transaction::create([
                            'customer_id' => $customer->id,
                            'created_at' => $transactionDate->copy()->addHours(rand(8, 12)),
                            'updated_at' => $transactionDate->copy()->addHours(rand(8, 12)),
                        ]);
                        $transaction1Products = $products->random(rand(1, 2));
                        foreach ($transaction1Products as $product) {
                            $priceAtTime = $this->getProductPriceAtDate($product->id, $transactionDate);
                            TransactionDetail::create([
                                'transaction_id' => $transaction1->id,
                                'product_id' => $product->id,
                                'weight' => rand(1, 50) + (rand(0, 9) / 10), 
                                'cage' => rand(1, 3) + (rand(0, 9) / 10), 
                                'price_at_time' => $priceAtTime,
                                'discount' => rand(0, 3) + (rand(0, 9) / 10),
                                'created_at' => $transactionDate->copy()->addHours(rand(8, 12)),
                                'updated_at' => $transactionDate->copy()->addHours(rand(8, 12)),
                            ]);
                        }
                        $transaction2 = Transaction::create([
                            'customer_id' => $customer->id,
                            'created_at' => $transactionDate->copy()->addHours(rand(14, 18)),
                            'updated_at' => $transactionDate->copy()->addHours(rand(14, 18)),
                        ]);
                        $transaction2Products = $products->random(rand(1, 2));
                        foreach ($transaction2Products as $product) {
                            $priceAtTime = $this->getProductPriceAtDate($product->id, $transactionDate);
                            TransactionDetail::create([
                                'transaction_id' => $transaction2->id,
                                'product_id' => $product->id,
                                'weight' => rand(1, 50) + (rand(0, 9) / 10), 
                                'cage' => rand(1, 3) + (rand(0, 9) / 10), 
                                'price_at_time' => $priceAtTime,
                                'discount' => rand(0, 3) + (rand(0, 9) / 10),
                                'created_at' => $transactionDate->copy()->addHours(rand(14, 18)),
                                'updated_at' => $transactionDate->copy()->addHours(rand(14, 18)),
                            ]);
                        }
                    }
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
