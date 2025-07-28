<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->customer = $order->user;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        
        // Set queue connection
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(30)); // Small delay for status updates
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusTitle = $this->getStatusTitle($this->newStatus);
        
        return new Envelope(
            to: [$this->customer->email],
            subject: "Order Update: {$statusTitle} - {$this->order->order_number}",
            tags: ['order-status', $this->newStatus],
            metadata: [
                'order_id' => $this->order->id,
                'customer_id' => $this->customer->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status-update',
            with: [
                'customerName' => $this->customer->name,
                'orderNumber' => $this->order->order_number,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'statusTitle' => $this->getStatusTitle($this->newStatus),
                'statusMessage' => $this->getStatusMessage($this->newStatus),
                'statusColor' => $this->getStatusColor($this->newStatus),
                'productName' => $this->order->product->name,
                'productImage' => $this->order->product->image,
                'orderAmount' => $this->order->amount,
                'orderDate' => $this->order->created_at,
                'estimatedDelivery' => $this->order->estimated_delivery,
                'trackingNumber' => $this->order->tracking_number,
                'deliveryAddress' => $this->order->delivery_address,
                'nextSteps' => $this->getNextSteps($this->newStatus),
                'trackingUrl' => route('orders.track', $this->order->order_number),
                'supportEmail' => config('mail.from.address'),
                'supportPhone' => config('app.support_phone', '+254 700 000 000'),
                'storeName' => config('app.name'),
                'storeUrl' => config('app.url'),
                'timeline' => $this->getOrderTimeline(),
            ],
        );
    }

    /**
     * Get human-readable status title.
     */
    protected function getStatusTitle(string $status): string
    {
        return match($status) {
            'pending' => 'Order Received',
            'confirmed' => 'Order Confirmed',
            'processing' => 'Order Processing',
            'shipped' => 'Order Shipped',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
            'refunded' => 'Order Refunded',
            'payment_failed' => 'Payment Failed',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Get status message for customer.
     */
    protected function getStatusMessage(string $status): string
    {
        return match($status) {
            'pending' => 'We have received your order and are processing your payment.',
            'confirmed' => 'Your payment has been confirmed and your order is being prepared.',
            'processing' => 'Your order is being prepared and will be shipped soon.',
            'shipped' => 'Your order has been shipped and is on its way to you.',
            'out_for_delivery' => 'Your order is out for delivery and will arrive soon.',
            'delivered' => 'Your order has been delivered successfully. Thank you for your purchase!',
            'cancelled' => 'Your order has been cancelled. If you have any questions, please contact us.',
            'refunded' => 'Your order has been refunded. The refund will be processed within 3-5 business days.',
            'payment_failed' => 'We were unable to process your payment. Please try again or contact support.',
            default => 'Your order status has been updated.',
        };
    }

    /**
     * Get status color for styling.
     */
    protected function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => '#f59e0b',        // yellow-500
            'confirmed' => '#3b82f6',      // blue-500
            'processing' => '#8b5cf6',     // violet-500
            'shipped' => '#06b6d4',        // cyan-500
            'out_for_delivery' => '#f97316', // orange-500
            'delivered' => '#10b981',      // emerald-500
            'cancelled' => '#ef4444',      // red-500
            'refunded' => '#6b7280',       // gray-500
            'payment_failed' => '#dc2626', // red-600
            default => '#6b7280',          // gray-500
        };
    }

    /**
     * Get next steps for customer based on status.
     */
    protected function getNextSteps(string $status): array
    {
        return match($status) {
            'pending' => [
                'We will confirm your payment shortly',
                'You will receive a confirmation email once processed',
                'Contact us if you have any questions',
            ],
            'confirmed' => [
                'Your order is being prepared for shipment',
                'You will receive tracking information once shipped',
                'Estimated delivery: ' . $this->order->estimated_delivery?->format('M d, Y'),
            ],
            'processing' => [
                'Your order is being carefully prepared',
                'We will notify you once it has been shipped',
                'Track your order status anytime',
            ],
            'shipped' => [
                'Track your package using the tracking number provided',
                'Prepare to receive your delivery',
                'Contact us if you have delivery concerns',
            ],
            'out_for_delivery' => [
                'Someone should be available to receive the package',
                'Delivery typically occurs during business hours',
                'Contact the delivery partner if needed',
            ],
            'delivered' => [
                'We hope you enjoy your purchase!',
                'Rate and review your experience',
                'Contact us if you have any issues',
            ],
            'cancelled' => [
                'Any payment will be refunded within 3-5 business days',
                'You can place a new order anytime',
                'Contact support if you need assistance',
            ],
            'payment_failed' => [
                'Try using a different payment method',
                'Contact your bank if the issue persists',
                'Reach out to our support team for help',
            ],
            default => [
                'Track your order for the latest updates',
                'Contact us if you have any questions',
            ],
        };
    }

    /**
     * Get order timeline for display.
     */
    protected function getOrderTimeline(): array
    {
        $timeline = [
            [
                'status' => 'pending',
                'title' => 'Order Placed',
                'completed' => true,
                'date' => $this->order->created_at,
            ],
        ];

        $statusOrder = ['confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered'];
        $currentIndex = array_search($this->newStatus, $statusOrder);

        foreach ($statusOrder as $index => $status) {
            $timeline[] = [
                'status' => $status,
                'title' => $this->getStatusTitle($status),
                'completed' => $index <= $currentIndex,
                'current' => $status === $this->newStatus,
                'date' => $status === $this->newStatus ? now() : null,
            ];
        }

        return $timeline;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Determine if the message should be sent.
     */
    public function shouldSend(): bool
    {
        // Don't send emails for certain internal status changes
        $skipStatuses = ['payment_pending', 'processing_payment'];
        
        return !in_array($this->newStatus, $skipStatuses) && 
               ($this->customer->email_notifications ?? true);
    }
}
