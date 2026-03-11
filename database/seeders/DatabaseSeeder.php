<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\DailyPriceSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\CollectionSeeder;
use Database\Seeders\ProductReturnSeeder;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ProductSeeder::class,
            DailyPriceSeeder::class,
            TransactionSeeder::class,
            CollectionSeeder::class,
            ProductReturnSeeder::class,
        ]);
    }
}
