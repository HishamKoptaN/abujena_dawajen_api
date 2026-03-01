<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Transaction;
use App\Models\DailyCollection;
use App\Models\Customer;
class CustomerDailyReportResource extends JsonResource
{
    public function toArray($request)
    {
        $targetDate = $request->date ? $this->parseArabicDate($request->date) : today();
        $todayTransactions = $this->resource->customer->transactions()
        ->whereDate('created_at', $targetDate->toDateString())
        ->with(['transactionDetails.product'])
        ->get();
        $todayCollections = $this->resource->customer->dailyCollections()
            ->whereDate('created_at', $targetDate)
            ->get();
        $productDailyTotals = [];
        foreach ($todayTransactions as $transaction) {
            foreach ($transaction->transactionDetails as $detail) {
                $productId = $detail->product_id;
                $productName = $detail->product->name;
                if (!isset($productDailyTotals[$productId])) {
                    $productDailyTotals[$productId] = [
                        'product_id' => (int)$productId,
                        'total_weight' => 0,
                        'total_amount' => 0,
                    ];
                }
                $productDailyTotals[$productId]['total_weight'] += (float)$detail->weight;
                $productDailyTotals[$productId]['total_amount'] += (float)(($detail->weight * $detail->price_at_time) - $detail->discount);
            }
        }
        $totalTransactionAmount = $todayTransactions->sum(function ($transaction) {
            return (float)$transaction->transactionDetails->sum(function ($detail) {
                return (float)(($detail->weight * $detail->price_at_time) - $detail->discount);
            });
        });
        $totalCollected = (float)$todayCollections->sum('amount');
        return [
            'id' => (int)$this->resource->id,
            'customer' => $this->resource->customer,
            'yesterday_closed_balance' => (int)$this->resource->getYesterdayClosedBalance(),
            'product_daily_totals' => array_values($productDailyTotals),
            'collections' => $todayCollections->map(function ($collection) {
                return [
                    'id' => (int)$collection->id,
                    'amount' => (double)$collection->amount,
                    'created_at' => $collection->created_at
                ];
            }),
            'closing_balance' => (float)$this->resource->closing_balance,
            'summary' => [
                'total_transaction_amount' => $totalTransactionAmount,
                'total_collected' => $totalCollected,
                'balance' => $totalTransactionAmount - $totalCollected
            ],
            'product_orders' => $this->resource->customer->dailyOrders()
                ->whereDate('created_at', $targetDate)
                ->with(['product'])
                ->get()
                ->groupBy('product_id')
                ->map(function ($orders, $productId) {
                    $totalCount = $orders->sum('count');
                    return [
                        'product_id' => (int)$productId,
                        'total_count' => (float)$totalCount,
                    ];
                })
                ->values()
        ];
    }
    private function parseArabicDate($dateString)
    {
        try {
            return \Carbon\Carbon::parse($dateString);
        } catch (\Exception $e) {
            $patterns = [
                '/(\d{1,2})\/(\d{1,2})\/(\d{4})/' => function($matches) {
                    return \Carbon\Carbon::createFromDate($matches[3], $matches[2], $matches[1]);
                },
                '/(\d{4})\/(\d{1,2})\/(\d{1,2})/' => function($matches) {
                    return \Carbon\Carbon::createFromDate($matches[1], $matches[2], $matches[3]);
                },
                '/(\d{1,2})-(\d{1,2})-(\d{4})/' => function($matches) {
                    return \Carbon\Carbon::createFromDate($matches[3], $matches[2], $matches[1]);
                },
            ];
            foreach ($patterns as $pattern => $callback) {
                if (preg_match($pattern, $dateString, $matches)) {
                    return $callback($matches);
                }
            }
            return \Carbon\Carbon::today();
        }
    }
    private function getYesterdayClosedBalance(): float
    {
        $yesterday = \Carbon\Carbon::yesterday();
        $customerId = $this->resource->customer->id;
        $yesterdayTransactions = Transaction::where('customer_id', $customerId)
            ->whereDate('created_at', $yesterday)
            ->with('transactionDetails')
            ->get();
        $yesterdayCollections = DailyCollection::where('customer_id', $customerId)
            ->whereDate('created_at', $yesterday)
            ->get();
        $totalTransactionAmount = $yesterdayTransactions->sum(function ($transaction) {
            return (float)$transaction->transactionDetails->sum(function ($detail) {
                return (float)(($detail->weight * $detail->price_at_time) - $detail->discount);
            });
        });
        $totalCollected = (float)$yesterdayCollections->sum('amount');
        $openingBalance = (float)$this->resource->customer->opening_balance;
        return (int)($openingBalance + $totalTransactionAmount - $totalCollected);
    }
}