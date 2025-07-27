<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ActiveSession;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class OptimizedAnalyticsService
{
    protected $cacheTtl = 300; // 5 minutes cache
    protected $quickCacheTtl = 60; // 1 minute for real-time data

    /**
     * Get real-time sales metrics with optimized queries
     */
    public function getRealTimeSalesMetrics(): array
    {
        $cacheKey = 'analytics:real_time_sales_metrics_v2';
        
        return Cache::remember($cacheKey, $this->quickCacheTtl, function () use ($cacheKey) {
            $now = Carbon::now();
            $todayStart = $now->copy()->startOfDay();
            $yesterdayStart = $now->copy()->subDay()->startOfDay();
            $yesterdayEnd = $now->copy()->subDay()->endOfDay();
            $weekStart = $now->copy()->startOfWeek();
            $monthStart = $now->copy()->startOfMonth();
            
            // Single optimized query for today's and yesterday's metrics
            $dailyMetrics = DB::table('orders')
                ->select([
                    DB::raw('COUNT(CASE WHEN created_at >= ? THEN 1 END) as today_orders'),
                    DB::raw('COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 END) as yesterday_orders'),
                    DB::raw('SUM(CASE WHEN status = "paid" AND created_at >= ? THEN amount ELSE 0 END) as today_revenue'),
                    DB::raw('SUM(CASE WHEN status = "paid" AND created_at BETWEEN ? AND ? THEN amount ELSE 0 END) as yesterday_revenue'),
                ])
                ->setBindings([
                    $todayStart, $yesterdayStart, $yesterdayEnd,
                    $todayStart, $yesterdayStart, $yesterdayEnd
                ])
                ->first();

            // Single optimized query for weekly and monthly metrics
            $periodMetrics = DB::table('orders')
                ->select([
                    DB::raw('COUNT(CASE WHEN created_at >= ? THEN 1 END) as week_orders'),
                    DB::raw('COUNT(CASE WHEN created_at >= ? THEN 1 END) as month_orders'),
                    DB::raw('SUM(CASE WHEN status = "paid" AND created_at >= ? THEN amount ELSE 0 END) as week_revenue'),
                    DB::raw('SUM(CASE WHEN status = "paid" AND created_at >= ? THEN amount ELSE 0 END) as month_revenue'),
                ])
                ->setBindings([$weekStart, $monthStart, $weekStart, $monthStart])
                ->first();

            // Calculate percentage changes
            $orderChange = $dailyMetrics->yesterday_orders > 0 
                ? (($dailyMetrics->today_orders - $dailyMetrics->yesterday_orders) / $dailyMetrics->yesterday_orders) * 100 
                : 0;
            
            $revenueChange = $dailyMetrics->yesterday_revenue > 0 
                ? (($dailyMetrics->today_revenue - $dailyMetrics->yesterday_revenue) / $dailyMetrics->yesterday_revenue) * 100 
                : 0;

            return [
                'today' => [
                    'orders' => (int) $dailyMetrics->today_orders,
                    'revenue' => (float) $dailyMetrics->today_revenue ?: 0,
                    'order_change' => round($orderChange, 2),
                    'revenue_change' => round($revenueChange, 2),
                ],
                'yesterday' => [
                    'orders' => (int) $dailyMetrics->yesterday_orders,
                    'revenue' => (float) $dailyMetrics->yesterday_revenue ?: 0,
                ],
                'week' => [
                    'orders' => (int) $periodMetrics->week_orders,
                    'revenue' => (float) $periodMetrics->week_revenue ?: 0,
                ],
                'month' => [
                    'orders' => (int) $periodMetrics->month_orders,
                    'revenue' => (float) $periodMetrics->month_revenue ?: 0,
                ],
                'cache_key' => $cacheKey,
                'updated_at' => $now->toISOString(),
            ];
        });
    }

    /**
     * Get conversion metrics with optimized queries
     */
    public function getConversionMetrics(): array
    {
        $cacheKey = 'analytics:conversion_metrics_v2';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($cacheKey) {
            $thirtyDaysAgo = Carbon::now()->subDays(30);
            
            // Single query for all conversion metrics
            $metrics = DB::table('users as u')
                ->leftJoin('orders as o', function($join) use ($thirtyDaysAgo) {
                    $join->on('u.id', '=', 'o.user_id')
                         ->where('o.created_at', '>=', $thirtyDaysAgo);
                })
                ->select([
                    DB::raw('COUNT(DISTINCT u.id) as total_users'),
                    DB::raw('COUNT(DISTINCT CASE WHEN o.id IS NOT NULL THEN u.id END) as converting_users'),
                    DB::raw('COUNT(o.id) as total_orders'),
                    DB::raw('AVG(CASE WHEN o.status = "paid" THEN o.amount END) as avg_order_value'),
                    DB::raw('SUM(CASE WHEN o.status = "paid" THEN o.amount ELSE 0 END) as total_revenue'),
                ])
                ->where('u.created_at', '>=', $thirtyDaysAgo)
                ->first();

            $conversionRate = $metrics->total_users > 0 
                ? ($metrics->converting_users / $metrics->total_users) * 100 
                : 0;

            return [
                'conversion_rate' => round($conversionRate, 2),
                'total_visitors' => (int) $metrics->total_users,
                'total_customers' => (int) $metrics->converting_users,
                'avg_order_value' => round((float) $metrics->avg_order_value ?: 0, 2),
                'total_revenue' => (float) $metrics->total_revenue ?: 0,
                'orders_count' => (int) $metrics->total_orders,
                'cache_key' => $cacheKey,
                'period' => '30 days',
            ];
        });
    }

    /**
     * Get performance indicators with database optimization
     */
    public function getPerformanceIndicators(): array
    {
        $cacheKey = 'analytics:performance_indicators_v2';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($cacheKey) {
            $thirtyDaysAgo = Carbon::now()->subDays(30);
            
            // Optimized query using joins and aggregations
            $performance = DB::table('orders as o')
                ->leftJoin('products as p', 'o.product_id', '=', 'p.id')
                ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
                ->select([
                    DB::raw('COUNT(o.id) as total_orders'),
                    DB::raw('COUNT(CASE WHEN o.status = "paid" THEN 1 END) as successful_orders'),
                    DB::raw('COUNT(CASE WHEN o.status = "cancelled" THEN 1 END) as cancelled_orders'),
                    DB::raw('COUNT(CASE WHEN o.status = "pending" THEN 1 END) as pending_orders'),
                    DB::raw('AVG(CASE WHEN o.status = "paid" THEN o.amount END) as avg_order_value'),
                    DB::raw('COUNT(DISTINCT o.user_id) as unique_customers'),
                    DB::raw('COUNT(DISTINCT p.id) as products_sold'),
                ])
                ->where('o.created_at', '>=', $thirtyDaysAgo)
                ->first();

            $successRate = $performance->total_orders > 0 
                ? ($performance->successful_orders / $performance->total_orders) * 100 
                : 0;

            $cancellationRate = $performance->total_orders > 0 
                ? ($performance->cancelled_orders / $performance->total_orders) * 100 
                : 0;

            return [
                'order_success_rate' => round($successRate, 2),
                'order_cancellation_rate' => round($cancellationRate, 2),
                'avg_order_value' => round((float) $performance->avg_order_value ?: 0, 2),
                'total_orders' => (int) $performance->total_orders,
                'successful_orders' => (int) $performance->successful_orders,
                'pending_orders' => (int) $performance->pending_orders,
                'unique_customers' => (int) $performance->unique_customers,
                'products_sold' => (int) $performance->products_sold,
                'cache_key' => $cacheKey,
            ];
        });
    }

    /**
     * Get trend analysis with optimized time-series queries
     */
    public function getTrendAnalysis(int $days = 30): array
    {
        $cacheKey = "analytics:trend_analysis_{$days}d_v2";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($days, $cacheKey) {
            $startDate = Carbon::now()->subDays($days)->startOfDay();
            
            // Single optimized query for daily trends
            $dailyTrends = DB::table('orders')
                ->select([
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as orders'),
                    DB::raw('SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as revenue'),
                    DB::raw('COUNT(DISTINCT user_id) as customers'),
                ])
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            // Transform data for frontend consumption
            $trends = [
                'labels' => [],
                'orders' => [],
                'revenue' => [],
                'customers' => [],
            ];

            foreach ($dailyTrends as $trend) {
                $trends['labels'][] = Carbon::parse($trend->date)->format('M j');
                $trends['orders'][] = (int) $trend->orders;
                $trends['revenue'][] = (float) $trend->revenue;
                $trends['customers'][] = (int) $trend->customers;
            }

            // Calculate growth rates
            $totalOrders = array_sum($trends['orders']);
            $totalRevenue = array_sum($trends['revenue']);
            $avgDailyOrders = $totalOrders / $days;
            $avgDailyRevenue = $totalRevenue / $days;

            return [
                'trends' => $trends,
                'summary' => [
                    'total_orders' => $totalOrders,
                    'total_revenue' => $totalRevenue,
                    'avg_daily_orders' => round($avgDailyOrders, 2),
                    'avg_daily_revenue' => round($avgDailyRevenue, 2),
                    'period_days' => $days,
                ],
                'cache_key' => $cacheKey,
            ];
        });
    }

    /**
     * Clear all analytics caches
     */
    public function clearCache(): void
    {
        $patterns = [
            'analytics:real_time_sales_metrics*',
            'analytics:conversion_metrics*',
            'analytics:performance_indicators*',
            'analytics:trend_analysis*',
        ];

        foreach ($patterns as $pattern) {
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        }
    }

    /**
     * Preload analytics data for faster access
     */
    public function preloadAnalytics(): array
    {
        return [
            'sales_metrics' => $this->getRealTimeSalesMetrics(),
            'conversion_metrics' => $this->getConversionMetrics(),
            'performance_indicators' => $this->getPerformanceIndicators(),
            'trend_analysis' => $this->getTrendAnalysis(),
        ];
    }
}
