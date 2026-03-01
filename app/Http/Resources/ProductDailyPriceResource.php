<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class ProductDailyPriceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'product_id' => $this->resource->product_id,
            'product_name' => $this->resource->product->name ?? 'Unknown',
            'price' => (double)$this->resource->price,
            'created_at' => $this->resource->created_at
        ];
    }
}
