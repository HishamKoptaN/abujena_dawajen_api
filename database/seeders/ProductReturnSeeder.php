<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductReturn;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;

class ProductReturnSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::get();
        $products = Product::all();
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i)->toDateString();
            foreach ($customers as $customer) {
                $hasTransaction = Transaction::where('customer_id', $customer->id)
                    ->whereDate('created_at', $date)
                    ->exists();
                if (!$hasTransaction) {
                    continue; 
                }
                $customerProducts = Transaction::where('customer_id', $customer->id)
                    ->whereDate('created_at', $date)
                    ->with(['transactionDetails.product'])
                    ->get()
                    ->pluck('transactionDetails')
                    ->flatten()
                    ->pluck('product_id')
                    ->unique()
                    ->toArray();
                if (empty($customerProducts)) {
                    continue; 
                }
                $randomProductId = $customerProducts[array_rand($customerProducts)];
                $product = $products->find($randomProductId);
                ProductReturn::create([
                    'customer_id' => $customer->id,
                    'product_id' => $randomProductId,
                    'weight' => rand(1, 5) + (rand(0, 99) / 100),
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
                echo "Created return for customer {$customer->id} on {$date} for product {$product->name}\n";
            }
        }
        echo "Product returns seeding completed!\n";
    }
}
