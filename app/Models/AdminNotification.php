<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'priority',
        'title',
        'message',
        'data',
        'action_url',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'read_at',
    ];

    /**
     * Notification types enum values.
     */
    const TYPE_ORDER = 'order';
    const TYPE_PAYMENT = 'payment';
    const TYPE_INVENTORY = 'inventory';
    const TYPE_USER = 'user';
    const TYPE_SYSTEM = 'system';

    /**
     * Priority levels enum values.
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to filter by notification type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by priority level.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get high priority notifications.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_CRITICAL]);
    }

    /**
     * Scope to get critical notifications.
     */
    public function scopeCritical($query)
    {
        return $query->where('priority', self::PRIORITY_CRITICAL);
    }

    /**
     * Scope to get recent notifications (within last 24 hours).
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDay());
    }

    /**
     * Scope to get notifications from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        return $this->update(['read_at' => Carbon::now()]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        return $this->update(['read_at' => null]);
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Get notification priority color class.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'text-gray-500',
            self::PRIORITY_MEDIUM => 'text-blue-500',
            self::PRIORITY_HIGH => 'text-orange-500',
            self::PRIORITY_CRITICAL => 'text-red-500',
            default => 'text-gray-500',
        };
    }

    /**
     * Get notification type icon class.
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_ORDER => 'fas fa-shopping-cart',
            self::TYPE_PAYMENT => 'fas fa-credit-card',
            self::TYPE_INVENTORY => 'fas fa-boxes',
            self::TYPE_USER => 'fas fa-user',
            self::TYPE_SYSTEM => 'fas fa-cog',
            default => 'fas fa-bell',
        };
    }

    /**
     * Get formatted time since creation.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get all available notification types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_ORDER,
            self::TYPE_PAYMENT,
            self::TYPE_INVENTORY,
            self::TYPE_USER,
            self::TYPE_SYSTEM,
        ];
    }

    /**
     * Get all available priority levels.
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH,
            self::PRIORITY_CRITICAL,
        ];
    }
}
