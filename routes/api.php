<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\CustomersApiController;
use App\Http\Controllers\Api\ProductsApiController;
use App\Http\Controllers\Api\OrdersApiController;
use App\Http\Controllers\Api\InventoryApiController;
use App\Http\Controllers\Api\PaymentsApiController;
use App\Http\Controllers\Api\CollectionsApiController;
use App\Http\Controllers\Api\CustomerDailyReportsApiController;
use App\Http\Controllers\Api\TransactionsApiController;
use App\Http\Controllers\Api\ReturnsApiController;
Route::get('/system-status', function () {
    return response()->json([
        'version' => config('app.version'),
        'status' => 'healthy'
    ]);
});
Route::apiResource('customer-daily-reports', CustomerDailyReportsApiController::class);
Route::get('customer-daily-reports/{customer_id}/statement', [CustomerDailyReportsApiController::class, 'generateAccountStatement']);
Route::apiResource('transactions', TransactionsApiController::class);
Route::apiResource('returns', ReturnsApiController::class);
Route::apiResource('customers', CustomersApiController::class);
Route::apiResource('products', ProductsApiController::class);
Route::post('products/price', [ProductsApiController::class, 'storePrice']);
Route::get('products/today-prices', [ProductsApiController::class, 'getTodayPrices']);
Route::get('products/{productId}/price-history/{days?}', [ProductsApiController::class, 'getPriceHistory']);
Route::apiResource('collections', CollectionsApiController::class);
Route::apiResource('orders', OrdersApiController::class);
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Abu Jena Dawajen API routes are working ✅',
        'timestamp' => now(),
        'route' => 'api.test'
    ], 200);
});
// test 1