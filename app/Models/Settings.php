<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Settings extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];
    protected $casts = [
        'value' => 'string',
    ];
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'number' => is_numeric($setting->value) ? (float) $setting->value : $default,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }
    public static function set(string $key, $value): void
    {
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            $setting->value = is_array($value) ? json_encode($value) : $value;
            $setting->save();
        }

        Cache::forget("settings.{$key}");
    }
    public static function getPublic()
    {
        return Cache::remember('settings.public', 3600, function () {
            return static::where('is_public', true)
                ->get()
                ->mapWithKeys(function ($setting) {
                    $value = match ($setting->type) {
                        'boolean' => (bool) $setting->value,
                        'number' => is_numeric($setting->value) ? (float) $setting->value : $setting->value,
                        'json' => json_decode($setting->value, true),
                        default => $setting->value,
                    };
                    
                    return [$setting->key => $value];
                });
        });
    }
    public static function clearCache(): void
    {
        Cache::forget('settings.public');
        static::all()->each(function ($setting) {
            Cache::forget("settings.{$setting->key}");
        });
    }
    protected static function booted()
    {
        static::updated(function () {
            static::clearCache();
        });
        static::created(function () {
            static::clearCache();
        });
        static::deleted(function () {
            static::clearCache();
        });
    }
}
