<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Common action types.
     */
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_REGISTER = 'register';
    const ACTION_ORDER_CREATED = 'order_created';
    const ACTION_ORDER_UPDATED = 'order_updated';
    const ACTION_ORDER_CANCELLED = 'order_cancelled';
    const ACTION_PAYMENT_PROCESSED = 'payment_processed';
    const ACTION_PAYMENT_FAILED = 'payment_failed';
    const ACTION_PRODUCT_VIEWED = 'product_viewed';
    const ACTION_CART_UPDATED = 'cart_updated';
    const ACTION_PROFILE_UPDATED = 'profile_updated';
    const ACTION_PASSWORD_CHANGED = 'password_changed';

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (polymorphic relationship).
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get authentication related actions.
     */
    public function scopeAuthActions($query)
    {
        return $query->whereIn('action', [
            self::ACTION_LOGIN,
            self::ACTION_LOGOUT,
            self::ACTION_REGISTER,
        ]);
    }

    /**
     * Scope to get order related actions.
     */
    public function scopeOrderActions($query)
    {
        return $query->whereIn('action', [
            self::ACTION_ORDER_CREATED,
            self::ACTION_ORDER_UPDATED,
            self::ACTION_ORDER_CANCELLED,
        ]);
    }

    /**
     * Scope to get payment related actions.
     */
    public function scopePaymentActions($query)
    {
        return $query->whereIn('action', [
            self::ACTION_PAYMENT_PROCESSED,
            self::ACTION_PAYMENT_FAILED,
        ]);
    }

    /**
     * Scope to get shopping related actions.
     */
    public function scopeShoppingActions($query)
    {
        return $query->whereIn('action', [
            self::ACTION_PRODUCT_VIEWED,
            self::ACTION_CART_UPDATED,
        ]);
    }

    /**
     * Scope to get profile related actions.
     */
    public function scopeProfileActions($query)
    {
        return $query->whereIn('action', [
            self::ACTION_PROFILE_UPDATED,
            self::ACTION_PASSWORD_CHANGED,
        ]);
    }

    /**
     * Scope to get activities from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope to get recent activities (within last 24 hours).
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDay());
    }

    /**
     * Scope to get activities for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get activities for a specific model.
     */
    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Get formatted action description.
     */
    public function getActionDescriptionAttribute(): string
    {
        return match($this->action) {
            self::ACTION_LOGIN => 'User logged in',
            self::ACTION_LOGOUT => 'User logged out',
            self::ACTION_REGISTER => 'User registered',
            self::ACTION_ORDER_CREATED => 'Order created',
            self::ACTION_ORDER_UPDATED => 'Order updated',
            self::ACTION_ORDER_CANCELLED => 'Order cancelled',
            self::ACTION_PAYMENT_PROCESSED => 'Payment processed',
            self::ACTION_PAYMENT_FAILED => 'Payment failed',
            self::ACTION_PRODUCT_VIEWED => 'Product viewed',
            self::ACTION_CART_UPDATED => 'Cart updated',
            self::ACTION_PROFILE_UPDATED => 'Profile updated',
            self::ACTION_PASSWORD_CHANGED => 'Password changed',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get action icon class.
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            self::ACTION_LOGIN => 'fas fa-sign-in-alt text-green-500',
            self::ACTION_LOGOUT => 'fas fa-sign-out-alt text-gray-500',
            self::ACTION_REGISTER => 'fas fa-user-plus text-blue-500',
            self::ACTION_ORDER_CREATED => 'fas fa-shopping-cart text-green-500',
            self::ACTION_ORDER_UPDATED => 'fas fa-edit text-blue-500',
            self::ACTION_ORDER_CANCELLED => 'fas fa-times text-red-500',
            self::ACTION_PAYMENT_PROCESSED => 'fas fa-credit-card text-green-500',
            self::ACTION_PAYMENT_FAILED => 'fas fa-exclamation-triangle text-red-500',
            self::ACTION_PRODUCT_VIEWED => 'fas fa-eye text-gray-500',
            self::ACTION_CART_UPDATED => 'fas fa-shopping-basket text-blue-500',
            self::ACTION_PROFILE_UPDATED => 'fas fa-user-edit text-blue-500',
            self::ACTION_PASSWORD_CHANGED => 'fas fa-key text-orange-500',
            default => 'fas fa-info-circle text-gray-500',
        };
    }

    /**
     * Get formatted time since action.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Log a new activity.
     */
    public static function logActivity(
        string $action,
        ?int $userId = null,
        ?string $modelType = null,
        ?int $modelId = null,
        array $metadata = []
    ): self {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get all available action types.
     */
    public static function getActionTypes(): array
    {
        return [
            self::ACTION_LOGIN,
            self::ACTION_LOGOUT,
            self::ACTION_REGISTER,
            self::ACTION_ORDER_CREATED,
            self::ACTION_ORDER_UPDATED,
            self::ACTION_ORDER_CANCELLED,
            self::ACTION_PAYMENT_PROCESSED,
            self::ACTION_PAYMENT_FAILED,
            self::ACTION_PRODUCT_VIEWED,
            self::ACTION_CART_UPDATED,
            self::ACTION_PROFILE_UPDATED,
            self::ACTION_PASSWORD_CHANGED,
        ];
    }
}
