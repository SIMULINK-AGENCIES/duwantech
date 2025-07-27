<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get users who don't have admin role (regular customers)
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
        
        // If no regular users exist, create some
        if ($users->isEmpty()) {
            $this->command->info('Creating sample customer users...');
            for ($i = 1; $i <= 10; $i++) {
                User::create([
                    'name' => "Customer {$i}",
                    'email' => "customer{$i}@example.com",
                    'password' => bcrypt('password'),
                    'phone' => '+254' . rand(700000000, 799999999),
                    'email_verified_at' => now(),
                ]);
            }
            $users = User::whereDoesntHave('roles')->get();
        }
        
        $products = Product::all();
        
        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please seed products first.');
            return;
        }

        // Create orders for the last 3 months
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();
        
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $product = $products->random();
            $orderDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            
            Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => $user->id,
                'product_id' => $product->id,
                'amount' => $product->price,
                'status' => collect(['pending', 'paid', 'failed', 'cancelled'])->random(),
                'payment_method' => 'mpesa',
                'phone_number' => $user->phone ?? '+254' . rand(700000000, 799999999),
                'notes' => 'Sample order for testing',
                'paid_at' => collect(['pending', 'paid'])->random() === 'paid' ? $orderDate : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }
        
        $this->command->info('Sample orders created successfully!');
    }
}
