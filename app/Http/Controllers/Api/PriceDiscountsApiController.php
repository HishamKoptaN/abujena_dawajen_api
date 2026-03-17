<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriceDiscount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CustomerDailyReportResource;
use App\Models\CustomerDailyReport;

class PriceDiscountsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PriceDiscount::with(['customer', 'product']);
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        $discounts = $query->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $discounts
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'discount_value' => 'required|numeric|min:0|max:100',
        ]);
        try {
            $existingDiscount = PriceDiscount::where('customer_id', $request->customer_id)
                ->where('product_id', $request->product_id)
                ->first();
            if ($existingDiscount) {
                $existingDiscount->update(['discount_value' => $request->discount_value]);
                $discount = $existingDiscount->fresh();
            } else {
                $discount = PriceDiscount::create([
                    'customer_id' => $request->customer_id,
                    'product_id' => $request->product_id,
                    'discount_value' => $request->discount_value,
                ]);
            }

              $dailyReport = CustomerDailyReport::getOrCreateForCustomer($request->customer_id);
            return response()->json( 
                new CustomerDailyReportResource($dailyReport), 201
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حفظ الخصم: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id): JsonResponse
    {
        $discount = PriceDiscount::findOrFail($id);
        try {
            $discount->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف الخصم بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حذف الخصم: ' . $e->getMessage()
            ], 500);
        }
    }
}
