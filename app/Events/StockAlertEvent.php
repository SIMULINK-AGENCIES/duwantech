<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockAlertEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $alertType;
    public $currentStock;
    public $threshold;

    const ALERT_LOW_STOCK = 'low_stock';
    const ALERT_OUT_OF_STOCK = 'out_of_stock';
    const ALERT_RESTOCK = 'restock';

    /**
     * Create a new event instance.
     */
    public function __construct(Product $product, string $alertType, int $currentStock, int $threshold = 0)
    {
        $this->product = $product;
        $this->alertType = $alertType;
        $this->currentStock = $currentStock;
        $this->threshold = $threshold;
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
            new PrivateChannel('admin-inventory'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'stock.alert';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
                'current_stock' => $this->currentStock,
                'threshold' => $this->threshold,
                'category' => $this->product->category?->name,
                'price' => $this->product->price,
                'currency' => $this->product->currency ?? 'USD',
            ],
            'alert' => [
                'type' => $this->alertType,
                'severity' => $this->getAlertSeverity(),
                'title' => $this->getAlertTitle(),
                'message' => $this->getAlertMessage(),
                'icon' => $this->getAlertIcon(),
                'color' => $this->getAlertColor(),
            ],
            'inventory_stats' => [
                'total_low_stock_products' => $this->getLowStockCount(),
                'total_out_of_stock_products' => $this->getOutOfStockCount(),
                'products_need_reorder' => $this->getReorderCount(),
            ],
            'notification' => [
                'title' => $this->getAlertTitle(),
                'message' => $this->getAlertMessage(),
                'type' => 'inventory',
                'priority' => $this->alertType === self::ALERT_OUT_OF_STOCK ? 'high' : 'medium',
                'action_url' => route('admin.products.show', $this->product->id),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get alert severity level.
     */
    private function getAlertSeverity(): string
    {
        return match($this->alertType) {
            self::ALERT_OUT_OF_STOCK => 'critical',
            self::ALERT_LOW_STOCK => 'warning',
            self::ALERT_RESTOCK => 'info',
            default => 'info',
        };
    }

    /**
     * Get alert title.
     */
    private function getAlertTitle(): string
    {
        return match($this->alertType) {
            self::ALERT_OUT_OF_STOCK => 'Product Out of Stock',
            self::ALERT_LOW_STOCK => 'Low Stock Alert',
            self::ALERT_RESTOCK => 'Product Restocked',
            default => 'Inventory Alert',
        };
    }

    /**
     * Get alert message.
     */
    private function getAlertMessage(): string
    {
        return match($this->alertType) {
            self::ALERT_OUT_OF_STOCK => "{$this->product->name} is completely out of stock",
            self::ALERT_LOW_STOCK => "{$this->product->name} has only {$this->currentStock} units left (threshold: {$this->threshold})",
            self::ALERT_RESTOCK => "{$this->product->name} has been restocked to {$this->currentStock} units",
            default => "Inventory alert for {$this->product->name}",
        };
    }

    /**
     * Get alert icon.
     */
    private function getAlertIcon(): string
    {
        return match($this->alertType) {
            self::ALERT_OUT_OF_STOCK => 'fas fa-exclamation-triangle',
            self::ALERT_LOW_STOCK => 'fas fa-exclamation-circle',
            self::ALERT_RESTOCK => 'fas fa-check-circle',
            default => 'fas fa-boxes',
        };
    }

    /**
     * Get alert color.
     */
    private function getAlertColor(): string
    {
        return match($this->alertType) {
            self::ALERT_OUT_OF_STOCK => 'text-red-500',
            self::ALERT_LOW_STOCK => 'text-orange-500',
            self::ALERT_RESTOCK => 'text-green-500',
            default => 'text-blue-500',
        };
    }

    /**
     * Get low stock products count.
     */
    private function getLowStockCount(): int
    {
        return Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 10) // Default low stock threshold
            ->count();
    }

    /**
     * Get out of stock products count.
     */
    private function getOutOfStockCount(): int
    {
        return Product::where('stock_quantity', '<=', 0)->count();
    }

    /**
     * Get products that need reordering.
     */
    private function getReorderCount(): int
    {
        return Product::where('stock_quantity', '<=', 5) // Default reorder threshold
            ->count();
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return true;
    }
}
