<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $product;
    public $alertType;
    public $currentStock;
    public $threshold;

    const ALERT_LOW_STOCK = 'low_stock';
    const ALERT_OUT_OF_STOCK = 'out_of_stock';
    const ALERT_CRITICAL_STOCK = 'critical_stock';

    /**
     * Create a new message instance.
     */
    public function __construct(Product $product, string $alertType, int $currentStock, int $threshold = 0)
    {
        $this->product = $product;
        $this->alertType = $alertType;
        $this->currentStock = $currentStock;
        $this->threshold = $threshold;
        
        // High priority for stock alerts
        $this->onQueue('emails');
        
        // Immediate sending for critical alerts
        if ($alertType === self::ALERT_OUT_OF_STOCK) {
            $this->delay(now());
        } else {
            $this->delay(now()->addMinutes(1));
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $adminEmail = config('app.admin_email', config('mail.from.address'));
        $urgency = $this->alertType === self::ALERT_OUT_OF_STOCK ? 'URGENT' : 'ALERT';
        
        return new Envelope(
            to: [$adminEmail],
            subject: "[{$urgency}] Stock Alert - {$this->product->name}",
            tags: ['stock-alert', $this->alertType],
            metadata: [
                'product_id' => $this->product->id,
                'alert_type' => $this->alertType,
                'current_stock' => $this->currentStock,
                'threshold' => $this->threshold,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.stock-alert',
            with: [
                'productName' => $this->product->name,
                'productSku' => $this->product->sku,
                'productImage' => $this->product->image,
                'currentStock' => $this->currentStock,
                'threshold' => $this->threshold,
                'alertType' => $this->alertType,
                'alertTitle' => $this->getAlertTitle(),
                'alertMessage' => $this->getAlertMessage(),
                'urgencyLevel' => $this->getUrgencyLevel(),
                'recommendedAction' => $this->getRecommendedAction(),
                'categoryName' => $this->product->category->name ?? 'Uncategorized',
                'productPrice' => $this->product->price,
                'lastRestocked' => $this->product->last_restocked_at,
                'supplierInfo' => $this->product->supplier_info ?? 'No supplier information',
                'productUrl' => route('admin.products.show', $this->product->id),
                'restockUrl' => route('admin.products.restock', $this->product->id),
                'storeName' => config('app.name'),
                'adminPanelUrl' => route('admin.dashboard'),
            ],
        );
    }

    /**
     * Get the alert title based on type.
     */
    protected function getAlertTitle(): string
    {
        return match($this->alertType) {
            self::ALERT_LOW_STOCK => 'Low Stock Warning',
            self::ALERT_OUT_OF_STOCK => 'OUT OF STOCK - Immediate Action Required',
            self::ALERT_CRITICAL_STOCK => 'Critical Stock Level',
            default => 'Stock Alert',
        };
    }

    /**
     * Get the alert message based on type.
     */
    protected function getAlertMessage(): string
    {
        return match($this->alertType) {
            self::ALERT_LOW_STOCK => "Stock is running low. Current level ({$this->currentStock}) is below the threshold ({$this->threshold}).",
            self::ALERT_OUT_OF_STOCK => "This product is completely out of stock and unavailable for purchase.",
            self::ALERT_CRITICAL_STOCK => "Stock has reached a critical level that requires immediate attention.",
            default => "Stock levels need attention.",
        };
    }

    /**
     * Get the urgency level.
     */
    protected function getUrgencyLevel(): string
    {
        return match($this->alertType) {
            self::ALERT_LOW_STOCK => 'Medium',
            self::ALERT_OUT_OF_STOCK => 'Critical',
            self::ALERT_CRITICAL_STOCK => 'High',
            default => 'Low',
        };
    }

    /**
     * Get recommended action.
     */
    protected function getRecommendedAction(): string
    {
        return match($this->alertType) {
            self::ALERT_LOW_STOCK => 'Consider restocking soon to avoid running out.',
            self::ALERT_OUT_OF_STOCK => 'Immediate restocking required. Product is currently unavailable to customers.',
            self::ALERT_CRITICAL_STOCK => 'Review stock levels and restock as needed.',
            default => 'Review and take appropriate action.',
        };
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
        // Check if stock alerts are enabled
        return config('app.stock_email_alerts', true);
    }
}
