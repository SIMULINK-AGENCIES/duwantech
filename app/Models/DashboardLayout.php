<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardLayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug', 
        'description',
        'layout_config',
        'widget_positions',
        'type',
        'is_active',
        'is_public',
        'created_by',
        'usage_count',
        'metadata'
    ];

    protected $casts = [
        'layout_config' => 'array',
        'widget_positions' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'usage_count' => 'integer'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('type', 'system');
    }
}
