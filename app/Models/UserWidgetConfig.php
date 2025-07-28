<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWidgetConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'widget_id',
        'position',
        'config',
        'is_enabled',
        'sort_order',
        'last_accessed',
        'metadata'
    ];

    protected $casts = [
        'position' => 'array',
        'config' => 'array',
        'metadata' => 'array',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
        'last_accessed' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(DashboardWidget::class, 'widget_id', 'widget_id');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
