<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardWidget extends Model
{
    use HasFactory;

    protected $fillable = [
        'widget_id',
        'name',
        'description',
        'category',
        'size',
        'component_path',
        'default_config',
        'config_schema',
        'data_endpoint',
        'refresh_interval',
        'is_premium',
        'is_active',
        'cache_enabled',
        'permissions',
        'icon',
        'preview_image',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'default_config' => 'array',
        'config_schema' => 'array',
        'permissions' => 'array',
        'metadata' => 'array',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
        'cache_enabled' => 'boolean',
        'refresh_interval' => 'integer',
        'sort_order' => 'integer'
    ];

    public function userConfigs(): HasMany
    {
        return $this->hasMany(UserWidgetConfig::class, 'widget_id', 'widget_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }
}
