<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductDailyPrice;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\DailyCollection;
use App\Http\Resources\CustomerDailyReportResource;
use App\Http\Resources\ProductDailyPriceResource;
use App\Models\CustomerDailyReport;

class CustomerDailyReportsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {   
       $targetDate = $request->date ? $this->parseArabicDate($request->date) : today();
       $dateString = $targetDate->toDateString();
       $productDailyPrices = ProductDailyPrice::with(['product'])
            ->whereNotNull('price')
            ->orderBy('product_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('product_id');
        $todayPrices = ProductDailyPrice::whereDate('created_at', $targetDate)
            ->pluck('product_id')
            ->toArray();
        $products = Product::all();
        foreach ($products as $product) {
            if (!in_array($product->id, $todayPrices)) {
                ProductDailyPrice::create([
                    'product_id' => $product->id,
                    'price' => null,
                    'created_at' => $targetDate,
                    'updated_at' => $targetDate
                ]);
            }
        }
        $customers = Customer::orderBy('number')->get();
        $customerDailyReports = collect();
        foreach ($customers as $customer) {
            $dailyReport = CustomerDailyReport::getOrCreateForCustomer($customer->id, $dateString);
            $customerDailyReports->push($dailyReport->load('customer'));
        }
        return response()->json([
             'product_daily_prices' => ProductDailyPriceResource::collection($productDailyPrices),
             'customer_daily_reports' => CustomerDailyReportResource::collection($customerDailyReports),
        ]);
    }
    private function parseArabicDate($dateString)
    {
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            $patterns = [
                '/(\d{1,2})\/(\d{1,2})\/(\d{4})/' => function($matches) {
                    return Carbon::createFromDate($matches[3], $matches[2], $matches[1]);
                },
                '/(\d{4})\/(\d{1,2})\/(\d{1,2})/' => function($matches) {
                    return Carbon::createFromDate($matches[1], $matches[2], $matches[3]);
                },
                '/(\d{1,2})-(\d{1,2})-(\d{4})/' => function($matches) {
                    return Carbon::createFromDate($matches[3], $matches[2], $matches[1]);
                },
            ];
            foreach ($patterns as $pattern => $callback) {
                if (preg_match($pattern, $dateString, $matches)) {
                    return $callback($matches);
                }
            }
            return today();
        }
    }
    
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'report_date' => 'required|date',
        ]);
        $reportDate = Carbon::parse($request->report_date);
        $previousReport = CustomerDailyReport::where('customer_id', $request->customer_id)
            ->whereDate('created_at', $reportDate->copy()->subDay())
            ->first();
        $openingBalance = $previousReport ? $previousReport->closing_balance : 0;
        $totalSales = Transaction::where('customer_id', $request->customer_id)
            ->whereDate('created_at', $reportDate)
            ->with(['transactionDetails.product'])
            ->get()
            ->sum(function ($transaction) {
                return $transaction->transactionDetails->sum(function ($detail) {
                    return ($detail->quantity * $detail->price_at_time) - $detail->discount;
                });
            });
        $totalCollections = DailyCollection::where('customer_id', $request->customer_id)
            ->whereDate('created_at', $reportDate)
            ->sum('amount');
        $closingBalance = $openingBalance + $totalSales - $totalCollections;
        $dailyReport = CustomerDailyReport::create([
            'customer_id' => $request->customer_id,
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'created_at' => $reportDate,
            'updated_at' => $reportDate
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء التقرير اليومي بنجاح',
            'data' => $dailyReport
        ]);
    }
}
