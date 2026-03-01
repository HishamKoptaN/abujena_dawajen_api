<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CustomerDailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'closing_balance',
    ];
    protected $casts = [
        'closing_balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected static function boot()
    {
        parent::boot();
        static::retrieved(function ($model) {
            $model->updateClosingBalance();
        });
    }
    public function updateClosingBalance(): void
    {
        $targetDate = $this->created_at->startOfDay();
        $yesterday = $targetDate->copy()->subDay();
        $yesterdayTransactions = $this->customer->transactions()
            ->whereDate('created_at', $yesterday)
            ->with('transactionDetails')
            ->get();
        $yesterdayCollections = $this->customer->dailyCollections()
            ->whereDate('created_at', $yesterday)
            ->get();
        $yesterdayTransactionAmount = $yesterdayTransactions->sum(function ($transaction) {
            return $transaction->transactionDetails->sum(function ($detail) {
                return ($detail->weight * $detail->price_at_time) - $detail->discount;
            });
        });
        $yesterdayCollected = (float)$yesterdayCollections->sum('amount');
        $yesterdayClosedBalance = (float)$this->customer->opening_balance + $yesterdayTransactionAmount - $yesterdayCollected;
        $todayTransactions = $this->customer->transactions()
            ->whereDate('created_at', $targetDate)
            ->with('transactionDetails')
            ->get();
        $todayCollections = $this->customer->dailyCollections()
            ->whereDate('created_at', $targetDate)
            ->get();
        $todayTransactionAmount = $todayTransactions->sum(function ($transaction) {
            return $transaction->transactionDetails->sum(function ($detail) {
                return ($detail->weight * $detail->price_at_time) - $detail->discount;
            });
        });
        $todayCollected = (float)$todayCollections->sum('amount');
        $newClosingBalance = $yesterdayClosedBalance + $todayTransactionAmount - $todayCollected;
        if ($this->closing_balance != $newClosingBalance) {
            $this->withoutEvents(function () use ($newClosingBalance) {
                $this->update(['closing_balance' => $newClosingBalance]);
            });
        }
    }
    public function getYesterdayClosedBalance(): float
    {
        $yesterday = $this->created_at->copy()->subDay();
        $yesterdayTransactions = $this->customer->transactions()
            ->whereDate('created_at', $yesterday)
            ->with('transactionDetails')
            ->get();
        $yesterdayCollections = $this->customer->dailyCollections()
            ->whereDate('created_at', $yesterday)
            ->get();
        $totalTransactionAmount = $yesterdayTransactions->sum(function ($transaction) {
            return (float)$transaction->transactionDetails->sum(function ($detail) {
                return (float)(($detail->weight * $detail->price_at_time) - $detail->discount);
            });
        });
        $totalCollected = (float)$yesterdayCollections->sum('amount');
        $openingBalance = (float)$this->customer->opening_balance;
        return (int)($openingBalance + $totalTransactionAmount - $totalCollected);
    }
    public static function getOrCreateForCustomer(int $customerId, $date = null)
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();
        $dateString = $targetDate->toDateString();
        $report = self::where('customer_id', $customerId)
            ->whereDate('created_at', $dateString)
            ->first();
        if ($report) {
            return $report;
        }
        return self::create([
            'customer_id'     => $customerId,
            'created_at'      => $targetDate,
        ]);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
