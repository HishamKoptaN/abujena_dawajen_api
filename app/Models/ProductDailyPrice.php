<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\PreciseDouble;
class ProductDailyPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'price',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
       'price' => PreciseDouble::class,
    ];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
