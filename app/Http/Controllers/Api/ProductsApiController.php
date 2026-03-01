<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDailyPrice;
use App\Http\Resources\ProductDailyPriceResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
class ProductsApiController extends Controller
{
    public function show($id): JsonResponse
    {
        $product = Product::with(['productDailyPrices' => function($query) {
            $query->orderBy('date', 'desc')->limit(30);
        }])->findOrFail($id);
        return response()->json(
            $product
        );
    }
    public function storePrice(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
        ]);
        $latestPrice = ProductDailyPrice::where('product_id', $request->product_id)
            ->whereDate('created_at', today())
            ->latest()
            ->first();
        if ($latestPrice && $latestPrice->price == $request->price) {
            return response()->json(new ProductDailyPriceResource($latestPrice));
        }
        if ($latestPrice && ($latestPrice->price === null || $latestPrice->price == 0)) {
            $latestPrice->update(['price' => $request->price]);
            return response()->json(new ProductDailyPriceResource($latestPrice));
        }
        $newPrice = ProductDailyPrice::create([
            'product_id' => $request->product_id,
            'price'      => $request->price,
        ]);
        return response()->json(new ProductDailyPriceResource($newPrice));
    }
    public function getTodayPrices(): JsonResponse
    {
        $prices = ProductDailyPrice::with(['product'])
            ->whereDate('created_at', today())
            ->get()
            ->map(function ($price) {
                return [
                    'product_id' => $price->product_id,
                    'product_name' => $price->product->name_ar,
                    'price' => $price->price,
                    'notes' => $price->notes
                ];
            });
        return response()->json([
            'status' => 'success',
            'data' => $prices
        ]);
    }
    public function getPriceHistory($productId, $days = 30): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $prices = $product->dailyPrices()
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $prices
        ]);
    }
}
