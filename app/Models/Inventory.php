<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Inventory extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'quantity',
        'date',
        'transaction_type',
        'transaction_quantity',
        'notes'
    ];
    protected $casts = [
        'quantity' => 'decimal:2',
        'transaction_quantity' => 'decimal:2',
        'date' => 'date'
    ];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }
    public function scopeIn($query)
    {
        return $query->where('transaction_type', 'in');
    }
    public function scopeOut($query)
    {
        return $query->where('transaction_type', 'out');
    }
    public function scopeAdjustment($query)
    {
        return $query->where('transaction_type', 'adjustment');
    }
    public static function getCurrentStock($productId): float
    {
        return self::where('product_id', $productId)
            ->where('date', '<=', today())
            ->sum('transaction_quantity');
    }
}
