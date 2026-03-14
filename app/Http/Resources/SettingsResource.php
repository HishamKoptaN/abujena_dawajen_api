<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'key' => $this->resource->key,
            'value' => $this->getFormattedValue(),
            'type' => $this->resource->type,
            'description' => $this->resource->description,
            'group' => $this->resource->group,
            'is_public' => $this->resource->is_public,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }

    /**
     * Get formatted value based on type
     */
    private function getFormattedValue()
    {
        return match ($this->resource->type) {
            'boolean' => (bool) $this->resource->value,
            'number' => is_numeric($this->resource->value) ? (float) $this->resource->value : $this->resource->value,
            'json' => json_decode($this->resource->value, true) ?: $this->resource->value,
            default => $this->resource->value,
        };
    }
}
