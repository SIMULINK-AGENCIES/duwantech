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
}