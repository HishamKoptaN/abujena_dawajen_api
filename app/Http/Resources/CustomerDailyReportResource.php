<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Transaction;
use App\Models\Collection;
use App\Models\Customer;
use App\Models\ProductReturn;
use Carbon\Carbon;

class CustomerDailyReportResource extends JsonResource
{
    public function toArray($request)
    {
        $targetDate = $this->resource->created_at;
        $todayTransactions = $this->resource->customer->transactions()
        ->whereDate('created_at', $targetDate->toDateString())
        ->with(['transactionDetails.product'])
        ->get();
        $todayCollections = $this->resource->customer->collections()
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
        foreach ($productDailyTotals as &$productTotal) {
            $productTotal['total_weight'] = round($productTotal['total_weight'], 2);
            $productTotal['total_amount'] = (int)round($productTotal['total_amount']);
        }
        $totalCollected = (float)$todayCollections->sum('amount');
        return [
            'id' => (int)$this->resource->id,
            'customer' => $this->resource->customer,
            'yesterday_closed_balance' => (int)$this->resource->getYesterdayClosedBalance(),
            'product_orders' => $this->resource->customer->orders()
                ->whereDate('created_at', $targetDate)
                ->with(['product'])
                ->get()
                ->groupBy('product_id')
                ->map(function ($orders, $productId) {
                    $totalCount = $orders->sum('count');
                    return [
                        'product_id' => (int)$productId,
                        'total_count' => (int)$totalCount,
                    ];
                })
                ->values(),
            'product_daily_totals' => array_values($productDailyTotals),
            'total_transactions_amount' => $this->total_transactions_amount,
            'returns' => $this->getReturnsSummary($targetDate),
            'total_collections' => (int)$todayCollections->sum('amount'),
            'closing_balance' => $this->resource->closing_balance,
        ];
    }
    private function getYesterdayClosedBalance(): float
    {
        $yesterday = Carbon::yesterday();
        $customerId = $this->resource->customer->id;
        $yesterdayTransactions = Transaction::where('customer_id', $customerId)
            ->whereDate('created_at', $yesterday)
            ->with('transactionDetails')
            ->get();
        $yesterdayCollections = Collection::where('customer_id', $customerId)
            ->whereDate('created_at', $yesterday)
            ->get();
        $totalTransactionAmount = $yesterdayTransactions->sum(function ($transaction) {
            return (float)$transaction->transactionDetails->sum(function ($detail) {
                return (float)(($detail->weight * $detail->price_at_time) - $detail->discount);
            });
        });
        $totalCollected = (float)$yesterdayCollections->sum('amount');
        return (int)($totalTransactionAmount - $totalCollected);
    }
    private function getReturnsSummary($targetDate)
    {
        $returns = ProductReturn::where('customer_id', $this->resource->customer->id)
            ->whereDate('created_at', $targetDate)
            ->with(['product'])
            ->get();
        $returnsByProduct = $returns->groupBy('product_id');
        $returnsSummary = [];
        foreach ($returnsByProduct as $productId => $productReturns) {
            $product = $productReturns->first()->product;
            $totalWeight = $productReturns->sum('weight');
            $count = $productReturns->count();
            $returnsSummary[] = [
                'product' => $product,
                'total_weight' => (double)$totalWeight,
            ];
        }
        return $returnsSummary;
    }
}