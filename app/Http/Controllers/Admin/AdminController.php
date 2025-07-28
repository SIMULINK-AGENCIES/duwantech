<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Helpers\UserActivityHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $dashboardService = app(\App\Services\DashboardService::class);
        $metrics = $dashboardService->getRealTimeMetrics();
        
        // Get dashboard statistics
        $stats = [
            'total_orders' => $metrics['overview']['total_orders'],
            'total_products' => $metrics['overview']['total_products'],
            'total_users' => $metrics['overview']['total_users'],
            'total_categories' => $metrics['overview']['total_categories'],
            'pending_orders' => $metrics['orders']['pending'],
            'completed_orders' => $metrics['orders']['delivered'],
            'total_revenue' => $metrics['revenue']['total'],
        ];

        // Quick stats for widgets
        $quickStats = [
            'orders' => [
                'today' => $metrics['orders']['today'],
                'change' => $metrics['orders']['daily_change'],
            ],
            'revenue' => [
                'today' => $metrics['sales']['today'],
                'change' => $metrics['sales']['daily_change'],
            ],
            'users' => [
                'today' => $metrics['users']['new_today'],
                'change' => $metrics['users']['daily_change'],
            ],
            'alerts' => [
                'total' => $metrics['alerts']['unread_count'],
                'critical' => $metrics['alerts']['critical_count'],
            ],
        ];

        // Get recent orders
        $recentOrders = Order::with(['user', 'product'])
            ->latest()
            ->take(10)
            ->get();

        // Get top selling products
        $topProducts = Product::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        // Get monthly revenue data for chart
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', date('Y'))
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
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
    
    /**
     * Get live user statistics for the admin monitoring dashboard
     */
    public function getLiveStats(Request $request)
    {
        try {
            // Get live user statistics
            $stats = UserActivityHelper::getLiveStats();
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString(),
                'message' => 'Live statistics retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to get live stats: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve live statistics',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
} 