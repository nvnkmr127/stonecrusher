<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Boot the model and register event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when a setting is saved
        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });

        // Clear cache when a setting is deleted
        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });
    }

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
