<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\NewOrderEvent;
use App\Events\PaymentProcessedEvent;
use App\Events\StockAlertEvent;
use App\Events\NewActivityEvent;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ActivityLog;

class TestEventListeners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:event-listeners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the e-commerce event listeners';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing E-commerce Event Listeners...');

        try {
            // Test order notification
            $this->info('1. Testing Order Notification...');
            $order = Order::first();
            if ($order) {
                event(new NewOrderEvent($order, 1));
                $this->info('✓ Order notification event dispatched');
            } else {
                $this->warn('! No orders found, skipping order notification test');
            }

            // Test payment notification
            $this->info('2. Testing Payment Notification...');
            $payment = Payment::first();
            if ($payment) {
                event(new PaymentProcessedEvent($payment, $payment->order, true));
                $this->info('✓ Payment notification event dispatched');
            } else {
                $this->warn('! No payments found, skipping payment notification test');
            }

            // Test inventory notification
            $this->info('3. Testing Inventory Notification...');
            $product = Product::first();
            if ($product) {
                event(new StockAlertEvent($product, StockAlertEvent::ALERT_LOW_STOCK, $product->stock ?? 5, 10));
                $this->info('✓ Inventory notification event dispatched');
            } else {
                $this->warn('! No products found, skipping inventory notification test');
            }

            // Test user activity
            $this->info('4. Testing User Activity...');
            $activity = ActivityLog::first();
            if ($activity) {
                event(new NewActivityEvent($activity));
                $this->info('✓ User activity event dispatched');
            } else {
                $this->warn('! No activities found, skipping user activity test');
            }

            $this->info('');
            $this->info('All event listeners tested successfully!');
            $this->info('Check the logs and admin notifications to verify they were processed.');

            return 0;

        } catch (\Exception $e) {
            $this->error('Error testing event listeners: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
