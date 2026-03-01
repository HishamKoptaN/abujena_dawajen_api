<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryApiController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::where('is_active', true)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'unit' => $product->unit,
                    'current_stock' => $product->getCurrentStock()
                ];
            });
        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'transaction_type' => 'required|in:in,out,adjustment',
            'transaction_quantity' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $inventory = Inventory::create([
                'product_id' => $request->product_id,
                'quantity' => Product::findOrFail($request->product_id)->getCurrentStock(),
                'transaction_type' => $request->transaction_type,
                'transaction_quantity' => $request->transaction_type === 'out' 
                    ? -$request->transaction_quantity 
                    : $request->transaction_quantity,
                'date' => $request->date,
                'notes' => $request->notes
            ]);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الحركة بنجاح',
                'data' => $inventory->load('product')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل الحركة'
            ], 500);
        }
    }
    public function getProductHistory($productId, $days = 30): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $transactions = Inventory::with(['product'])
            ->where('product_id', $productId)
            ->where('date', '>=', now()->subDays($days))
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => [
                'product' => $product,
                'current_stock' => $product->getCurrentStock(),
                'transactions' => $transactions
            ]
        ]);
    }
    public function getLowStockProducts(): JsonResponse
    {
        $products = Product::where('is_active', true)
            ->get()
            ->filter(function ($product) {
                return $product->getCurrentStock() <= 10;
            })
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name_ar' => $product->name_ar,
                    'current_stock' => $product->getCurrentStock(),
                    'unit' => $product->unit
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function getDailyTransactions($date = null): JsonResponse
    {
        $date = $date ?? today();
        
        $transactions = Inventory::with(['product'])
            ->where('date', $date)
            ->orderBy('created_at', 'desc')
            ->get();
        $summary = [
            'total_in' => $transactions->where('transaction_type', 'in')->sum('transaction_quantity'),
            'total_out' => abs($transactions->where('transaction_type', 'out')->sum('transaction_quantity')),
            'total_adjustment' => $transactions->where('transaction_type', 'adjustment')->sum('transaction_quantity')
        ];
        return response()->json([
            'status' => 'success',
            'data' => [
                'transactions' => $transactions,
                'summary' => $summary,
                'date' => $date
            ]
        ]);
    }
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'transactions' => 'required|array',
            'transactions.*.product_id' => 'required|exists:products,id',
            'transactions.*.transaction_type' => 'required|in:in,out,adjustment',
            'transactions.*.transaction_quantity' => 'required|numeric|min:0.01',
            'transactions.*.date' => 'required|date',
            'transactions.*.notes' => 'nullable|string'
        ]);
        DB::beginTransaction();
        try {
            $createdTransactions = [];
            
            foreach ($request->transactions as $transactionData) {
                $transaction = Inventory::create([
                    'product_id' => $transactionData['product_id'],
                    'quantity' => Product::findOrFail($transactionData['product_id'])->getCurrentStock(),
                    'transaction_type' => $transactionData['transaction_type'],
                    'transaction_quantity' => $transactionData['transaction_type'] === 'out' 
                        ? -$transactionData['transaction_quantity'] 
                        : $transactionData['transaction_quantity'],
                    'date' => $transactionData['date'],
                    'notes' => $transactionData['notes'] ?? null
                ]);
                
                $createdTransactions[] = $transaction;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل جميع الحركات بنجاح',
                'data' => $createdTransactions
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل الحركات'
            ], 500);
        }
    }
}
