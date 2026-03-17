<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CustomerDailyReportResource;
use App\Models\CustomerDailyReport;
use Carbon\Carbon;
class CollectionsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Collection::with(['customer']);
        if ($request->date) {
            $query->forDate($request->date);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $collections = $query->orderBy('collection_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($collections);
    }
    public function show(Request $request, $id): JsonResponse
    {
        $request->validate([
            'date' => 'nullable|date'
        ]);
        $dateString = $request->date->toDateString();
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'العميل غير موجود'
            ], 404);
        }
        $collections = Collection::where('customer_id', $id)
            ->whereDate('created_at', $dateString)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalAmount = $collections->sum('amount');
        $collectionsCount = $collections->count();
        return response()->json([
                'collections' => $collections->map(function ($collection) {
                    return [
                        'id' => $collection->id,
                        'amount' => $collection->amount,
                        'notes' => $collection->notes,
                        'created_at' => $collection->created_at->toISOString(),
                        'updated_at' => $collection->updated_at->toISOString()
                    ];
                }),
                'summary' => [
                    'total_collections' => $collectionsCount,
                    'total_amount' => $totalAmount,
                ]
            ]
        );
    }
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:0.01',
            'date' => 'nullable|date',
        ]);
        try {
            $collection = Collection::create([
                'customer_id' => $request->customer_id,
                'amount'      => $request->amount,
                'created_at'  => $request->date ? Carbon::parse($request->date) : today(),
                'updated_at'  => $request->date ? Carbon::parse($request->date) : today(),
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
    public function update(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
            ]);
            $collection->update($validated);
            $collection->touch();
            DB::commit();
            $dailyReport = CustomerDailyReport::getOrCreateForCustomer($collection->customer_id);
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
    public function getCustomerCollections($customerId): JsonResponse
    {
        $collections = Collection::with(['customer'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $collections
        ]);
    }
   
}
