<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyCollection;
use App\Models\Customer;
use App\Models\DailyOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CustomerDailyReportResource;
use App\Models\CustomerDailyReport;

class DailyCollectionsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DailyCollection::with(['customer']);
        if ($request->date) {
            $query->forDate($request->date);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $collections = $query->orderBy('collection_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $collections
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:0.01',
        ]);
        try {
            $collection = DailyCollection::create([
                'customer_id' => $request->customer_id,
                'amount'      => $request->amount,
            ]);
            $dailyReport = CustomerDailyReport::getOrCreateForCustomer($request->customer_id);
            return response()->json( 
                new CustomerDailyReportResource($dailyReport)
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ تقني: ' . $e->getMessage() 
            ], 500);
        }
    }
    public function update(Request $request, $id): JsonResponse
    {
        $collection = DailyCollection::findOrFail($id);
        $request->validate([
            'amount' => 'sometimes|required|numeric|min:0.01',
            'status' => 'sometimes|required|in:pending,collected,cancelled',
        ]);
        $collection->update($request->all());
        if ($collection->wasChanged('status') && $collection->status === 'collected') {
            $this->updateOrdersAfterCollection($collection->customer_id, $collection->collection_date, $collection->chicken_price);
        }
        return response()->json($collection);
    }
    public function getCustomerCollections($customerId): JsonResponse
    {
        $collections = DailyCollection::with(['customer'])
            ->where('customer_id', $customerId)
            ->orderBy('collection_date', 'desc')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $collections
        ]);
    }

    public function getDailyStats($date = null): JsonResponse
    {
        $date = $date ?? today();
        $stats = [
            'total_collections' => DailyCollection::forDate($date)->count(),
            'pending_collections' => DailyCollection::forDate($date)->pending()->count(),
            'collected_collections' => DailyCollection::forDate($date)->collected()->count(),
            'total_amount' => DailyCollection::forDate($date)->where('status', 'collected')->sum('amount'),
            'total_weight' => DailyCollection::forDate($date)->where('status', 'collected')->sum('chicken_weight'),
            'avg_price' => DailyCollection::forDate($date)->where('status', 'collected')->avg('chicken_price')
        ];
        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}
