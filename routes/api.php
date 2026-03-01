<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\CustomersApiController;
use App\Http\Controllers\Api\ProductsApiController;
use App\Http\Controllers\Api\DailyOrdersApiController;
use App\Http\Controllers\Api\InventoryApiController;
use App\Http\Controllers\Api\PaymentsApiController;
use App\Http\Controllers\Api\DailyCollectionsApiController;
use App\Http\Controllers\Api\CustomerDailyReportsApiController;
use App\Http\Controllers\Api\TransactionsApiController;

Route::apiResource('customer-daily-reports', CustomerDailyReportsApiController::class);
Route::apiResource('transactions', TransactionsApiController::class);
Route::apiResource('customers', CustomersApiController::class);
Route::apiResource('products', ProductsApiController::class);
Route::post('products/price', [ProductsApiController::class, 'storePrice']);
Route::get('products/today-prices', [ProductsApiController::class, 'getTodayPrices']);
Route::get('products/{productId}/price-history/{days?}', [ProductsApiController::class, 'getPriceHistory']);
Route::apiResource('daily-collections', DailyCollectionsApiController::class);
Route::apiResource('daily-orders', DailyOrdersApiController::class);
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Dashboard routes are working ✅',
        'timestamp' => now(),
        'route' => 'api.test'
    ], 200);
});
