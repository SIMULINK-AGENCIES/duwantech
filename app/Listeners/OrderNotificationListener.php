<?php

namespace App\Listeners;

use App\Events\NewOrderEvent;
use App\Models\AdminNotification;
use App\Services\ActivityLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class OrderNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(NewOrderEvent $event): void
    {
        try {
            $order = $event->order;
            
            // Create admin notification for new order
            AdminNotification::create([
                'title' => 'New Order Received',
                'message' => "New order #{$order->order_number} from {$order->user->name} for {$order->product->name}",
                'type' => 'order',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->user->name,
                    'product_name' => $order->product->name,
                    'amount' => $order->amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toISOString(),
                ],
                'read_at' => null,
                'action_url' => route('admin.orders.show', $order->id),
            ]);

            // Log activity for order notification
            ActivityLogger::log(
                'order_notification_sent',
                'Order notification created',
                null,
                [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $order->user_id,
                    'customer_name' => $order->user->name,
                    'product_id' => $order->product_id,
                    'product_name' => $order->product->name,
                    'amount' => $order->amount,
                    'notification_type' => 'new_order',
                ]
            );

            // Send email notification to admin (if configured)
            $this->sendEmailNotification($order);

            Log::info('Order notification sent successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process order notification', [
                'order_id' => $event->order->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger queue retry
            throw $e;
        }
    }

    /**
     * Send email notification to admin
     */
    protected function sendEmailNotification($order): void
    {
        try {
            // Check if email notifications are enabled
            $emailEnabled = config('app.admin_email_notifications', true);
            $adminEmail = config('app.admin_email');

            if ($emailEnabled && $adminEmail) {
                // You can implement email sending here
                // Mail::to($adminEmail)->send(new NewOrderNotification($order));
                
                Log::info('Admin email notification queued', [
                    'order_id' => $order->id,
                    'admin_email' => $adminEmail,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send admin email notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(NewOrderEvent $event, \Throwable $exception): void
    {
        Log::error('OrderNotificationListener job failed', [
            'order_id' => $event->order->id ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Create a failure notification
        try {
            AdminNotification::create([
                'title' => 'Order Notification Failed',
                'message' => "Failed to process notification for order #{$event->order->order_number}",
                'type' => 'system_error',
                'data' => [
                    'order_id' => $event->order->id,
                    'error' => $exception->getMessage(),
                    'failed_at' => now()->toISOString(),
                ],
                'read_at' => null,
            ]);
        } catch (\Exception $e) {
            Log::critical('Failed to create failure notification', [
                'original_error' => $exception->getMessage(),
                'notification_error' => $e->getMessage(),
            ]);
        }
    }
}
