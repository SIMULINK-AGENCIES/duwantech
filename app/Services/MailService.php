<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdateMail;
use App\Mail\PaymentConfirmationMail;
use App\Mail\PaymentFailedMail;
use App\Mail\StockAlertMail;
use App\Mail\WelcomeUserMail;
use App\Mail\AdminNotificationMail;
use App\Mail\SystemAlertMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Send order confirmation email.
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        try {
            Mail::send(new OrderConfirmationMail($order));
            
            Log::info('Order confirmation email sent', [
                'order_id' => $order->id,
                'customer_email' => $order->user->email,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send order status update email.
     */
    public function sendOrderStatusUpdate(Order $order, string $oldStatus, string $newStatus): bool
    {
        try {
            Mail::send(new OrderStatusUpdateMail($order, $oldStatus, $newStatus));
            
            Log::info('Order status update email sent', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order status update email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send payment confirmation email.
     */
    public function sendPaymentConfirmation($payment, Order $order): bool
    {
        try {
            Mail::send(new PaymentConfirmationMail($payment, $order));
            
            Log::info('Payment confirmation email sent', [
                'order_id' => $order->id,
                'payment_id' => is_object($payment) ? $payment->id : null,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send payment failed email.
     */
    public function sendPaymentFailed($payment, Order $order): bool
    {
        try {
            Mail::send(new PaymentFailedMail($payment, $order));
            
            Log::info('Payment failed email sent', [
                'order_id' => $order->id,
                'payment_id' => is_object($payment) ? $payment->id : null,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send stock alert email.
     */
    public function sendStockAlert($product, int $threshold, ?User $recipient = null): bool
    {
        try {
            Mail::send(new StockAlertMail($product, $recipient, $threshold));
            
            Log::info('Stock alert email sent', [
                'product_id' => is_object($product) ? $product->id : null,
                'product_name' => is_object($product) ? $product->name : $product,
                'threshold' => $threshold,
                'recipient' => $recipient?->email ?? 'admin',
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send stock alert email', [
                'product' => is_object($product) ? $product->id : $product,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send welcome email to new user.
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Mail::send(new WelcomeUserMail($user));
            
            Log::info('Welcome email sent', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send admin notification email.
     */
    public function sendAdminNotification($adminNotification, ?User $recipient = null): bool
    {
        try {
            Mail::send(new AdminNotificationMail($adminNotification, $recipient));
            
            Log::info('Admin notification email sent', [
                'notification_id' => is_object($adminNotification) ? $adminNotification->id : null,
                'recipient' => $recipient?->email ?? 'admin',
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification email', [
                'notification' => is_object($adminNotification) ? $adminNotification->id : $adminNotification,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send system alert email.
     */
    public function sendSystemAlert(
        string $alertType,
        string $alertTitle,
        string $alertMessage,
        array $alertData = [],
        string $severity = 'medium',
        ?User $recipient = null
    ): bool {
        try {
            Mail::send(new SystemAlertMail(
                $alertType,
                $alertTitle,
                $alertMessage,
                $alertData,
                $severity,
                $recipient
            ));
            
            Log::info('System alert email sent', [
                'alert_type' => $alertType,
                'severity' => $severity,
                'recipient' => $recipient?->email ?? 'admin',
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send system alert email', [
                'alert_type' => $alertType,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get email queue status.
     */
    public function getQueueStatus(): array
    {
        try {
            // This would typically integrate with your queue monitoring system
            return [
                'emails_queue' => [
                    'pending' => 0,
                    'processing' => 0,
                    'failed' => 0,
                ],
                'critical_alerts_queue' => [
                    'pending' => 0,
                    'processing' => 0,
                    'failed' => 0,
                ],
                'high_priority_queue' => [
                    'pending' => 0,
                    'processing' => 0,
                    'failed' => 0,
                ],
                'low_priority_queue' => [
                    'pending' => 0,
                    'processing' => 0,
                    'failed' => 0,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get queue status', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Test email configuration.
     */
    public function testEmailConfiguration(): array
    {
        $results = [];
        
        try {
            // Test SMTP connection
            $transport = Mail::getSwiftMailer()->getTransport();
            $transport->start();
            
            $results['smtp_connection'] = true;
            $results['smtp_message'] = 'SMTP connection successful';
        } catch (\Exception $e) {
            $results['smtp_connection'] = false;
            $results['smtp_message'] = 'SMTP connection failed: ' . $e->getMessage();
        }
        
        try {
            // Test queue connection
            $queueConnection = config('queue.default');
            $results['queue_connection'] = true;
            $results['queue_message'] = "Queue connection ({$queueConnection}) is working";
        } catch (\Exception $e) {
            $results['queue_connection'] = false;
            $results['queue_message'] = 'Queue connection failed: ' . $e->getMessage();
        }
        
        // Test email templates
        $templates = [
            'orders.confirmation',
            'orders.status-update',
            'payments.confirmation',
            'payments.failed',
            'system.stock-alert',
            'users.welcome',
            'system.admin-notification',
            'system.alert',
        ];
        
        $results['templates'] = [];
        foreach ($templates as $template) {
            try {
                view("emails.{$template}", $this->getTestData($template));
                $results['templates'][$template] = true;
            } catch (\Exception $e) {
                $results['templates'][$template] = false;
                $results['template_errors'][$template] = $e->getMessage();
            }
        }
        
        return $results;
    }

    /**
     * Get test data for email templates.
     */
    protected function getTestData(string $template): array
    {
        $baseData = [
            'storeName' => config('app.name'),
            'storeUrl' => config('app.url'),
            'supportEmail' => config('mail.from.address'),
            'supportPhone' => '+254 700 000 000',
            'supportUrl' => '#',
        ];
        
        return match($template) {
            'orders.confirmation' => array_merge($baseData, [
                'customerName' => 'John Doe',
                'orderNumber' => 'ORD-2024-001',
                'orderDate' => now(),
                'paymentMethod' => 'M-Pesa',
                'orderAmount' => 2500.00,
                'productName' => 'Test Product',
                'productImage' => null,
                'quantity' => 1,
                'unitPrice' => 2500.00,
                'estimatedDelivery' => now()->addDays(3),
                'deliveryAddress' => 'Test Address, Nairobi',
                'trackingUrl' => '#',
            ]),
            
            'orders.status-update' => array_merge($baseData, [
                'customerName' => 'John Doe',
                'orderNumber' => 'ORD-2024-001',
                'oldStatus' => 'processing',
                'newStatus' => 'shipped',
                'statusTitle' => 'Order Shipped',
                'statusMessage' => 'Your order has been shipped',
                'statusColor' => '#06b6d4',
                'productName' => 'Test Product',
                'productImage' => null,
                'orderAmount' => 2500.00,
                'orderDate' => now(),
                'trackingUrl' => '#',
                'timeline' => [],
                'nextSteps' => ['Track your package'],
            ]),
            
            'payments.confirmation' => array_merge($baseData, [
                'customerName' => 'John Doe',
                'orderNumber' => 'ORD-2024-001',
                'transactionId' => 'TXN-123456',
                'paymentMethod' => 'M-Pesa',
                'amount' => 2500.00,
                'paymentDate' => now(),
                'productName' => 'Test Product',
                'productImage' => null,
                'trackingUrl' => '#',
            ]),
            
            'users.welcome' => array_merge($baseData, [
                'userName' => 'John Doe',
                'profileUrl' => '#',
                'shopUrl' => '#',
                'popularCategories' => [
                    ['name' => 'Electronics', 'icon' => '📱', 'url' => '#', 'count' => 100],
                ],
            ]),
            
            default => $baseData,
        };
    }
}
