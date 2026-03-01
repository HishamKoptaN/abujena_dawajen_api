<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DailyCollection;
use App\Models\Customer;
use Carbon\Carbon;

class DailyCollectionSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        for ($i = 30; $i >= 0; $i--) {
            $collectionDate = Carbon::now()->subDays($i);
            
            // اختيار 95% من العملاء للتحصيل اليومي
            $dailyPayers = $customers->random(rand(3, ceil($customers->count() * 0.95)));
            
            foreach ($dailyPayers as $customer) {
                if (rand(1, 100) <= 70) {
                    // 70% من العملاء: تحصيل واحد
                    $amount = rand(100, 2000);
                    $notes = 'تحصيل نقدي - ' . $collectionDate->format('Y-m-d');
                    
                    DailyCollection::create([
                        'customer_id' => $customer->id,
                        'amount' => $amount,
                        'created_at' => $collectionDate,
                        'updated_at' => $collectionDate,
                    ]);
                } else {
                    // 30% من العملاء: تحصيلين في اليوم
                    $amount1 = rand(50, 1000);
                    $amount2 = rand(50, 1000);
                    
                    // التحصيل الأول
                    DailyCollection::create([
                        'customer_id' => $customer->id,
                        'amount' => $amount1,
                        'created_at' => $collectionDate->copy()->addHours(rand(8, 12)),
                        'updated_at' => $collectionDate->copy()->addHours(rand(8, 12)),
                    ]);
                    
                    // التحصيل الثاني
                    DailyCollection::create([
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