<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collection extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'amount',
        'notes'
    ];
    protected $casts = [
        'amount' => 'decimal:2',
    ];
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeCollected($query)
    {
        return $query->where('status', 'collected');
    }
    public function scopeForDate($query, $date)
    {
        return $query->where('collection_date', $date);
    }
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
