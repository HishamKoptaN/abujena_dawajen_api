<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProductReturn;
class Product extends Model
{
    use HasFactory;
    public $timestamps = false;
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
    public function getPriceForDate($date)
    {
        return $this->productDailyPrices()->whereDate('created_at', $date)->latest('id')->first();
    }
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ProductReturn::class);
    }

    public function getCurrentStock(): float
    {
        return $this->inventory()
            ->where('created_at', '<=', today())
            ->sum('transaction_quantity');
    }
}
