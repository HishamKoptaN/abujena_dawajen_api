<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class CustomerDailyReportDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $targetDate = $this->resource->created_at;
        $todayTransactions = $this->resource->customer->transactions()
            ->whereDate('created_at', $targetDate->toDateString())
            ->with(['transactionDetails.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        $todayCollections = $this->resource->customer->dailyCollections()
            ->whereDate('created_at', $targetDate)
            ->orderBy('created_at', 'desc')
            ->get();
        $todayOrders = $this->resource->customer->dailyOrders()
            ->whereDate('created_at', $targetDate)
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->get();
        $totalTransactionAmount = $todayTransactions->sum(function ($transaction) {
            return $transaction->transactionDetails->sum(function ($detail) {
                return ($detail->weight * $detail->price_at_time) - $detail->discount;
            });
        });
        $totalCollected = $todayCollections->sum('amount');
        $closingBalance = $this->resource->closing_balance;
        if (!$this->resource->created_at->isToday()) {
            $yesterdayClosedBalance = $this->resource->getYesterdayClosedBalance();
            $closingBalance = $yesterdayClosedBalance + $totalTransactionAmount - $totalCollected;
        }
        return [
            'id' => $this->resource->id,
            'yesterday_closed_balance' => (int)$this->resource->getYesterdayClosedBalance(),
            'transactions' => TransactionResource::collection($todayTransactions),
            'total_transactions_amount' => (int)$totalTransactionAmount,
            'collections' => DailyCollectionResource::collection($todayCollections),
            'orders' => DailyOrderResource::collection($todayOrders),
            'closing_balance' => (int)$closingBalance,
            'created_at' => $this->resource->created_at,
        ];
    }
}
