<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyOrder;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentsApiController extends Controller
{
    public function getCustomerBalance($customerId): JsonResponse
    {
        $customer = Customer::findOrFail($customerId);
        
        // حساب الرصيد الإجمالي
        $totalOrders = DailyOrder::where('customer_id', $customerId)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');
        
        $totalPaid = DailyOrder::where('customer_id', $customerId)
            ->where('status', '!=', 'cancelled')
            ->sum('paid_amount');
        
        $balance = $totalOrders - $totalPaid;

        return response()->json([
            'status' => 'success',
            'data' => [
                'customer' => $customer,
                'total_orders' => $totalOrders,
                'total_paid' => $totalPaid,
                'balance' => $balance,
                'balance_type' => $balance >= 0 ? 'للعميل' : 'على العميل'
            ]
        ]);
    }

    public function storePayment(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($request->customer_id);
            
            // الحصول على الطلبات غير المدفوعة بالترتيب
            $unpaidOrders = DailyOrder::where('customer_id', $request->customer_id)
                ->where('status', 'delivered')
                ->where('remaining_amount', '>', 0)
                ->orderBy('delivery_date', 'asc')
                ->get();

            $paymentAmount = $request->amount;
            $remainingPayment = $paymentAmount;

            foreach ($unpaidOrders as $order) {
                if ($remainingPayment <= 0) break;

                $paymentToApply = min($remainingPayment, $order->remaining_amount);
                $order->paid_amount += $paymentToApply;
                $order->remaining_amount -= $paymentToApply;
                $remainingPayment -= $paymentToApply;

                if ($order->remaining_amount <= 0) {
                    $order->status = 'paid';
                }

                $order->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الدفعة بنجاح',
                'data' => [
                    'payment_amount' => $paymentAmount,
                    'orders_updated' => $unpaidOrders->count()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل الدفعة'
            ], 500);
        }
    }

    public function getPaymentHistory($customerId): JsonResponse
    {
        $payments = DailyOrder::where('customer_id', $customerId)
            ->where('paid_amount', '>', 0)
            ->with(['customer'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_date' => $order->order_date,
                    'delivery_date' => $order->delivery_date,
                    'total_price' => $order->total_price,
                    'paid_amount' => $order->paid_amount,
                    'remaining_amount' => $order->remaining_amount,
                    'status' => $order->status,
                    'notes' => $order->notes,
                    'updated_at' => $order->updated_at
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    public function getDailyPaymentsSummary($date = null): JsonResponse
    {
        $date = $date ?? today();
        
        $summary = DailyOrder::whereDate('date)
            ->where('paid_amount', '>', 0)
            ->selectRaw('
                customer_id,
                SUM(paid_amount) as total_paid,
                COUNT(*) as payment_count
            ')
            ->groupBy('customer_id')
            ->with(['customer:id,name'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $summary
        ]);
    }
}
