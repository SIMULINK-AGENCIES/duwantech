<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Events\NewActivityEvent;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log a user activity
     */
    public static function log(string $action, string $description, ?int $userId = null, array $metadata = []): ?ActivityLog
    {
        try {
            $activity = ActivityLog::create([
                'user_id' => $userId ?? Auth::id(),
                'action' => $action,
                'description' => $description,
                'metadata' => array_merge($metadata, [
                    'timestamp' => now()->toISOString(),
                    'session_id' => session()->getId(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method()
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            // Broadcast the new activity to admin users
            broadcast(new NewActivityEvent($activity))->toOthers();
            
            return $activity;
            
        } catch (\Exception $e) {
            \Log::error('Failed to log activity: ' . $e->getMessage(), [
                'action' => $action,
                'description' => $description,
                'user_id' => $userId,
                'metadata' => $metadata,
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }
    
    /**
     * Log user login
     */
    public static function logLogin(?int $userId = null): ?ActivityLog
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        $description = $user ? "User {$user->name} logged in" : 'User logged in';
        
        return self::log('login', $description, $userId, [
            'priority' => 'medium',
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log user logout
     */
    public static function logLogout(?int $userId = null): ?ActivityLog
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        $description = $user ? "User {$user->name} logged out" : 'User logged out';
        
        return self::log('logout', $description, $userId, [
            'priority' => 'low',
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log user registration
     */
    public static function logRegistration(int $userId): ?ActivityLog
    {
        $user = \App\Models\User::find($userId);
        $description = $user ? "New user {$user->name} registered" : 'New user registered';
        
        return self::log('registration', $description, $userId, [
            'priority' => 'medium',
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log order creation
     */
    public static function logOrderCreated(int $orderId, ?int $userId = null): ?ActivityLog
    {
        $order = \App\Models\Order::find($orderId);
        $user = $userId ? \App\Models\User::find($userId) : ($order?->user ?? Auth::user());
        
        $description = $user && $order ? 
            "User {$user->name} created order #{$order->order_number}" : 
            'New order created';
        
        return self::log('order_created', $description, $userId ?? $order?->user_id, [
            'priority' => 'high',
            'order_id' => $orderId,
            'order_number' => $order?->order_number,
            'amount' => $order?->amount,
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log order update
     */
    public static function logOrderUpdated(int $orderId, string $status, ?int $userId = null): ?ActivityLog
    {
        $order = \App\Models\Order::find($orderId);
        $user = $userId ? \App\Models\User::find($userId) : ($order?->user ?? Auth::user());
        
        $description = $order ? 
            "Order #{$order->order_number} status changed to {$status}" : 
            "Order status changed to {$status}";
        
        return self::log('order_updated', $description, $userId ?? $order?->user_id, [
            'priority' => 'medium',
            'order_id' => $orderId,
            'order_number' => $order?->order_number,
            'new_status' => $status,
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log order cancellation
     */
    public static function logOrderCancelled(int $orderId, ?int $userId = null): ?ActivityLog
    {
        $order = \App\Models\Order::find($orderId);
        $user = $userId ? \App\Models\User::find($userId) : ($order?->user ?? Auth::user());
        
        $description = $order ? 
            "Order #{$order->order_number} was cancelled" : 
            'Order was cancelled';
        
        return self::log('order_cancelled', $description, $userId ?? $order?->user_id, [
            'priority' => 'high',
            'order_id' => $orderId,
            'order_number' => $order?->order_number,
            'amount' => $order?->amount,
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log payment completion
     */
    public static function logPaymentCompleted(int $paymentId, ?int $userId = null): ?ActivityLog
    {
        $payment = \App\Models\Payment::find($paymentId);
        $user = $userId ? \App\Models\User::find($userId) : ($payment?->order?->user ?? Auth::user());
        
        $description = $payment && $user ? 
            "Payment of KES {$payment->amount} completed by {$user->name}" : 
            'Payment completed';
        
        return self::log('payment_completed', $description, $userId ?? $payment?->order?->user_id, [
            'priority' => 'high',
            'payment_id' => $paymentId,
            'amount' => $payment?->amount,
            'method' => $payment?->method,
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log payment failure
     */
    public static function logPaymentFailed(int $paymentId, string $reason, ?int $userId = null): ?ActivityLog
    {
        $payment = \App\Models\Payment::find($paymentId);
        $user = $userId ? \App\Models\User::find($userId) : ($payment?->order?->user ?? Auth::user());
        
        $description = $payment && $user ? 
            "Payment of KES {$payment->amount} failed for {$user->name}: {$reason}" : 
            "Payment failed: {$reason}";
        
        return self::log('payment_failed', $description, $userId ?? $payment?->order?->user_id, [
            'priority' => 'high',
            'payment_id' => $paymentId,
            'amount' => $payment?->amount,
            'method' => $payment?->method,
            'failure_reason' => $reason,
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log product view
     */
    public static function logProductViewed(int $productId, ?int $userId = null): ?ActivityLog
    {
        $product = \App\Models\Product::find($productId);
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        
        $description = $product ? 
            ($user ? "User {$user->name} viewed product '{$product->name}'" : "Guest viewed product '{$product->name}'") : 
            'Product viewed';
        
        return self::log('product_viewed', $description, $userId, [
            'priority' => 'low',
            'product_id' => $productId,
            'product_name' => $product?->name,
            'product_price' => $product?->price,
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log profile update
     */
    public static function logProfileUpdated(?int $userId = null): ?ActivityLog
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        $description = $user ? "User {$user->name} updated their profile" : 'Profile updated';
        
        return self::log('profile_updated', $description, $userId, [
            'priority' => 'low',
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log password change
     */
    public static function logPasswordChanged(?int $userId = null): ?ActivityLog
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        $description = $user ? "User {$user->name} changed their password" : 'Password changed';
        
        return self::log('password_changed', $description, $userId, [
            'priority' => 'medium',
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log email verification
     */
    public static function logEmailVerified(?int $userId = null): ?ActivityLog
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();
        $description = $user ? "User {$user->name} verified their email" : 'Email verified';
        
        return self::log('email_verified', $description, $userId, [
            'priority' => 'medium',
            'user_email' => $user?->email
        ]);
    }
    
    /**
     * Log system error
     */
    public static function logSystemError(string $error, array $context = []): ?ActivityLog
    {
        return self::log('system_error', "System error: {$error}", null, [
            'priority' => 'high',
            'error_message' => $error,
            'context' => $context
        ]);
    }
    
    /**
     * Log admin action
     */
    public static function logAdminAction(string $action, string $description, ?int $adminId = null): ?ActivityLog
    {
        $admin = $adminId ? \App\Models\User::find($adminId) : Auth::user();
        $fullDescription = $admin ? "Admin {$admin->name}: {$description}" : "Admin: {$description}";
        
        return self::log('admin_action', $fullDescription, $adminId, [
            'priority' => 'medium',
            'admin_action' => $action,
            'admin_email' => $admin?->email
        ]);
    }
}
