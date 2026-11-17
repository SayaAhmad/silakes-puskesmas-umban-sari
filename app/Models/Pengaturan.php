<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $fillable = ['key', 'value', 'grup'];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.$key", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, $value, string $grup = 'umum'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'grup' => $grup]);
        Cache::forget("setting.$key");
    }

    public static function banyak(array $keys, array $defaults = []): array
    {
        $out = [];
        foreach ($keys as $k) {
            $out[$k] = static::get($k, $defaults[$k] ?? null);
        }

        return $out;
    }
}
