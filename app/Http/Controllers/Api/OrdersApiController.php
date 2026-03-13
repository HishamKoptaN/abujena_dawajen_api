<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductDailyPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Http\Resources\CustomerDailyReportResource;
use App\Models\CustomerDailyReport;

class OrdersApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['customer', 'product']);
        $orders = $query->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'count' => 'required|numeric|min:0.1',
        ]);
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'count' => $request->count,
        ]);
           $dailyReport = CustomerDailyReport::getOrCreateForCustomer($request->customer_id);
            return response()->json( 
                new CustomerDailyReportResource($dailyReport)
            );    
    }
    public function update(Request $request, $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'quantity' => 'sometimes|required|numeric|min:0.1',
            'status' => 'sometimes|required|in:pending,confirmed,delivered,cancelled',
        ]);
        $order->update($request->all());
        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الطلب بنجاح',
            'data' => $order->load(['customer', 'product'])
        ]);
    }
    public function destroy($id): JsonResponse
    {
        $order = Order::findOrFail($id);
        if ($order->status === 'delivered') {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن حذف طلب تم تسليمه'
            ], 400);
        }
        $order->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الطلب بنجاح'
        ]);
    }
    public function getTomorrowOrders(): JsonResponse
    {
        $orders = Order::with(['customer', 'product'])
            ->where('delivery_date', today()->addDay())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }
    public function confirmOrders(Request $request): JsonResponse
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id'
        ]);
        $orders = Order::whereIn('id', $request->order_ids)
            ->where('status', 'pending')
            ->get();
        foreach ($orders as $order) {
            $order->update(['status' => 'confirmed']);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'تم تأكيد الطلبات بنجاح',
            'data' => $orders
        ]);
    }

    public function markAsDelivered(Request $request): JsonResponse
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id'
        ]);
        $orders = Order::whereIn('id', $request->order_ids)
            ->where('status', 'confirmed')
            ->get();
        foreach ($orders as $order) {
            $order->update(['status' => 'delivered']);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'تم تسليم الطلبات بنجاح',
            'data' => $orders
        ]);
    }

    public function getDailyStats($date = null): JsonResponse
    {
        $date = $date ?? today();
        $stats = [
            'total_orders' => Order::where('delivery_date', $date)->count(),
            'pending_orders' => Order::where('delivery_date', $date)->where('status', 'pending')->count(),
            'confirmed_orders' => Order::where('delivery_date', $date)->where('status', 'confirmed')->count(),
            'delivered_orders' => Order::where('delivery_date', $date)->where('status', 'delivered')->count(),
            'total_revenue' => Order::where('delivery_date', $date)->where('status', 'delivered')->sum('total_price'),
            'total_quantity' => Order::where('delivery_date', $date)->sum('quantity')
        ];
        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}
