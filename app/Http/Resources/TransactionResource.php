<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'created_at' => $this->resource->created_at->toISOString(),
            'date' => $this->resource->created_at->toDateString(),
            'time' => $this->resource->created_at->toTimeString(),
            'total_amount' => (int)$this->resource->transactionDetails->sum(function ($detail) {
                return ($detail->weight * $detail->price_at_time) - $detail->discount;
            }),
            'details' => $this->resource->transactionDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product' => [
                        'id' => $detail->product->id,
                        'name' => $detail->product->name,
                    ],
                    'weight' => (double)$detail->weight,
                    'price_at_time' => (double)$detail->price_at_time,
                    'discount' => (int)$detail->discount,
                    'total' => (int)(($detail->weight * $detail->price_at_time) - $detail->discount)
                ];
            })
        ];
    }
}
