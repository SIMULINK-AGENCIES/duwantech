<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard overview
     */
    public function index()
    {
        $overviewData = $this->getOverviewData();
        $recentActivity = $this->getRecentActivity();
        $quickStats = $this->getQuickStats();
        $chartData = $this->getOverviewChartData();
        
        return view('admin.reports.index', compact(
            'overviewData',
            'recentActivity',
            'quickStats',
            'chartData'
        ));
    }

    /**
     * Display the revenue reports dashboard
     */
    public function revenue()
    {
        $revenueData = $this->getRevenueData();
        $salesData = $this->getSalesData();
        $topProducts = $this->getTopSellingProducts();
        $customerData = $this->getCustomerData();
        
        return view('admin.reports.revenue', compact(
            'revenueData',
            'salesData', 
            'topProducts',
            'customerData'
        ));
    }

    /**
     * Display sales reports
     */
    public function sales()
    {
        $salesMetrics = $this->getSalesMetrics();
        $salesTrends = $this->getSalesTrends();
        $productPerformance = $this->getProductPerformance();
        
        return view('admin.reports.sales', compact(
            'salesMetrics',
            'salesTrends',
            'productPerformance'
        ));
    }

    /**
     * Display customer reports
     */
    public function customers()
    {
        $customerMetrics = $this->getCustomerMetrics();
        $customerActivity = $this->getCustomerActivity();
        $customerSegments = $this->getCustomerSegments();
        
        return view('admin.reports.customers', compact(
            'customerMetrics',
            'customerActivity',
            'customerSegments'
        ));
    }

    /**
     * Display product reports
     */
    public function products()
    {
        $productMetrics = $this->getProductMetrics();
        $inventoryStatus = $this->getInventoryStatus();
        $categoryPerformance = $this->getCategoryPerformance();
        
        return view('admin.reports.products', compact(
            'productMetrics',
            'inventoryStatus',
            'categoryPerformance'
        ));
    }

    /**
     * Get revenue data for API
     */
    public function getRevenueApi(Request $request): JsonResponse
    {
        $period = $request->get('period', '30days');
        $data = $this->getRevenueDataByPeriod($period);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get sales data for API
     */
    public function getSalesApi(Request $request): JsonResponse
    {
        $period = $request->get('period', '30days');
        $data = $this->getSalesDataByPeriod($period);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Export revenue report
     */
    public function exportRevenue(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $period = $request->get('period', '30days');
        
        $data = $this->getRevenueDataByPeriod($period);
        
        if ($format === 'csv') {
            return $this->exportToCsv($data, 'revenue_report');
        }
        
        return $this->exportToPdf($data, 'revenue_report');
    }

    /**
     * Get revenue data
     */
    protected function getRevenueData(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        
        return [
            'today' => Order::whereDate('created_at', $today)
                ->where('status', 'paid')
                ->sum('amount'),
            
            'this_week' => Order::whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->where('status', 'paid')
                ->sum('amount'),
            
            'this_month' => Order::whereBetween('created_at', [
                    $thisMonth,
                    Carbon::now()->endOfMonth()
                ])
                ->where('status', 'paid')
                ->sum('amount'),
            
            'last_month' => Order::whereBetween('created_at', [
                    $lastMonth,
                    $lastMonth->copy()->endOfMonth()
                ])
                ->where('status', 'paid')
                ->sum('amount'),
            
            'this_year' => Order::whereBetween('created_at', [
                    $thisYear,
                    Carbon::now()->endOfYear()
                ])
                ->where('status', 'paid')
                ->sum('amount'),
            
            'total' => Order::where('status', 'paid')
                ->sum('amount'),
        ];
    }

    /**
     * Get sales data
     */
    protected function getSalesData(): array
    {
        $dailySales = Order::where('status', 'paid')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthlySales = Order::where('status', 'paid')
            ->whereDate('created_at', '>=', Carbon::now()->subMonths(12))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total, COUNT(*) as orders')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return [
            'daily' => $dailySales,
            'monthly' => $monthlySales,
            'chart_data' => [
                'labels' => $dailySales->pluck('date')->toArray(),
                'revenue' => $dailySales->pluck('total')->toArray(),
                'orders' => $dailySales->pluck('orders')->toArray(),
            ]
        ];
    }

    /**
     * Get top selling products
     */
    protected function getTopSellingProducts(int $limit = 10): array
    {
        return Order::join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.status', 'paid')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                DB::raw('COUNT(orders.id) as total_sold'),
                DB::raw('SUM(orders.amount) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get customer data
     */
    protected function getCustomerData(): array
    {
        $totalCustomers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();
        
        $newCustomers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->whereDate('created_at', '>=', Carbon::now()->subDays(30))
        ->count();
        
        $topCustomers = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'paid')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.amount) as total_spent')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return [
            'total' => $totalCustomers,
            'new_this_month' => $newCustomers,
            'top_customers' => $topCustomers,
            'retention_rate' => $this->calculateRetentionRate(),
        ];
    }

    /**
     * Get sales metrics
     */
    protected function getSalesMetrics(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        
        $thisMonthOrders = Order::whereBetween('created_at', [
            $thisMonth, Carbon::now()
        ])->count();
        
        $lastMonthOrders = Order::whereBetween('created_at', [
            $lastMonth->startOfMonth(), $lastMonth->endOfMonth()
        ])->count();
        
        $avgOrderValue = Order::where('status', 'paid')->avg('amount');
        
        $conversionRate = $this->calculateConversionRate();
        
        return [
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('status', 'paid')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'this_month_orders' => $thisMonthOrders,
            'last_month_orders' => $lastMonthOrders,
            'growth_rate' => $lastMonthOrders > 0 ? 
                (($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 : 0,
            'avg_order_value' => round($avgOrderValue, 2),
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * Get customer metrics
     */
    protected function getCustomerMetrics(): array
    {
        $totalUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();
        
        $activeUsers = DB::table('active_sessions')
            ->where('last_activity', '>', Carbon::now()->subMinutes(15))
            ->distinct('user_id')
            ->count();
        
        return [
            'total_customers' => $totalUsers,
            'active_customers' => $activeUsers,
            'new_customers_today' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })->whereDate('created_at', Carbon::today())
            ->count(),
            'customers_with_orders' => Order::distinct('user_id')->count(),
        ];
    }

    /**
     * Get product metrics
     */
    protected function getProductMetrics(): array
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'out_of_stock' => 0, // Stock tracking not implemented yet
            'low_stock' => 0, // Stock tracking not implemented yet
            'categories' => DB::table('categories')->count(),
            'avg_product_price' => Product::avg('price'),
        ];
    }

    /**
     * Calculate retention rate
     */
    protected function calculateRetentionRate(): float
    {
        $usersWithMultipleOrders = DB::table('orders')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();
        
        $totalUsers = DB::table('orders')
            ->distinct('user_id')
            ->count();
        
        return $totalUsers > 0 ? ($usersWithMultipleOrders / $totalUsers) * 100 : 0;
    }

    /**
     * Calculate conversion rate
     */
    protected function calculateConversionRate(): float
    {
        $totalVisitors = ActivityLog::distinct('user_id')->count();
        $totalOrders = Order::distinct('user_id')->count();
        
        return $totalVisitors > 0 ? ($totalOrders / $totalVisitors) * 100 : 0;
    }

    /**
     * Get revenue data by period
     */
    protected function getRevenueDataByPeriod(string $period): array
    {
        $query = Order::where('status', 'completed');
        
        switch ($period) {
            case '7days':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(7));
                break;
            case '30days':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(30));
                break;
            case '90days':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(90));
                break;
            case '1year':
                $query->whereDate('created_at', '>=', Carbon::now()->subYear());
                break;
        }
        
        return $query->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get sales data by period
     */
    protected function getSalesDataByPeriod(string $period): array
    {
        return $this->getRevenueDataByPeriod($period);
    }

    /**
     * Get sales trends
     */
    protected function getSalesTrends(): array
    {
        return [
            'weekly' => $this->getWeeklyTrends(),
            'monthly' => $this->getMonthlyTrends(),
            'yearly' => $this->getYearlyTrends(),
        ];
    }

    /**
     * Get weekly trends
     */
    protected function getWeeklyTrends(): array
    {
        return Order::where('status', 'paid')
            ->whereDate('created_at', '>=', Carbon::now()->subWeeks(8))
            ->selectRaw('YEARWEEK(created_at) as week, SUM(amount) as revenue, COUNT(*) as orders')
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->toArray();
    }

    /**
     * Get monthly trends
     */
    protected function getMonthlyTrends(): array
    {
        return Order::where('status', 'paid')
            ->whereDate('created_at', '>=', Carbon::now()->subMonths(12))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as revenue, COUNT(*) as orders')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    /**
     * Get yearly trends
     */
    protected function getYearlyTrends(): array
    {
        return Order::where('status', 'paid')
            ->selectRaw('YEAR(created_at) as year, SUM(amount) as revenue, COUNT(*) as orders')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->toArray();
    }

    /**
     * Get product performance
     */
    protected function getProductPerformance(): array
    {
        return $this->getTopSellingProducts(20);
    }

    /**
     * Get customer activity
     */
    protected function getCustomerActivity(): array
    {
        return ActivityLog::join('users', 'activity_logs.user_id', '=', 'users.id')
            ->where('users.role', 'user')
            ->whereDate('activity_logs.created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(activity_logs.created_at) as date, COUNT(*) as activities')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get customer segments
     */
    protected function getCustomerSegments(): array
    {
        $segments = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'paid')
            ->selectRaw('
                users.id,
                users.name,
                users.email,
                COUNT(orders.id) as order_count,
                SUM(orders.amount) as total_spent,
                AVG(orders.amount) as avg_order_value,
                CASE 
                    WHEN SUM(orders.amount) >= 10000 THEN "VIP"
                    WHEN SUM(orders.amount) >= 5000 THEN "Premium"
                    WHEN SUM(orders.amount) >= 1000 THEN "Regular"
                    ELSE "New"
                END as segment
            ')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();

        return [
            'vip' => $segments->where('segment', 'VIP')->count(),
            'premium' => $segments->where('segment', 'Premium')->count(),
            'regular' => $segments->where('segment', 'Regular')->count(),
            'new' => $segments->where('segment', 'New')->count(),
            'details' => $segments->toArray(),
        ];
    }

    /**
     * Get inventory status
     */
    protected function getInventoryStatus(): array
    {
        return [
            'total_products' => Product::count(),
            'in_stock' => Product::where('is_active', true)->count(),
            'out_of_stock' => 0, // Stock tracking not implemented yet
            'low_stock' => 0, // Stock tracking not implemented yet
            'products' => Product::select('id', 'name', 'price', 'is_active')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->toArray(),
        ];
    }

    /**
     * Get category performance
     */
    protected function getCategoryPerformance(): array
    {
        return Order::join('products', 'orders.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'paid')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(orders.id) as total_sold'),
                DB::raw('SUM(orders.amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Export data to CSV
     */
    protected function exportToCsv(array $data, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return response()->streamDownload(function () use ($data) {
            $output = fopen('php://output', 'w');
            
            if (!empty($data)) {
                // Write headers
                fputcsv($output, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fclose($output);
        }, "{$filename}.csv", $headers);
    }

    /**
     * Export data to PDF (placeholder - would need PDF library)
     */
    protected function exportToPdf(array $data, string $filename)
    {
        // This would require a PDF library like TCPDF or DomPDF
        // For now, return JSON response
        return response()->json([
            'message' => 'PDF export would be implemented with a PDF library',
            'data' => $data
        ]);
    }

    /**
     * Get overview data for the reports dashboard
     */
    protected function getOverviewData(): array
    {
        $revenue = $this->getRevenueData();
        $sales = $this->getSalesMetrics();
        $customers = $this->getCustomerMetrics();
        $products = $this->getProductMetrics();
        
        return [
            'revenue' => $revenue,
            'sales' => $sales,
            'customers' => $customers,
            'products' => $products,
        ];
    }

    /**
     * Get recent activity for dashboard
     */
    protected function getRecentActivity(): array
    {
        $recentOrders = Order::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'order',
                    'message' => "New order #{$order->order_number} from {$order->user->name}",
                    'amount' => $order->amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                ];
            });

        $recentCustomers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'customer',
                    'message' => "New customer registered: {$user->name}",
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ];
            });

        return $recentOrders->merge($recentCustomers)
            ->sortByDesc('created_at')
            ->take(15)
            ->values()
            ->toArray();
    }

    /**
     * Get quick stats for dashboard
     */
    protected function getQuickStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();
        
        // Today vs Yesterday
        $todayRevenue = Order::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('amount');
        $yesterdayRevenue = Order::where('status', 'paid')
            ->whereDate('created_at', $yesterday)
            ->sum('amount');
        
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();
        
        // This week vs Last week
        $thisWeekRevenue = Order::where('status', 'paid')
            ->whereBetween('created_at', [$thisWeek, Carbon::now()])
            ->sum('amount');
        $lastWeekRevenue = Order::where('status', 'paid')
            ->whereBetween('created_at', [$lastWeek, $lastWeek->copy()->endOfWeek()])
            ->sum('amount');
        
        return [
            'today_revenue' => $todayRevenue,
            'yesterday_revenue' => $yesterdayRevenue,
            'revenue_change' => $yesterdayRevenue > 0 ? 
                (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0,
            
            'today_orders' => $todayOrders,
            'yesterday_orders' => $yesterdayOrders,
            'orders_change' => $yesterdayOrders > 0 ? 
                (($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100 : 0,
            
            'week_revenue' => $thisWeekRevenue,
            'last_week_revenue' => $lastWeekRevenue,
            'week_revenue_change' => $lastWeekRevenue > 0 ? 
                (($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100 : 0,
        ];
    }

    /**
     * Get chart data for overview dashboard
     */
    protected function getOverviewChartData(): array
    {
        // Revenue trend for last 7 days
        $revenueTrend = Order::where('status', 'paid')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Orders by status
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Top categories by revenue
        $topCategories = Order::join('products', 'orders.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'paid')
            ->selectRaw('categories.name, SUM(orders.amount) as revenue')
            ->groupBy('categories.name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        return [
            'revenue_trend' => [
                'labels' => $revenueTrend->pluck('date')->toArray(),
                'data' => $revenueTrend->pluck('revenue')->toArray(),
            ],
            'orders_by_status' => [
                'labels' => $ordersByStatus->pluck('status')->toArray(),
                'data' => $ordersByStatus->pluck('count')->toArray(),
            ],
            'top_categories' => [
                'labels' => $topCategories->pluck('name')->toArray(),
                'data' => $topCategories->pluck('revenue')->toArray(),
            ],
        ];
    }
}
