<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CustomerDailyReportResource;
use App\Models\CustomerDailyReport;
class ReturnsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'nullable|date'
        ]);
        $query = ProductReturn::with(['customer', 'product']);
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }
        $returns = $query->orderBy('created_at', 'desc')->get();
        return response()->json($returns);
    }
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'weight' => 'required|numeric|min:0'
        ]);
        return \DB::transaction(function () use ($request) {
            $today = now()->toDateString();
            ProductReturn::create([
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'weight' => $request->weight,
                'created_at' => $today,
                'updated_at' => $today
            ]);
            $dailyReport = CustomerDailyReport::where('customer_id', $request->customer_id)
                ->whereDate('created_at', $today)
                ->first();
            if (!$dailyReport) {
                $dailyReport = CustomerDailyReport::getOrCreateForCustomer($request->customer_id);
            }
            return response()->json(
                new CustomerDailyReportResource($dailyReport)
            );
        });
    }
    public function show($id): JsonResponse
    {
        $return = ProductReturn::with(['customer', 'product'])->findOrFail($id);
        return response()->json($return);
    }
    public function update(Request $request, $id): JsonResponse
    {
        $return = ProductReturn::findOrFail($id);
        $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'product_id' => 'sometimes|exists:products,id',
            'weight' => 'sometimes|numeric|min:0',
            'reason' => 'sometimes|nullable|string|max:255'
        ]);
        $return->update($request->all());
        return response()->json($return->load(['customer', 'product']));
    }
    public function destroy($id): JsonResponse
    {
        $return = ProductReturn::findOrFail($id);
        $return->delete();
        return response()->json(null, 204);
    }
}
