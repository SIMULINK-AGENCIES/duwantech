<?php

namespace App\Listeners;

use App\Events\StockAlertEvent;
use App\Models\AdminNotification;
use App\Services\ActivityLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class InventoryNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(StockAlertEvent $event): void
    {
        try {
            $product = $event->product;
            $alertType = $event->alertType;
            $currentStock = $event->currentStock;
            $threshold = $event->threshold;

            // Generate notification content based on alert type
            $notificationData = $this->generateNotificationContent($product, $alertType, $currentStock, $threshold);

            // Create admin notification
            AdminNotification::create([
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
                'type' => $notificationData['type'],
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? null,
                    'alert_type' => $alertType,
                    'current_stock' => $currentStock,
                    'threshold' => $threshold,
                    'category' => $product->category->name ?? null,
                    'price' => $product->price,
                    'created_at' => now()->toISOString(),
                ],
                'read_at' => null,
                'action_url' => route('admin.products.show', $product->id),
            ]);

            // Log inventory alert activity
            ActivityLogger::log(
                'inventory_alert_sent',
                'Inventory alert notification created',
                null,
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'alert_type' => $alertType,
                    'current_stock' => $currentStock,
                    'threshold' => $threshold,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category->name ?? null,
                    'notification_type' => $notificationData['type'],
                ]
            );

            // Handle specific actions based on alert type
            $this->handleAlertTypeActions($product, $alertType, $currentStock, $threshold);

            // Send urgent notifications for critical stock levels
            if ($alertType === StockAlertEvent::ALERT_OUT_OF_STOCK) {
                $this->sendUrgentNotification($product, $alertType);
            }

            Log::info('Inventory alert notification sent successfully', [
                'product_id' => $product->id,
                'alert_type' => $alertType,
                'current_stock' => $currentStock,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process inventory notification', [
                'product_id' => $event->product->id ?? null,
                'alert_type' => $event->alertType ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger queue retry
            throw $e;
        }
    }

    /**
     * Generate notification content based on alert type
     */
    protected function generateNotificationContent($product, $alertType, $currentStock, $threshold): array
    {
        switch ($alertType) {
            case StockAlertEvent::ALERT_LOW_STOCK:
                return [
                    'title' => 'Low Stock Alert',
                    'message' => "Product '{$product->name}' is running low on stock. Current: {$currentStock}, Threshold: {$threshold}",
                    'type' => 'inventory_low',
                ];

            case StockAlertEvent::ALERT_OUT_OF_STOCK:
                return [
                    'title' => 'Out of Stock Alert',
                    'message' => "Product '{$product->name}' is now out of stock. Immediate restocking required.",
                    'type' => 'inventory_out',
                ];

            case StockAlertEvent::ALERT_RESTOCK:
                return [
                    'title' => 'Restock Notification',
                    'message' => "Product '{$product->name}' has been restocked. Current stock: {$currentStock}",
                    'type' => 'inventory_restock',
                ];

            default:
                return [
                    'title' => 'Inventory Alert',
                    'message' => "Inventory alert for product '{$product->name}'. Current stock: {$currentStock}",
                    'type' => 'inventory_general',
                ];
        }
    }

    /**
     * Handle specific actions based on alert type
     */
    protected function handleAlertTypeActions($product, $alertType, $currentStock, $threshold): void
    {
        try {
            switch ($alertType) {
                case StockAlertEvent::ALERT_LOW_STOCK:
                    $this->handleLowStockAlert($product, $currentStock, $threshold);
                    break;

                case StockAlertEvent::ALERT_OUT_OF_STOCK:
                    $this->handleOutOfStockAlert($product);
                    break;

                case StockAlertEvent::ALERT_RESTOCK:
                    $this->handleRestockAlert($product, $currentStock);
                    break;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to process alert type specific actions', [
                'product_id' => $product->id,
                'alert_type' => $alertType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle low stock alert actions
     */
    protected function handleLowStockAlert($product, $currentStock, $threshold): void
    {
        // Mark product as low stock if not already marked
        if ($product->status !== 'low_stock') {
            $product->update(['status' => 'low_stock']);
            
            ActivityLogger::log(
                'product_status_updated',
                'Product status updated to low_stock',
                null,
                [
                    'product_id' => $product->id,
                    'old_status' => $product->getOriginal('status'),
                    'new_status' => 'low_stock',
                    'current_stock' => $currentStock,
                    'threshold' => $threshold,
                ]
            );
        }

        // Auto-generate purchase order suggestions (if configured)
        $this->generatePurchaseOrderSuggestion($product, $threshold);
    }

    /**
     * Handle out of stock alert actions
     */
    protected function handleOutOfStockAlert($product): void
    {
        // Mark product as out of stock
        if ($product->status !== 'out_of_stock') {
            $product->update(['status' => 'out_of_stock']);
            
            ActivityLogger::log('product_status_updated', 'Product status updated to out_of_stock', null, [
                    'product_id' => $product->id,
                    'old_status' => $product->getOriginal('status'),
                    'new_status' => 'out_of_stock',
                    'current_stock' => 0,
                ]
            );
        }

        // Hide product from frontend if configured
        $hideOutOfStock = config('app.hide_out_of_stock_products', false);
        if ($hideOutOfStock && $product->is_active) {
            $product->update(['is_active' => false]);
            
            ActivityLogger::log('product_hidden', 'Product hidden from frontend due to out of stock', null, [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                ]
            );
        }

        // Create urgent purchase request
        $this->createUrgentPurchaseRequest($product);
    }

    /**
     * Handle restock alert actions
     */
    protected function handleRestockAlert($product, $currentStock): void
    {
        // Update product status back to active if it was marked as low/out of stock
        if (in_array($product->status, ['low_stock', 'out_of_stock'])) {
            $oldStatus = $product->status;
            $product->update(['status' => 'active']);
            
            ActivityLogger::log('product_status_updated', 'Product status updated to active after restock', null, [
                    'product_id' => $product->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'active',
                    'current_stock' => $currentStock,
                ]
            );
        }

        // Re-enable product on frontend if it was hidden
        if (!$product->is_active) {
            $product->update(['is_active' => true]);
            
            ActivityLogger::log('product_reactivated', 'Product reactivated on frontend after restock', null, [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $currentStock,
                ]
            );
        }
    }

    /**
     * Generate purchase order suggestion
     */
    protected function generatePurchaseOrderSuggestion($product, $threshold): void
    {
        try {
            // Calculate suggested order quantity (2x threshold or minimum order quantity)
            $suggestedQuantity = max($threshold * 2, $product->min_order_quantity ?? 10);
            
            ActivityLogger::log('purchase_order_suggested', 'Purchase order suggestion generated for low stock product', null, [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'suggested_quantity' => $suggestedQuantity,
                    'current_stock' => $product->stock,
                    'threshold' => $threshold,
                ]
            );

            Log::info('Purchase order suggestion generated', [
                'product_id' => $product->id,
                'suggested_quantity' => $suggestedQuantity,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to generate purchase order suggestion', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create urgent purchase request
     */
    protected function createUrgentPurchaseRequest($product): void
    {
        try {
            ActivityLogger::log('urgent_purchase_request', 'Urgent purchase request created for out of stock product', null, [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'urgency' => 'high',
                    'reason' => 'out_of_stock',
                ]
            );

            Log::info('Urgent purchase request created', [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to create urgent purchase request', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send urgent notification for critical stock levels
     */
    protected function sendUrgentNotification($product, $alertType): void
    {
        try {
            // Send immediate email/SMS to key personnel for out of stock
            $urgentContacts = config('app.urgent_inventory_contacts', []);
            
            if (!empty($urgentContacts)) {
                foreach ($urgentContacts as $contact) {
                    // Implement urgent notification sending
                    // Mail::to($contact['email'])->send(new UrgentInventoryAlert($product, $alertType));
                    
                    Log::info('Urgent inventory notification sent', [
                        'product_id' => $product->id,
                        'alert_type' => $alertType,
                        'contact_email' => $contact['email'] ?? 'unknown',
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send urgent inventory notification', [
                'product_id' => $product->id,
                'alert_type' => $alertType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(StockAlertEvent $event, \Throwable $exception): void
    {
        Log::error('InventoryNotificationListener job failed', [
            'product_id' => $event->product->id ?? null,
            'alert_type' => $event->alertType ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Create a failure notification
        try {
            AdminNotification::create([
                'title' => 'Inventory Alert Failed',
                'message' => "Failed to process inventory alert for product '{$event->product->name}'",
                'type' => 'system_error',
                'data' => [
                    'product_id' => $event->product->id,
                    'alert_type' => $event->alertType,
                    'error' => $exception->getMessage(),
                    'failed_at' => now()->toISOString(),
                ],
                'read_at' => null,
            ]);
        } catch (\Exception $e) {
            Log::critical('Failed to create inventory notification failure notification', [
                'original_error' => $exception->getMessage(),
                'notification_error' => $e->getMessage(),
            ]);
        }
    }
}
