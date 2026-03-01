<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
    public function getTotalAmountAttribute()
    {
        return $this->transactionDetails->sum(function ($detail) {
            return ($detail->quantity * $detail->price_at_time) - $detail->discount;
        });
    }
    public function getTotalQuantityAttribute()
    {
        return $this->transactionDetails->sum('quantity');
    }
    public function getTotalCagesAttribute()
    {
        return $this->transactionDetails->sum('cage');
    }
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
