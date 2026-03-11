<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
class CustomerDailyReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'closing_balance',
    ];
    protected $casts = [
        'closing_balance' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected static function boot()
    {
        parent::boot();
        static::retrieved(function ($model) {
            if ($model->created_at->isToday()) {
                $model->updateClosingBalance();
            }
        });
    }
    protected function totalTransactionsAmount(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (int) $this->customer->transactions()
                    ->whereDate('created_at', $this->created_at->toDateString())
                    ->with('transactionDetails')
                    ->get()
                    ->sum(function ($transaction) {
                        return $transaction->transactionDetails->sum(function     ($detail) {
                            return ($detail->weight * $detail->price_at_time) -     $detail->discount;
                        });
                }) - $this->getReturnsTotal();
            }
        );
    }
    public function updateClosingBalance(): void
    {
        $targetDate = $this->created_at->startOfDay();
        $previousClosingBalance = $this->getYesterdayClosedBalance();
        $todayTransactionAmount = $this->customer->transactions()
            ->whereDate('created_at', $targetDate)
            ->with('transactionDetails')
            ->get()
            ->sum(function ($t) {
                return $t->transactionDetails->sum(fn($d) => ($d->weight *     $d->price_at_time) - $d->discount);
            });
        $todayCollected = (float) $this->customer->collections()
            ->whereDate('created_at', $targetDate)
            ->sum('amount');
        $todayReturns = (float) ProductReturn::where('customer_id',     $this->customer_id)
            ->whereDate('created_at', $targetDate)
            ->get()
            ->sum(function ($return) use ($targetDate) {
                $price = $return->product->getPriceForDate($targetDate);
                return $return->weight * ($price->price ?? 100);
            });
        $newClosingBalance = $previousClosingBalance + $todayTransactionAmount -     $todayCollected - $todayReturns;
        if ($this->closing_balance != $newClosingBalance) {
            $this->withoutEvents(fn() => $this->update(['closing_balance' =>     $newClosingBalance]));
        }
    }
    public function getYesterdayClosedBalance(): float
    {
        $previousReport = self::where('customer_id', $this->customer_id)
            ->where('created_at', '<', $this->created_at->startOfDay())
            ->orderBy('created_at', 'desc')
            ->first();
    
        return $previousReport ? (float)$previousReport->closing_balance : 0.00;
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
        $lastAvailableReport = self::where('customer_id', $customerId)
            ->where('created_at', '<', $targetDate->startOfDay())
            ->orderBy('created_at', 'desc')
            ->first();
        $previousClosingBalance = $lastAvailableReport ? $lastAvailableReport->closing_balance : 0.00;
        return self::create([
            'customer_id'     => $customerId,
            'closing_balance' => $previousClosingBalance,
            'created_at'      => $targetDate,
            'updated_at'      => $targetDate
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
    public static function getAccountStatement(int $customerId, $startDate = null, $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $end = $endDate ? Carbon::parse($endDate) : Carbon::now();
        
        $customer = Customer::find($customerId);
        if (!$customer) {
            return [
                'customer' => null,
                'period' => [
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString()
                ],
                'summary' => [],
                'daily_details' => [],
                'transactions' => [],
                'collections' => []
            ];
        }
        $openingBalanceReport = self::where('customer_id', $customerId)
            ->where('created_at', '<', $start->startOfDay())
            ->orderBy('created_at', 'desc')
            ->first();
        $openingBalance = $openingBalanceReport ? $openingBalanceReport->closing_balance : 0.00;
        $transactions = Transaction::where('customer_id', $customerId)
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->with(['transactionDetails.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        $collections = Collection::where('customer_id', $customerId)
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();
        $dailyReports = self::where('customer_id', $customerId)
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();
        $totalSales = $transactions->sum(function ($transaction) {
            return $transaction->transactionDetails->sum(function ($detail) {
                return ($detail->quantity * $detail->price_at_time) - $detail->discount;
            });
        });
        $totalCollections = $collections->sum('amount');
        $closingBalanceReport = $dailyReports->first();
        $closingBalance = $closingBalanceReport ? $closingBalanceReport->closing_balance : ($openingBalance + $totalSales - $totalCollections);
        $dailyDetails = [];
        $currentDate = $start->copy();
        while ($currentDate->lte($end)) {
            $dateString = $currentDate->toDateString();
            $dayTransactions = $transactions->filter(function ($transaction) use ($dateString) {
                return $transaction->created_at->toDateString() === $dateString;
            });
            $dayCollections = $collections->filter(function ($collection) use ($dateString) {
                return $collection->created_at->toDateString() === $dateString;
            });
            $dayReport = $dailyReports->firstWhere('created_at.toDateString', $dateString);
            $daySales = $dayTransactions->sum(function ($transaction) {
                return $transaction->transactionDetails->sum(function ($detail) {
                    return ($detail->quantity * $detail->price_at_time) - $detail->discount;
                });
            });
            $dayCollected = $dayCollections->sum('amount');
            $dailyDetails[] = [
                'date' => $dateString,
                'transactions_count' => $dayTransactions->count(),
                'collections_count' => $dayCollections->count(),
                'collections_amount' => $dayCollected,
                'net_change' => $daySales - $dayCollected,
                'closing_balance' => $dayReport ? $dayReport->closing_balance : null,
                'transactions' => $dayTransactions->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'created_at' => $transaction->created_at->toISOString(),
                        'total_amount' => $transaction->total_amount,
                        'total_quantity' => $transaction->total_quantity,
                        'details' => $transaction->transactionDetails->map(function ($detail) {
                            return [
                                'product_name' => $detail->product->name ?? 'Unknown',
                                'quantity' => $detail->quantity,
                                'price_at_time' => $detail->price_at_time,
                                'discount' => $detail->discount,
                                'total' => ($detail->quantity * $detail->price_at_time) - $detail->discount
                            ];
                        })
                    ];
                }),
                'collections' => $dayCollections->map(function ($collection) {
                    return [
                        'id' => $collection->id,
                        'created_at' => $collection->created_at->toISOString(),
                        'amount' => $collection->amount,
                        'notes' => $collection->notes
                    ];
                })
            ];
            $currentDate->addDay();
        }
        return [
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'number' => $customer->number
            ],
            'period' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'days_count' => $start->diffInDays($end) + 1
            ],
            'summary' => [
                'opening_balance' => $openingBalance,
                'total_sales' => $totalSales,
                'total_collections' => $totalCollections,
                'net_change' => $totalSales - $totalCollections,
                'closing_balance' => $closingBalance,
                'transactions_count' => $transactions->count(),
                'collections_count' => $collections->count()
            ],
            'daily_details' => $dailyDetails,
            'all_transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'created_at' => $transaction->created_at->toISOString(),
                    'total_amount' => $transaction->total_amount,
                    'total_quantity' => $transaction->total_quantity,
                    'details' => $transaction->transactionDetails->map(function ($detail) {
                        return [
                            'product_name' => $detail->product->name ?? 'Unknown',
                            'quantity' => $detail->quantity,
                            'price_at_time' => $detail->price_at_time,
                            'discount' => $detail->discount,
                            'total' => ($detail->quantity * $detail->price_at_time) - $detail->discount
                        ];
                    })
                ];
            }),
            'all_collections' => $collections->map(function ($collection) {
                return [
                    'id' => $collection->id,
                    'created_at' => $collection->created_at->toISOString(),
                    'amount' => $collection->amount,
                    'notes' => $collection->notes
                ];
            })
        ];
    }
    public function getReturnsTotal()
    {
        $returns = ProductReturn::where('customer_id', $this->customer->id)
            ->whereDate('created_at', $this->created_at)
            ->with(['product'])
            ->get();
            
        $returnsTotal = 0;
        
        foreach ($returns as $return) {
            $product = $return->product;
            if ($product) {
                $priceForDate = $product->getPriceForDate($this->created_at);
                if ($priceForDate && $priceForDate->price > 0) {
                    $returnAmount = $return->weight * $priceForDate->price;
                    $returnsTotal += $returnAmount;
                } else {
                    $defaultPrice = 100;
                    $returnAmount = $return->weight * $defaultPrice;
                    $returnsTotal += $returnAmount;
                }
            }
        }
        
        return (float)$returnsTotal;
    }
}
