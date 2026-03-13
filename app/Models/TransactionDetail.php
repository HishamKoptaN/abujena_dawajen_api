<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TransactionDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'product_id',
        'weight',
        'cage',
        'price_at_time',
        'discount'
    ];
    protected $casts = [
        'weight' => 'decimal:2',
        'cage' => 'decimal:2',
        'price_at_time' => 'decimal:2',
        'discount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function getSubtotalAttribute()
    {
        return $this->weight * $this->price_at_time;
    }

    public function getTotalAttribute()
    {
        return $this->subtotal - $this->discount;
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }
}
