<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get real-time metrics for dashboard widgets.
     */
    public function getRealTimeMetrics(): array
    {
        return Cache::remember('dashboard_metrics', 30, function () {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $thisMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();
            
            return [
                'overview' => $this->getOverviewMetrics(),
                'sales' => $this->getSalesMetrics($today, $yesterday, $thisMonth, $lastMonth),
                'orders' => $this->getOrderMetrics($today, $yesterday),
                'products' => $this->getProductMetrics(),
                'users' => $this->getUserMetrics($today, $yesterday),
                'revenue' => $this->getRevenueMetrics($thisMonth, $lastMonth),
                'alerts' => $this->getSystemAlerts(),
                'performance' => $this->getPerformanceMetrics(),
            ];
        });
    }

    /**
     * Get overview metrics.
     */
    protected function getOverviewMetrics(): array
    {
        return [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'active_sessions' => $this->getActiveSessions(),
            'system_health' => $this->getSimpleSystemHealth(),
        ];
    }

    /**
     * Get sales metrics with comparison.
     */
    protected function getSalesMetrics($today, $yesterday, $thisMonth, $lastMonth): array
    {
        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('amount');
            
        $yesterdaySales = Order::whereDate('created_at', $yesterday)
            ->where('status', 'paid')
            ->sum('amount');
            
        $thisMonthSales = Order::where('created_at', '>=', $thisMonth)
            ->where('status', 'paid')
            ->sum('amount');
            
        $lastMonthSales = Order::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $thisMonth)
            ->where('status', 'paid')
            ->sum('amount');

        return [
            'today' => $todaySales,
            'yesterday' => $yesterdaySales,
            'this_month' => $thisMonthSales,
            'last_month' => $lastMonthSales,
            'daily_change' => $this->calculatePercentageChange($todaySales, $yesterdaySales),
            'monthly_change' => $this->calculatePercentageChange($thisMonthSales, $lastMonthSales),
        ];
    }

    /**
     * Get order metrics with status breakdown.
     */
    protected function getOrderMetrics($today, $yesterday): array
    {
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $todayOrders = Order::whereDate('created_at', $today)->count();
        $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();

        return [
            'by_status' => $ordersByStatus,
            'today' => $todayOrders,
            'yesterday' => $yesterdayOrders,
            'daily_change' => $this->calculatePercentageChange($todayOrders, $yesterdayOrders),
            'pending' => $ordersByStatus['pending'] ?? 0,
            'processing' => $ordersByStatus['processing'] ?? 0,
            'shipped' => $ordersByStatus['shipped'] ?? 0,
            'delivered' => $ordersByStatus['delivered'] ?? 0,
            'cancelled' => $ordersByStatus['cancelled'] ?? 0,
        ];
    }

    /**
     * Get product metrics.
     */
    protected function getProductMetrics(): array
    {
                $lowStockThreshold = 10;
        
        return [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'low_stock' => 0, // TODO: Add stock column to products table
            'out_of_stock' => 0, // TODO: Add stock column to products table
        ];
    }

    /**
     * Get user metrics.
     */
    protected function getUserMetrics($today, $yesterday): array
    {
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $newUsersYesterday = User::whereDate('created_at', $yesterday)->count();
        // Calculate active users from active sessions in the last 24 hours
        $activeUsers = DB::table('active_sessions')
            ->where('last_activity', '>=', Carbon::now()->subHours(24))
            ->distinct('user_id')
            ->whereNotNull('user_id')
            ->count('user_id');

        return [
            'total' => User::count(),
            'new_today' => $newUsersToday,
            'new_yesterday' => $newUsersYesterday,
            'active_24h' => $activeUsers,
            'daily_change' => $this->calculatePercentageChange($newUsersToday, $newUsersYesterday),
            'online_now' => $this->getOnlineUsers(),
        ];
    }

    /**
     * Get revenue metrics with trends.
     */
    protected function getRevenueMetrics($thisMonth, $lastMonth): array
    {
        $thisMonthRevenue = Order::where('created_at', '>=', $thisMonth)
            ->where('status', 'paid')
            ->sum('amount');
            
        $lastMonthRevenue = Order::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $thisMonth)
            ->where('status', 'paid')
            ->sum('amount');

        return [
            'total' => Order::where('status', 'paid')->sum('amount'),
            'this_month' => $thisMonthRevenue,
            'last_month' => $lastMonthRevenue,
            'monthly_change' => $this->calculatePercentageChange($thisMonthRevenue, $lastMonthRevenue),
            'average_order_value' => $this->getAverageOrderValue(),
            'projected_monthly' => $this->getProjectedMonthlyRevenue(),
        ];
    }

    /**
     * Get system alerts and notifications.
     */
    protected function getSystemAlerts(): array
    {
        $alerts = AdminNotification::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return [
            'unread_count' => AdminNotification::where('is_read', false)->count(),
            'critical_count' => AdminNotification::where('is_read', false)
                ->where('type', 'critical')
                ->count(),
            'recent' => $alerts->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'title' => $alert->title,
                    'type' => $alert->type,
                    'created_at' => $alert->created_at,
                ];
            }),
        ];
    }

    /**
     * Get performance metrics.
     */
    protected function getPerformanceMetrics(): array
    {
        return [
            'response_time' => $this->getAverageResponseTime(),
            'error_rate' => $this->getErrorRate(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'queue_size' => $this->getQueueSize(),
            'server_load' => $this->getServerLoad(),
        ];
    }

    /**
     * Get chart data for real-time charts.
     */
    public function getChartData(string $type, string $period = '24h'): array
    {
        return match($type) {
            'sales' => $this->getSalesChartData($period),
            'orders' => $this->getOrdersChartData($period),
            'users' => $this->getUsersChartData($period),
            'revenue' => $this->getRevenueChartData($period),
            'traffic' => $this->getTrafficChartData($period),
            default => [],
        };
    }

    /**
     * Get sales chart data.
     */
    protected function getSalesChartData(string $period): array
    {
        $query = Order::where('status', 'paid');
        
        return match($period) {
            '24h' => $this->getHourlyData($query, 'amount'),
            '7d' => $this->getDailyData($query, 'amount', 7),
            '30d' => $this->getDailyData($query, 'amount', 30),
            '12m' => $this->getMonthlyData($query, 'amount', 12),
            default => [],
        };
    }

    /**
     * Get orders chart data.
     */
    protected function getOrdersChartData(string $period): array
    {
        $query = Order::query();
        
        return match($period) {
            '24h' => $this->getHourlyData($query, 'count'),
            '7d' => $this->getDailyData($query, 'count', 7),
            '30d' => $this->getDailyData($query, 'count', 30),
            '12m' => $this->getMonthlyData($query, 'count', 12),
            default => [],
        };
    }

    /**
     * Get users chart data.
     */
    protected function getUsersChartData(string $period): array
    {
        $query = User::query();
        
        return match($period) {
            '24h' => $this->getHourlyData($query, 'count'),
            '7d' => $this->getDailyData($query, 'count', 7),
            '30d' => $this->getDailyData($query, 'count', 30),
            '12m' => $this->getMonthlyData($query, 'count', 12),
            default => [],
        };
    }

    /**
     * Get revenue chart data.
     */
    protected function getRevenueChartData(string $period): array
    {
        $query = Order::where('status', 'paid');
        
        return match($period) {
            '24h' => $this->getHourlyData($query, 'amount'),
            '7d' => $this->getDailyData($query, 'amount', 7),
            '30d' => $this->getDailyData($query, 'amount', 30),
            '12m' => $this->getMonthlyData($query, 'amount', 12),
            default => [],
        };
    }

    /**
     * Get traffic chart data.
     */
    protected function getTrafficChartData(string $period): array
    {
        // This would integrate with your analytics system
        $labels = [];
        $values = [];
        
        if ($period === '24h') {
            for ($i = 0; $i < 24; $i++) {
                $labels[] = Carbon::now()->subHours(23 - $i)->format('H:00');
                $values[] = rand(50, 200); // Placeholder data
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Page Views',
                    'data' => $values,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ]
            ]
        ];
    }

    /**
     * Get hourly data for charts.
     */
    protected function getHourlyData($query, string $metric): array
    {
        $data = $query->where('created_at', '>=', Carbon::now()->subHours(24))
            ->selectRaw($metric === 'count' ? 
                'HOUR(created_at) as hour, COUNT(*) as value' : 
                'HOUR(created_at) as hour, SUM(amount) as value'
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $labels = [];
        $values = [];
        
        for ($i = 0; $i < 24; $i++) {
            $hour = Carbon::now()->subHours(23 - $i)->format('H:00');
            $labels[] = $hour;
            $values[] = $data->where('hour', $i)->first()->value ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ucfirst($metric),
                    'data' => $values,
                    'borderColor' => '#667eea',
                    'backgroundColor' => 'rgba(102, 126, 234, 0.1)',
                    'tension' => 0.4,
                ]
            ]
        ];
    }

    /**
     * Get daily data for charts.
     */
    protected function getDailyData($query, string $metric, int $days): array
    {
        $data = $query->where('created_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw($metric === 'count' ? 
                'DATE(created_at) as date, COUNT(*) as value' : 
                'DATE(created_at) as date, SUM(amount) as value'
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');
            $values[] = $data->where('date', $date->format('Y-m-d'))->first()->value ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ucfirst($metric),
                    'data' => $values,
                    'borderColor' => '#667eea',
                    'backgroundColor' => 'rgba(102, 126, 234, 0.1)',
                    'tension' => 0.4,
                ]
            ]
        ];
    }

    /**
     * Get monthly data for charts.
     */
    protected function getMonthlyData($query, string $metric, int $months): array
    {
        $data = $query->where('created_at', '>=', Carbon::now()->subMonths($months))
            ->selectRaw($metric === 'count' ? 
                'YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as value' : 
                'YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as value'
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            $monthData = $data->where('year', $date->year)
                            ->where('month', $date->month)
                            ->first();
            $values[] = $monthData ? $monthData->value : 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ucfirst($metric),
                    'data' => $values,
                    'borderColor' => '#667eea',
                    'backgroundColor' => 'rgba(102, 126, 234, 0.1)',
                    'tension' => 0.4,
                ]
            ]
        ];
    }

    /**
     * Calculate percentage change.
     */
    protected function calculatePercentageChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Get top selling products.
     */
    protected function getTopSellingProducts(int $limit = 5): array
    {
        return Product::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'orders_count' => $product->orders_count,
                    'image' => $product->image,
                ];
            })
            ->toArray();
    }

    /**
     * Get active sessions count.
     */
    protected function getActiveSessions(): int
    {
        // This would integrate with your session tracking system
        return Cache::get('active_sessions_count', 0);
    }

    /**
     * Get online users count.
     */
    protected function getOnlineUsers(): int
    {
        return Cache::get('online_users_count', 0);
    }

    /**
     * Get system health status.
     */
    public function getSystemHealth()
    {
        $cacheKey = 'dashboard:system_health_detailed';
        
        return Cache::remember($cacheKey, 30, function () {
            return [
                'overall_status' => $this->getOverallSystemStatus(),
                'performance' => [
                    'server_load' => $this->getServerLoad(),
                    'response_time' => $this->getAverageResponseTime(),
                    'cache_hit_rate' => $this->getCacheHitRate(),
                    'queue_size' => $this->getQueueSize(),
                    'error_rate' => $this->getErrorRate(),
                ],
                'services' => [
                    [
                        'name' => 'Web Server',
                        'status' => 'operational',
                        'uptime' => '99.9%',
                        'response_time' => 250
                    ],
                    [
                        'name' => 'Database',
                        'status' => 'operational',
                        'uptime' => '99.8%',
                        'response_time' => 15
                    ],
                    [
                        'name' => 'Cache',
                        'status' => 'operational',
                        'uptime' => '99.9%',
                        'response_time' => 5
                    ],
                    [
                        'name' => 'Queue',
                        'status' => 'operational',
                        'uptime' => '99.7%',
                        'response_time' => 10
                    ],
                    [
                        'name' => 'Mail Service',
                        'status' => 'operational',
                        'uptime' => '99.5%',
                        'response_time' => 500
                    ]
                ]
            ];
        });
    }
    
    /**
     * Get simple system health status for internal use.
     */
    protected function getSimpleSystemHealth(): string
    {
        // This would check various system metrics
        return 'healthy'; // 'healthy', 'warning', 'critical'
    }
    
    /**
     * Get overall system status
     */
    private function getOverallSystemStatus()
    {
        $load = $this->getServerLoad();
        $errorRate = $this->getErrorRate();
        $queueSize = $this->getQueueSize();
        
        if ($load > 0.8 || $errorRate > 0.05 || $queueSize > 100) {
            return 'Critical';
        }
        
        if ($load > 0.6 || $errorRate > 0.02 || $queueSize > 50) {
            return 'Warning';
        }
        
        return 'Healthy';
    }

    /**
     * Get average order value.
     */
    protected function getAverageOrderValue(): float
    {
        return Order::where('status', 'paid')->avg('amount') ?? 0;
    }

    /**
     * Get projected monthly revenue.
     */
    protected function getProjectedMonthlyRevenue(): float
    {
        $daysInMonth = Carbon::now()->daysInMonth;
        $daysPassed = Carbon::now()->day;
        $currentMonthRevenue = Order::whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'paid')
            ->sum('amount');
            
        return ($currentMonthRevenue / $daysPassed) * $daysInMonth;
    }

    /**
     * Get performance metrics (placeholder implementations).
     */
    protected function getAverageResponseTime(): float
    {
        return 0.25; // seconds
    }

    protected function getErrorRate(): float
    {
        return 0.02; // 2%
    }

    protected function getCacheHitRate(): float
    {
        return 0.95; // 95%
    }

    protected function getQueueSize(): int
    {
        return DB::table('jobs')->count();
    }

    protected function getServerLoad(): float
    {
        return 0.65; // 65%
    }
    
    /**
     * Get activity feed for dashboard
     */
    public function getActivityFeed($limit = 20)
    {
        $activities = collect();
        
        // Recent orders
        $recentOrders = \App\Models\Order::with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($order) {
                return [
                    'type' => 'order',
                    'title' => 'New Order #' . $order->id,
                    'description' => 'Order placed by ' . $order->user->name,
                    'amount' => $order->total,
                    'time' => $order->created_at,
                    'icon' => 'fas fa-shopping-cart',
                    'color' => 'text-green-600'
                ];
            });
        
        // Recent users
        $recentUsers = \App\Models\User::latest()
            ->limit($limit)
            ->get()
            ->map(function($user) {
                return [
                    'type' => 'user',
                    'title' => 'New User Registration',
                    'description' => $user->name . ' joined the platform',
                    'time' => $user->created_at,
                    'icon' => 'fas fa-user-plus',
                    'color' => 'text-blue-600'
                ];
            });
        
        // Recent products
        $recentProducts = \App\Models\Product::latest()
            ->limit($limit)
            ->get()
            ->map(function($product) {
                return [
                    'type' => 'product',
                    'title' => 'Product Added',
                    'description' => $product->name . ' was added to catalog',
                    'time' => $product->created_at,
                    'icon' => 'fas fa-box',
                    'color' => 'text-purple-600'
                ];
            });
        
        // Merge and sort activities
        $activities = $activities
            ->merge($recentOrders)
            ->merge($recentUsers)
            ->merge($recentProducts)
            ->sortByDesc('time')
            ->take($limit)
            ->values();
        
        return $activities;
    }
}
