<?php
namespace App\Casts;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class PreciseDouble implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }
        return (double) round((double) $value, 1);
    }
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}