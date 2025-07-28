<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get current statistics
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::whereDoesntHave('roles', function($query) {
            $query->where('name', 'admin');
        })->count();
        $totalCategories = Category::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'paid')->count();
        $totalRevenue = Order::where('status', 'paid')->sum('amount');

        $stats = [
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'total_users' => $totalUsers,
            'total_categories' => $totalCategories,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'total_revenue' => $totalRevenue,
        ];

        // Get today's statistics with change comparison
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();
        $ordersChange = $yesterdayOrders > 0 ? (($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100 : 0;

        $todayRevenue = Order::whereDate('created_at', $today)->where('status', 'paid')->sum('amount');
        $yesterdayRevenue = Order::whereDate('created_at', $yesterday)->where('status', 'paid')->sum('amount');
        $revenueChange = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0;

        $todayUsers = User::whereDate('created_at', $today)->whereDoesntHave('roles', function($query) {
            $query->where('name', 'admin');
        })->count();
        $yesterdayUsers = User::whereDate('created_at', $yesterday)->whereDoesntHave('roles', function($query) {
            $query->where('name', 'admin');
        })->count();
        $usersChange = $yesterdayUsers > 0 ? (($todayUsers - $yesterdayUsers) / $yesterdayUsers) * 100 : 0;

        $criticalAlerts = Order::where('status', 'pending')->where('created_at', '<', Carbon::now()->subHours(24))->count();
        $lowStockProducts = 0; // Product model doesn't have stock_quantity field yet
        $totalAlerts = $criticalAlerts + $lowStockProducts;

        $quickStats = [
            'orders' => ['today' => $todayOrders, 'change' => round($ordersChange, 1)],
            'revenue' => ['today' => $todayRevenue, 'change' => round($revenueChange, 1)],
            'users' => ['today' => $todayUsers, 'change' => round($usersChange, 1)],
            'alerts' => ['total' => $totalAlerts, 'critical' => $criticalAlerts],
        ];

        // Get recent orders
        $recentOrders = Order::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get top products by order count
        $topProducts = Product::select('products.*', DB::raw('COUNT(orders.id) as order_count'))
            ->leftJoin('orders', 'products.id', '=', 'orders.product_id')
            ->groupBy('products.id')
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();

        // Get monthly revenue data for chart
        $monthlyRevenue = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as revenue')
            )
            ->where('status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.dashboard.index', compact(
            'stats', 
            'quickStats', 
            'recentOrders', 
            'topProducts', 
            'monthlyRevenue'
        ));
    }

    public function settings()
    {
        return view('admin.settings.index');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Search Orders
        $orders = Order::where('id', 'like', "%{$query}%")
            ->orWhere('reference', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($orders as $order) {
            $results[] = [
                'type' => 'order',
                'title' => "Order #{$order->id}",
                'description' => "Amount: KSh " . number_format($order->amount, 2) . " • Status: " . ucfirst($order->status),
                'url' => route('admin.orders.show', $order),
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11h8"></path></svg>',
                'iconBg' => 'bg-blue-100 text-blue-600',
                'badge' => ucfirst($order->status),
                'badgeClass' => $order->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
            ];
        }

        // Search Products
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description_short', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($products as $product) {
            $results[] = [
                'type' => 'product',
                'title' => $product->name,
                'description' => "Price: KSh " . number_format($product->price, 2) . " • Stock: " . ($product->quantity ?? 'N/A'),
                'url' => route('admin.products.show', $product),
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                'iconBg' => 'bg-green-100 text-green-600',
                'badge' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock',
                'badgeClass' => $product->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
            ];
        }

        // Search Users/Customers
        $users = User::whereDoesntHave('roles', function($q) {
                $q->where('name', 'admin');
            })
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        foreach ($users as $user) {
            $results[] = [
                'type' => 'customer',
                'title' => $user->name,
                'description' => $user->email . " • Joined: " . $user->created_at->format('M Y'),
                'url' => route('admin.users.show', $user),
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
                'iconBg' => 'bg-purple-100 text-purple-600',
                'badge' => 'Customer',
                'badgeClass' => 'bg-blue-100 text-blue-800'
            ];
        }

        return response()->json(['results' => $results]);
    }
}