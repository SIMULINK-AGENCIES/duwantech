<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'label',
        'description',
        'is_public',
        'is_required',
        'sort_order'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_required' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected $appends = ['typed_value'];

    /**
     * Get the typed value based on the setting type
     */
    public function getTypedValueAttribute()
    {
        return match ($this->attributes['type'] ?? 'string') {
            'boolean' => filter_var($this->attributes['value'] ?? '', FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) ($this->attributes['value'] ?? 0),
            'float' => (float) ($this->attributes['value'] ?? 0),
            'array' => json_decode($this->attributes['value'] ?? '[]', true) ?? [],
            'json' => json_decode($this->attributes['value'] ?? '{}', true),
            default => $this->attributes['value'] ?? ''
        };
    }

    /**
     * Set the value based on the setting type
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match ($this->attributes['type'] ?? 'string') {
            'array', 'json' => is_string($value) ? $value : json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value
        };
    }

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "general_setting.{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->typed_value : $default;
        });
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, $value): bool
    {
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            $setting->value = $value;
            $result = $setting->save();
        } else {
            $result = static::create([
                'key' => $key,
                'value' => $value,
                'label' => ucwords(str_replace('_', ' ', $key)),
                'category' => 'general'
            ]);
        }

        // Clear cache
        Cache::forget("general_setting.{$key}");
        Cache::forget('general_settings_public');
        
        return (bool) $result;
    }

    /**
     * Get all public settings (for frontend)
     */
    public static function getPublicSettings(): array
    {
        return Cache::remember('general_settings_public', 3600, function () {
            return static::where('is_public', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get()
                ->pluck('typed_value', 'key')
                ->toArray();
        });
    }

    /**
     * Get settings by category
     */
    public static function getByCategory(string $category): array
    {
        $cacheKey = "general_settings_category.{$category}";
        
        return Cache::remember($cacheKey, 3600, function () use ($category) {
            return static::where('category', $category)
                ->orderBy('sort_order')
                ->get()
                ->pluck('typed_value', 'key')
                ->toArray();
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('general_settings_public');
        
        // Clear category caches
        $categories = static::distinct('category')->pluck('category');
        foreach ($categories as $category) {
            Cache::forget("general_settings_category.{$category}");
        }
        
        // Clear individual setting caches
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("general_setting.{$key}");
        }
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("general_setting.{$setting->key}");
            Cache::forget('general_settings_public');
            Cache::forget("general_settings_category.{$setting->category}");
        });

        static::deleted(function ($setting) {
            Cache::forget("general_setting.{$setting->key}");
            Cache::forget('general_settings_public');
            Cache::forget("general_settings_category.{$setting->category}");
        });
    }

    /**
     * Scope for public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
