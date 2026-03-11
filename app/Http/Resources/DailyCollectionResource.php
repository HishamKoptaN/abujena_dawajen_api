<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'amount' => (int)$this->resource->amount,
            'notes' => $this->resource->notes,
            'created_at' => $this->resource->created_at->toISOString(),
            'date' => $this->resource->created_at->toDateString(),
            'time' => $this->resource->created_at->toTimeString()
        ];
    }
}
