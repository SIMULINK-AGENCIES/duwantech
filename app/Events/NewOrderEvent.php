<?php

namespace App\Events;

use App\Models\Order;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $orderCount;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, int $orderCount)
    {
        $this->order = $order;
        $this->orderCount = $orderCount;
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
            new PrivateChannel('admin-orders'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.new';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number ?? 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
                'status' => $this->order->status,
                'total_amount' => $this->order->total_amount,
                'currency' => $this->order->currency ?? 'USD',
                'item_count' => $this->order->items_count ?? 0,
                'created_at' => $this->order->created_at->toISOString(),
            ],
            'customer' => [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
                'email' => $this->order->user->email,
            ],
            'statistics' => [
                'total_orders_today' => $this->orderCount,
                'revenue_today' => $this->calculateTodayRevenue(),
            ],
            'notification' => [
                'title' => 'New Order Received',
                'message' => "Order #{$this->order->id} for " . number_format($this->order->total_amount, 2),
                'type' => 'order',
                'priority' => 'medium',
                'action_url' => route('admin.orders.show', $this->order->id),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Calculate today's revenue.
     */
    private function calculateTodayRevenue(): float
    {
        return Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return true;
    }
}
