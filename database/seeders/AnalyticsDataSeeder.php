<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class AnalyticsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating sample analytics data...');

        // Create some sample orders for the last 30 days
        $this->createSampleOrders();
        
        $this->command->info('✅ Analytics sample data created successfully!');
    }

    /**
     * Create sample orders for analytics
     */
    private function createSampleOrders(): void
    {
        $users = User::all();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️  No users or products found. Please run UsersTableSeeder and ProductSeeder first.');
            return;
        }

        // Create orders for the last 30 days
        for ($days = 30; $days >= 0; $days--) {
            $date = Carbon::now()->subDays($days);
            
            // Random number of orders per day (0-8)
            $ordersPerDay = rand(0, 8);
            
            for ($i = 0; $i < $ordersPerDay; $i++) {
                $user = $users->random();
                $product = $products->random();
                
                // Create realistic order amounts
                $baseAmount = rand(50, 500);
                $quantity = rand(1, 3);
                $amount = $baseAmount * $quantity;
                
                // Random order status with higher probability of paid orders
                $statusOptions = ['pending', 'paid', 'paid', 'paid', 'cancelled'];
                $status = $statusOptions[array_rand($statusOptions)];
                
                Order::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'amount' => $amount,
                    'status' => $status,
                    'phone_number' => '+254' . rand(700000000, 799999999),
                    'created_at' => $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                    'updated_at' => $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59))
                ]);
            }
        }

        // Create some orders for today with hourly distribution
        $today = Carbon::today();
        $currentHour = Carbon::now()->hour;
        
        for ($hour = 0; $hour <= $currentHour; $hour++) {
            if (rand(0, 100) < 60) { // 60% chance of orders each hour
                $ordersThisHour = rand(1, 3);
                
                for ($i = 0; $i < $ordersThisHour; $i++) {
                    $user = $users->random();
                    $product = $products->random();
                    
                    $amount = rand(75, 400);
                    $status = rand(0, 100) < 80 ? 'paid' : 'pending'; // 80% paid orders
                    
                    Order::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'order_number' => 'ORD-' . strtoupper(uniqid()),
                        'amount' => $amount,
                        'status' => $status,
                        'phone_number' => '+254' . rand(700000000, 799999999),
                        'created_at' => $today->copy()->addHours($hour)->addMinutes(rand(0, 59)),
                        'updated_at' => $today->copy()->addHours($hour)->addMinutes(rand(0, 59))
                    ]);
                }
            }
        }

        $totalOrders = Order::count();
        $this->command->info("📊 Created sample orders. Total orders in database: {$totalOrders}");
    }
}
