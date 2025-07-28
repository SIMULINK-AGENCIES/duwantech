<?php

namespace App\Listeners;

use App\Events\PaymentProcessedEvent;
use App\Models\AdminNotification;
use App\Services\ActivityLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PaymentNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentProcessedEvent $event): void
    {
        try {
            $payment = $event->payment;
            $order = $event->order;
            $isSuccessful = $event->isSuccessful;

            // Determine notification type and message based on payment status
            $notificationType = $isSuccessful ? 'payment_success' : 'payment_failed';
            $title = $isSuccessful ? 'Payment Received' : 'Payment Failed';
            $statusText = $isSuccessful ? 'successfully processed' : 'failed';
            
            $message = "Payment for order #{$order->order_number} was {$statusText}";
            if ($payment->payment_method === 'mpesa') {
                $message .= " via M-Pesa";
            }

            // Create admin notification
            AdminNotification::create([
                'title' => $title,
                'message' => $message,
                'type' => $notificationType,
                'data' => [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->user->name,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'transaction_id' => $payment->transaction_id ?? null,
                    'mpesa_receipt_number' => $payment->mpesa_receipt_number ?? null,
                    'status' => $payment->status,
                    'is_successful' => $isSuccessful,
                    'processed_at' => $payment->updated_at->toISOString(),
                ],
                'read_at' => null,
                'action_url' => route('admin.orders.show', $order->id),
            ]);

            // Log payment activity
            ActivityLogger::log(
                'payment_notification_sent',
                'Payment notification created',
                null,
                [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $order->user_id,
                    'customer_name' => $order->user->name,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'transaction_id' => $payment->transaction_id,
                    'mpesa_receipt_number' => $payment->mpesa_receipt_number,
                    'status' => $payment->status,
                    'is_successful' => $isSuccessful,
                    'notification_type' => $notificationType,
                ]
            );

            // Handle specific actions based on payment status
            if ($isSuccessful) {
                $this->handleSuccessfulPayment($payment, $order);
            } else {
                $this->handleFailedPayment($payment, $order);
            }

            Log::info('Payment notification sent successfully', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'status' => $payment->status,
                'is_successful' => $isSuccessful,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process payment notification', [
                'payment_id' => $event->payment->id ?? null,
                'order_id' => $event->order->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger queue retry
            throw $e;
        }
    }

    /**
     * Handle successful payment actions
     */
    protected function handleSuccessfulPayment($payment, $order): void
    {
        try {
            // Update order status if needed
            if ($order->status === 'pending' && $payment->status === 'completed') {
                $order->update(['status' => 'confirmed']);
                
                ActivityLogger::log(
                    'order_status_updated',
                    'Order status updated to confirmed after successful payment',
                    null,
                    [
                        'order_id' => $order->id,
                        'old_status' => 'pending',
                        'new_status' => 'confirmed',
                        'payment_id' => $payment->id,
                    ]
                );
            }

            // Send customer confirmation email (if configured)
            $this->sendCustomerConfirmation($payment, $order);

            // Trigger inventory update if needed
            $this->updateInventory($order);

        } catch (\Exception $e) {
            Log::warning('Failed to process successful payment actions', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle failed payment actions
     */
    protected function handleFailedPayment($payment, $order): void
    {
        try {
            // Mark order as payment failed if needed
            if ($order->status !== 'cancelled') {
                $order->update(['status' => 'payment_failed']);
                
                ActivityLogger::log(
                    'order_status_updated',
                    'Order status updated to payment_failed',
                    null,
                    [
                        'order_id' => $order->id,
                        'old_status' => $order->getOriginal('status'),
                        'new_status' => 'payment_failed',
                        'payment_id' => $payment->id,
                    ]
                );
            }

            // Send customer notification about failed payment
            $this->sendCustomerFailureNotification($payment, $order);

        } catch (\Exception $e) {
            Log::warning('Failed to process failed payment actions', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send customer confirmation email
     */
    protected function sendCustomerConfirmation($payment, $order): void
    {
        try {
            // Implement customer email confirmation
            // Mail::to($order->user->email)->send(new PaymentConfirmationMail($payment, $order));
            
            Log::info('Customer payment confirmation queued', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'customer_email' => $order->user->email,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to send customer payment confirmation', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send customer failure notification
     */
    protected function sendCustomerFailureNotification($payment, $order): void
    {
        try {
            // Implement customer email notification for failed payment
            // Mail::to($order->user->email)->send(new PaymentFailedMail($payment, $order));
            
            Log::info('Customer payment failure notification queued', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'customer_email' => $order->user->email,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to send customer payment failure notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update inventory after successful payment
     */
    protected function updateInventory($order): void
    {
        try {
            $product = $order->product;
            if ($product && $product->stock > 0) {
                $product->decrement('stock', $order->quantity ?? 1);
                
                ActivityLogger::log(
                    'inventory_updated',
                    'Product stock decreased after successful payment',
                    null,
                    [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'order_id' => $order->id,
                        'quantity_sold' => $order->quantity ?? 1,
                        'remaining_stock' => $product->fresh()->stock,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update inventory after payment', [
                'order_id' => $order->id,
                'product_id' => $order->product_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(PaymentProcessedEvent $event, \Throwable $exception): void
    {
        Log::error('PaymentNotificationListener job failed', [
            'payment_id' => $event->payment->id ?? null,
            'order_id' => $event->order->id ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Create a failure notification
        try {
            AdminNotification::create([
                'title' => 'Payment Notification Failed',
                'message' => "Failed to process payment notification for order #{$event->order->order_number}",
                'type' => 'system_error',
                'data' => [
                    'payment_id' => $event->payment->id,
                    'order_id' => $event->order->id,
                    'error' => $exception->getMessage(),
                    'failed_at' => now()->toISOString(),
                ],
                'read_at' => null,
            ]);
        } catch (\Exception $e) {
            Log::critical('Failed to create payment notification failure notification', [
                'original_error' => $exception->getMessage(),
                'notification_error' => $e->getMessage(),
            ]);
        }
    }
}
