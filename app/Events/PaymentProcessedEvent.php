<?php

namespace App\Events;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentProcessedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;
    public $order;
    public $isSuccessful;

    /**
     * Create a new event instance.
     */
    public function __construct(Payment $payment, Order $order = null, bool $isSuccessful = true)
    {
        $this->payment = $payment;
        $this->order = $order ?? $payment->order;
        $this->isSuccessful = $isSuccessful;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-monitoring'),
            new PrivateChannel('admin-payments'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return $this->isSuccessful ? 'payment.success' : 'payment.failed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'payment' => [
                'id' => $this->payment->id,
                'transaction_id' => $this->payment->transaction_id,
                'amount' => $this->payment->amount,
                'currency' => $this->payment->currency ?? 'USD',
                'status' => $this->payment->status,
                'payment_method' => $this->payment->payment_method,
                'processed_at' => $this->payment->processed_at?->toISOString(),
                'created_at' => $this->payment->created_at->toISOString(),
            ],
            'order' => $this->order ? [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number ?? 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
                'status' => $this->order->status,
                'total_amount' => $this->order->total_amount,
            ] : null,
            'customer' => $this->order && $this->order->user ? [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
                'email' => $this->order->user->email,
            ] : null,
            'statistics' => [
                'successful_payments_today' => $this->getSuccessfulPaymentsToday(),
                'failed_payments_today' => $this->getFailedPaymentsToday(),
                'revenue_today' => $this->getTodayRevenue(),
            ],
            'notification' => [
                'title' => $this->isSuccessful ? 'Payment Successful' : 'Payment Failed',
                'message' => $this->getNotificationMessage(),
                'type' => 'payment',
                'priority' => $this->isSuccessful ? 'medium' : 'high',
                'action_url' => $this->order ? route('admin.orders.show', $this->order->id) : null,
            ],
            'is_successful' => $this->isSuccessful,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get notification message based on payment status.
     */
    private function getNotificationMessage(): string
    {
        if ($this->isSuccessful) {
            return "Payment of " . number_format($this->payment->amount, 2) . " processed successfully";
        }

        return "Payment of " . number_format($this->payment->amount, 2) . " failed - " . ($this->payment->failure_reason ?? 'Unknown error');
    }

    /**
     * Get successful payments count for today.
     */
    private function getSuccessfulPaymentsToday(): int
    {
        return Payment::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get failed payments count for today.
     */
    private function getFailedPaymentsToday(): int
    {
        return Payment::whereDate('created_at', today())
            ->where('status', 'failed')
            ->count();
    }

    /**
     * Get today's revenue from successful payments.
     */
    private function getTodayRevenue(): float
    {
        return Payment::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return true;
    }
}
