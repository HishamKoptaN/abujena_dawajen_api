<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\ProductDailyPrice;
use App\Models\PriceDiscount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Resources\CustomerDailyReportResource;
use App\Models\CustomerDailyReport;
class TransactionsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date'
        ]);
        $transactions = Transaction::with(['transactionDetails.product'])
            ->where('customer_id', $request->customer_id)
            ->whereDate('created_at', $request->date)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($transactions);
    }
    public function show($id): JsonResponse
    {
        $transaction = Transaction::with(['transactionDetails.product'])
            ->findOrFail($id);
        return response()->json($transaction);
    }
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'weight' => 'required|numeric|min:0.1',
            'cage' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
        ]);
        $transactionDate = $request->date ? Carbon::parse($request->date) : now();
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'customer_id' => $request->customer_id,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ]);
            $product = Product::findOrFail($request->product_id);
            $dailyPrice = ProductDailyPrice::where('product_id', $request->product_id)
                ->whereDate('created_at', $transactionDate)
                ->whereNotNull('price')
                ->orderBy('created_at', 'desc')
                ->first();
            $priceDiscount = PriceDiscount::where('customer_id', $request->customer_id)
                ->where('product_id', $request->product_id)
                ->first();
            $finalPrice = $dailyPrice->price;
            if ($priceDiscount) {
                $finalPrice = $dailyPrice->price - $priceDiscount->discount_value;
            }
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $request->product_id,
                'weight' => $request->weight,
                'cage' => $request->cage ?? 0,
                'price_at_time' => $finalPrice,
                'discount' => $request->discount ?? 0,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ]);
            DB::commit();
            $todayProductWeight = TransactionDetail::where('product_id', $request->product_id)
                ->whereHas('transaction', function($query) use ($transactionDate) {
                    $query->whereDate('created_at', $transactionDate);
                })
                ->sum('weight');
            $dailyReport = CustomerDailyReport::getOrCreateForCustomer($request->customer_id);
            return response()->json( 
                new CustomerDailyReportResource($dailyReport)
            );    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل العملية: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'weight' => 'sometimes|required|numeric|min:0.1',
            'cage' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);
        $transaction = Transaction::findOrFail($id);
        DB::beginTransaction();
        try {
            if ($request->hasAny(['product_id', 'weight', 'cage', 'discount'])) {
                $transaction->transactionDetails()->delete();
                $product = Product::findOrFail($request->product_id ?? $transaction->transactionDetails()->first()?->product_id);
                $dailyPrice = ProductDailyPrice::where('product_id', $product->id)
                    ->whereDate('created_at', $transaction->created_at)
                    ->first();
                if (!$dailyPrice) {
                    $dailyPrice = ProductDailyPrice::where('product_id', $product->id)
                        ->whereNotNull('price')
                        ->orderBy('created_at', 'desc')
                        ->first();
                }
                if (!$dailyPrice || $dailyPrice->price === null) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "لم يتم تحديد سعر للمنتج {$product->name}"
                    ], 400);
                }
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $request->product_id ?? $product->id,
                    'weight' => $request->weight ?? $transaction->transactionDetails()->first()?->weight,
                    'cage' => $request->cage ?? 0,
                    'price_at_time' => $dailyPrice->price,
                    'discount' => $request->discount ?? 0,
                ]);
            }
            $transaction->touch();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث العملية بنجاح',
                'data' => $transaction->load(['customer', 'transactionDetails.product'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تحديث العملية: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);
        DB::beginTransaction();
        try {
            $transaction->transactionDetails()->delete();
            $transaction->delete();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف العملية بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حذف العملية: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getCustomerTransactions($customerId): JsonResponse
    {
        $transactions = Transaction::with(['transactionDetails.product'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }
    public function getDailyTransactions($date = null): JsonResponse
    {
        $targetDate = $date ? Carbon::parse($date) : today();
        $transactions = Transaction::with(['customer', 'transactionDetails.product'])
            ->whereDate('created_at', $targetDate)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $transactions,
            'summary' => [
                'total_transactions' => $transactions->count(),
                'total_amount' => $transactions->sum('total_amount'),
                'total_weight' => $transactions->sum('total_weight'),
            ]
        ]);
    }
    public function getTransactionStats(Request $request): JsonResponse
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now();
        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['transactionDetails.product']);
        $stats = [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->get()->sum('total_amount'),
            'total_weight' => $transactions->get()->sum('total_weight'),
            'unique_customers' => $transactions->distinct('customer_id')->count('customer_id'),
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ];
        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}
