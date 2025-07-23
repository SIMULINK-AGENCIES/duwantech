<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Events\NewNotificationEvent;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a new admin notification
     */
    public function createNotification(
        string $title,
        string $message,
        string $type = 'info',
        string $priority = 'medium',
        ?int $userId = null,
        array $data = []
    ): AdminNotification {
        try {
            $notification = AdminNotification::create([
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'priority' => $priority,
                'user_id' => $userId,
                'data' => $data,
                'is_read' => false,
            ]);

            // Broadcast the notification
            broadcast(new NewNotificationEvent($notification))->toOthers();

            Log::info('Admin notification created', [
                'notification_id' => $notification->id,
                'type' => $type,
                'priority' => $priority,
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create admin notification', [
                'error' => $e->getMessage(),
                'title' => $title,
                'type' => $type,
            ]);

            throw $e;
        }
    }

    /**
     * Create order-related notification
     */
    public function createOrderNotification(string $orderId, string $status, array $details = []): AdminNotification
    {
        $titles = [
            'pending' => 'New Order Received',
            'processing' => 'Order Processing',
            'shipped' => 'Order Shipped',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
            'refunded' => 'Order Refunded',
        ];

        $messages = [
            'pending' => "New order #{$orderId} has been placed and requires attention.",
            'processing' => "Order #{$orderId} is now being processed.",
            'shipped' => "Order #{$orderId} has been shipped to the customer.",
            'delivered' => "Order #{$orderId} has been successfully delivered.",
            'cancelled' => "Order #{$orderId} has been cancelled.",
            'refunded' => "Refund has been processed for order #{$orderId}.",
        ];

        $types = [
            'pending' => 'info',
            'processing' => 'info',
            'shipped' => 'success',
            'delivered' => 'success',
            'cancelled' => 'warning',
            'refunded' => 'warning',
        ];

        $priorities = [
            'pending' => 'high',
            'processing' => 'medium',
            'shipped' => 'medium',
            'delivered' => 'low',
            'cancelled' => 'medium',
            'refunded' => 'high',
        ];

        return $this->createNotification(
            $titles[$status] ?? 'Order Update',
            $messages[$status] ?? "Order #{$orderId} status has been updated.",
            $types[$status] ?? 'info',
            $priorities[$status] ?? 'medium',
            null,
            array_merge(['order_id' => $orderId, 'status' => $status], $details)
        );
    }

    /**
     * Create payment-related notification
     */
    public function createPaymentNotification(string $paymentId, string $status, float $amount, array $details = []): AdminNotification
    {
        $titles = [
            'pending' => 'Payment Pending',
            'completed' => 'Payment Received',
            'failed' => 'Payment Failed',
            'refunded' => 'Payment Refunded',
            'cancelled' => 'Payment Cancelled',
        ];

        $messages = [
            'pending' => "Payment #{$paymentId} for KSH " . number_format($amount, 2) . " is pending confirmation.",
            'completed' => "Payment #{$paymentId} for KSH " . number_format($amount, 2) . " has been successfully received.",
            'failed' => "Payment #{$paymentId} for KSH " . number_format($amount, 2) . " has failed.",
            'refunded' => "Refund of KSH " . number_format($amount, 2) . " has been processed for payment #{$paymentId}.",
            'cancelled' => "Payment #{$paymentId} for KSH " . number_format($amount, 2) . " has been cancelled.",
        ];

        $types = [
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'error',
            'refunded' => 'info',
            'cancelled' => 'warning',
        ];

        return $this->createNotification(
            $titles[$status] ?? 'Payment Update',
            $messages[$status] ?? "Payment #{$paymentId} status has been updated.",
            $types[$status] ?? 'info',
            'high',
            null,
            array_merge(['payment_id' => $paymentId, 'status' => $status, 'amount' => $amount], $details)
        );
    }

    /**
     * Create stock alert notification
     */
    public function createStockAlert(string $productName, int $currentStock, int $threshold = 5): AdminNotification
    {
        $type = $currentStock === 0 ? 'error' : 'warning';
        $priority = $currentStock === 0 ? 'high' : 'medium';
        
        $title = $currentStock === 0 ? 'Product Out of Stock' : 'Low Stock Alert';
        $message = $currentStock === 0 
            ? "Product '{$productName}' is completely out of stock."
            : "Product '{$productName}' has only {$currentStock} items remaining in stock.";

        return $this->createNotification(
            $title,
            $message,
            $type,
            $priority,
            null,
            [
                'product_name' => $productName,
                'current_stock' => $currentStock,
                'threshold' => $threshold,
                'alert_type' => 'stock',
            ]
        );
    }

    /**
     * Create system alert notification
     */
    public function createSystemAlert(string $title, string $message, string $level = 'info', array $details = []): AdminNotification
    {
        $types = [
            'debug' => 'info',
            'info' => 'info',
            'notice' => 'info',
            'warning' => 'warning',
            'error' => 'error',
            'critical' => 'error',
            'alert' => 'error',
            'emergency' => 'error',
        ];

        $priorities = [
            'debug' => 'low',
            'info' => 'low',
            'notice' => 'medium',
            'warning' => 'medium',
            'error' => 'high',
            'critical' => 'high',
            'alert' => 'high',
            'emergency' => 'high',
        ];

        return $this->createNotification(
            $title,
            $message,
            $types[$level] ?? 'info',
            $priorities[$level] ?? 'medium',
            null,
            array_merge(['system_level' => $level, 'alert_type' => 'system'], $details)
        );
    }

    /**
     * Create user activity notification
     */
    public function createUserActivityAlert(string $activity, int $userId, array $details = []): AdminNotification
    {
        $activities = [
            'login_failed' => [
                'title' => 'Failed Login Attempt',
                'message' => "Multiple failed login attempts detected for user ID {$userId}.",
                'type' => 'warning',
                'priority' => 'medium',
            ],
            'suspicious_activity' => [
                'title' => 'Suspicious User Activity',
                'message' => "Suspicious activity detected for user ID {$userId}.",
                'type' => 'warning',
                'priority' => 'high',
            ],
            'account_locked' => [
                'title' => 'Account Locked',
                'message' => "User account {$userId} has been automatically locked due to security concerns.",
                'type' => 'error',
                'priority' => 'high',
            ],
        ];

        $config = $activities[$activity] ?? [
            'title' => 'User Activity Alert',
            'message' => "Activity '{$activity}' detected for user ID {$userId}.",
            'type' => 'info',
            'priority' => 'medium',
        ];

        return $this->createNotification(
            $config['title'],
            $config['message'],
            $config['type'],
            $config['priority'],
            $userId,
            array_merge(['activity' => $activity, 'alert_type' => 'user_activity'], $details)
        );
    }

    /**
     * Clean up old read notifications
     */
    public function cleanupOldNotifications(int $daysOld = 30): int
    {
        $deleted = AdminNotification::where('is_read', true)
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();

        Log::info("Cleaned up {$deleted} old notifications");

        return $deleted;
    }

    /**
     * Get notification statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => AdminNotification::count(),
            'unread' => AdminNotification::where('is_read', false)->count(),
            'by_type' => AdminNotification::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_priority' => AdminNotification::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
            'recent' => AdminNotification::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }
}
