<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\ProductDailyPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Resources\CustomerDailyReportResource;
use App\Models\CustomerDailyReport;
use Illuminate\Validation\Rule;

class TransactionsDetailsApiController extends Controller
{
    public function show($id): JsonResponse
    {
        $transaction = TransactionDetail::findOrFail($id);
        return response()->json($transaction);
    }
    public function update(Request $request, $id)
    {
        $transaction = TransactionDetail::findOrFail($id);
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'weight' => 'required|numeric|min:0',
                'cage' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'date' => 'nullable|date',
            ]);
            $transaction->update($validated);
            $transaction->touch();
            DB::commit();
            $dailyReport = CustomerDailyReport::getOrCreateForCustomer($transaction->transaction->customer_id);
            return response()->json( 
                new CustomerDailyReportResource($dailyReport)
            );
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
        $transaction = TransactionDetail::findOrFail($id);
        DB::beginTransaction();
        try {
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
