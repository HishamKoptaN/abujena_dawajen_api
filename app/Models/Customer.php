<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CustomerDailyReport;
use App\Models\Order;
use App\Models\ProductReturn;
class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'number',
        'address',
        'notes',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'number' => 'integer'
    ];
    protected static function booted()
    {
       static::retrieved(function ($customer) {
           $targetDate = today()->toDateString();
           $customer->ensureDailyReportExists($targetDate);
       });
    }
    public function ensureDailyReportExists($date)
    {
        $exists =CustomerDailyReport::where('customer_id', $this->id)
                    ->whereDate('created_at', $date)
                    ->exists();
        if (!$exists) {
            $lastReport = CustomerDailyReport::where('customer_id', $this->id)
                            ->whereDate('created_at', '<', $date)
                            ->orderBy('created_at', 'desc')
                            ->first();
            $lastClosingBalance = $lastReport ? $lastReport->closing_balance : 0;
            CustomerDailyReport::create([
                'customer_id'       => $this->id,
                'closing_balance'   => $lastClosingBalance,
                'created_at'        => now(),
            ]);
        }
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }

    public function getTodayTransactions()
    {
        return $this->transactions()->whereDate('created_at', today());
    }

    public function getTodayCollection()
    {
        return $this->collections()->whereDate('created_at', today())->first();
    }

    public function getTodayBalance()
    {
        $totalTransactions = $this->getTodayTransactions()->sum(function ($transaction) {
            return $transaction->getTotalAmountAttribute();
        });
        
        $collection = $this->getTodayCollection();
        $collectedAmount = $collection ? $collection->amount : 0;
        
        return $totalTransactions - $collectedAmount;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderByNumber($query)
    {
        return $query->orderBy('number')->orderBy('name');
    }
}
