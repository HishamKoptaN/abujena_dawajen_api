<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\System\NotificationSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\DailyPriceSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\DailyCollectionSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\CustomerDailyReportSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CustomerSeeder::class,
            ProductSeeder::class,
            DailyPriceSeeder::class,
            TransactionSeeder::class,
            DailyCollectionSeeder::class,
            CustomerDailyReportSeeder::class,
        ]);
    }
}
