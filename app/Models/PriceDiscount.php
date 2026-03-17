<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'product_id',
        'discount_value',
    ];
    protected $casts = [
        'discount_value' => 'decimal:2',
    ];
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
