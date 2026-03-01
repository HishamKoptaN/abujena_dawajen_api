<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    public function productDailyPrices(): HasMany
    {
        return $this->hasMany(ProductDailyPrice::class);
    }
    public function todayPrice(): HasMany
    {
        return $this->productDailyPrices()->whereDate('created_at', today());
    }
    public function getPriceForDate($date): ?ProductDailyPrice
    {
        return $this->productDailyPrices()->whereDate('created_at', $date)->first();
    }
    public function dailyOrders(): HasMany
    {
        return $this->hasMany(DailyOrder::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function getCurrentStock(): float
    {
        return $this->inventory()
            ->where('created_at', '<=', today())
            ->sum('transaction_quantity');
    }
}
