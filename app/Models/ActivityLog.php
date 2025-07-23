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
        'description',
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
    
    /**
     * Get the icon SVG for the activity
     */
    public function getIconSvg(): string
    {
        $icons = [
            'login' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>',
            'logout' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>',
            'registration' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>',
            'order_created' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>',
            'order_updated' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>',
            'order_cancelled' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'payment_initiated' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>',
            'payment_completed' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'payment_failed' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'product_viewed' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>',
            'cart_updated' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4m8-6V9a3 3 0 00-6 0v2m6 0a2 2 0 012 2v1H7v-1a2 2 0 012-2"></path>',
            'wishlist_updated' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>',
            'profile_updated' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>',
            'password_changed' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>',
            'email_verified' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>',
            'system_error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>',
            'admin_action' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>'
        ];
        
        return $icons[$this->action] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>';
    }
    
    /**
     * Get the color classes for the activity
     */
    public function getColorClasses(): string
    {
        $colors = [
            'login' => 'bg-green-100 text-green-600',
            'logout' => 'bg-gray-100 text-gray-600',
            'registration' => 'bg-blue-100 text-blue-600',
            'order_created' => 'bg-indigo-100 text-indigo-600',
            'order_updated' => 'bg-yellow-100 text-yellow-600',
            'order_cancelled' => 'bg-red-100 text-red-600',
            'payment_initiated' => 'bg-purple-100 text-purple-600',
            'payment_completed' => 'bg-green-100 text-green-600',
            'payment_failed' => 'bg-red-100 text-red-600',
            'product_viewed' => 'bg-blue-100 text-blue-600',
            'cart_updated' => 'bg-orange-100 text-orange-600',
            'wishlist_updated' => 'bg-pink-100 text-pink-600',
            'profile_updated' => 'bg-indigo-100 text-indigo-600',
            'password_changed' => 'bg-yellow-100 text-yellow-600',
            'email_verified' => 'bg-green-100 text-green-600',
            'system_error' => 'bg-red-100 text-red-600',
            'admin_action' => 'bg-purple-100 text-purple-600'
        ];
        
        return $colors[$this->action] ?? 'bg-gray-100 text-gray-600';
    }
}
