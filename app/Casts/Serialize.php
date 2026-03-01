<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Serialize implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {

        try {
            return unserialize($value);
        } catch (\Throwable $th) {
            return $value;
        }

        return $value;
    }
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {

        if (is_array($value)) {
            return serialize($value);
        }

        return $value;
    }
}
