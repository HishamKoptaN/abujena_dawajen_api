<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Collection;
use App\Models\Customer;
use Carbon\Carbon;

class CollectionSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        for ($i = 30; $i >= 0; $i--) {
            $collectionDate = Carbon::now()->subDays($i);
            foreach ($customers as $customer) {
                if (rand(1, 100) <= 70) {
                    if (rand(1, 100) <= 50) {
                        $amount = rand(100, 2000);
                        Collection::create([
                            'customer_id' => $customer->id,
                            'amount' => $amount,
                            'created_at' => $collectionDate->copy()->addHours(rand(8, 18)),
                            'updated_at' => $collectionDate->copy()->addHours(rand(8, 18)),
                        ]);
                    } else {
                        $amount1 = rand(50, 1000);
                        $amount2 = rand(50, 1000);
                        Collection::create([
                            'customer_id' => $customer->id,
                            'amount' => $amount1,
                            'created_at' => $collectionDate->copy()->addHours(rand(8, 12)),
                            'updated_at' => $collectionDate->copy()->addHours(rand(8, 12)),
                        ]);
                        Collection::create([
                            'customer_id' => $customer->id,
                            'amount' => $amount2,
                            'created_at' => $collectionDate->copy()->addHours(rand(14, 18)),
                            'updated_at' => $collectionDate->copy()->addHours(rand(14, 18)),
                        ]);
                    }
                }
            }
        }
    }
}