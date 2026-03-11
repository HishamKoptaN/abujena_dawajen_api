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
use App\Http\Resources\CustomerDailyReportDetailResource;
use App\Http\Resources\ProductPriceResource;
use App\Models\CustomerDailyReport;
use App\Helpers\DateHelper;
use App\Http\Requests\GetDailyReportRequest;

class CustomerDailyReportsApiController extends Controller
{
    public function index(GetDailyReportRequest $request): JsonResponse
    {   
        $productDailyPrices = $this->getDailyPrices($request->date);
        $customerDailyReports = $this->getCustomerDailyReports($request->date);
        return response()->json([
             'product_daily_prices' => ProductPriceResource::collection($productDailyPrices),
             'customer_daily_reports' => CustomerDailyReportResource::collection($customerDailyReports),
        ]);
    }
    private function getDailyPrices(Carbon $targetDate)
    {
        return Product::all()->map(function ($product) use ($targetDate) {
            $product->dailyPrice = $product->getPriceForDate($targetDate);
            return $product;
        });
    }
    private function getCustomerDailyReports(Carbon $targetDate)
    {
        $customers = Customer::orderBy('number')->get();
        $customerDailyReports = collect();
        
        foreach ($customers as $customer) {
            $dailyReport = CustomerDailyReport::getOrCreateForCustomer($customer->id, $targetDate);
            $customerDailyReports->push($dailyReport->load('customer'));
        }
        return $customerDailyReports;
    }
    public function show($id): JsonResponse
    {
        $dailyReport = CustomerDailyReport::findOrFail($id);
        $reportDate = $dailyReport->created_at->toDateString();
        return response()->json(
            new CustomerDailyReportDetailResource($dailyReport)
        );
    }
    public function getStatementByDays(Request $request, $customerId)
    {
        $request->validate([
            'days' => 'nullable|integer|min:1|max:365'
        ]);
        $days = $request->query('days', 2);
        $endDate = now()->endOfDay();
        $startDate = now()->subDays((int)$days - 1)->startOfDay();
        $customer = Customer::find($customerId);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'العميل غير     موجود'], 404);
        }
        $dailyReports = CustomerDailyReport::where('customer_id', $customerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        $detailedReports = [];
        foreach ($dailyReports as $dailyReport) {
            $detailedReports[] = new CustomerDailyReportDetailResource($dailyReport);
        }
        $summary = [
            'opening_balance' => optional($dailyReports->last())->yesterday_closed_balance ?? 0,
            'closing_balance' => optional($dailyReports->first())->closing_balance ?? 0,
            'total_period_collections' => $dailyReports->sum('total_collections'),
            'reports_count' => $dailyReports->count(),
        ];
        return response()->json([
            'period' => [
                'from' => $startDate->toDateString(),
                'to' => $endDate->toDateString(),
            ],
            'data' => $detailedReports
        ]);
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
