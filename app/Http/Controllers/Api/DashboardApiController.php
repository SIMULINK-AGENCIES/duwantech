<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get real-time metrics for dashboard widgets.
     */
    public function getMetrics(): JsonResponse
    {
        try {
            $metrics = $this->dashboardService->getRealTimeMetrics();
            
            return response()->json([
                'success' => true,
                'data' => $metrics,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch metrics',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

    /**
     * Get chart data for specific widget.
     */
    public function getChartData(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:sales,orders,users,revenue,traffic',
            'period' => 'nullable|in:24h,7d,30d,12m',
        ]);

        try {
            $chartData = $this->dashboardService->getChartData(
                $request->input('type'),
                $request->input('period', '24h')
            );
            
            return response()->json([
                'success' => true,
                'data' => $chartData,
                'type' => $request->input('type'),
                'period' => $request->input('period', '24h'),
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

    /**
     * Get quick stats for mini widgets.
     */
    public function getQuickStats(): JsonResponse
    {
        try {
            $metrics = $this->dashboardService->getRealTimeMetrics();
            
            $quickStats = [
                'orders' => [
                    'total' => $metrics['orders']['today'],
                    'change' => $metrics['orders']['daily_change'],
                    'status' => $metrics['orders']['daily_change'] >= 0 ? 'up' : 'down',
                ],
                'revenue' => [
                    'total' => $metrics['sales']['today'],
                    'change' => $metrics['sales']['daily_change'],
                    'status' => $metrics['sales']['daily_change'] >= 0 ? 'up' : 'down',
                ],
                'users' => [
                    'total' => $metrics['users']['new_today'],
                    'change' => $metrics['users']['daily_change'],
                    'status' => $metrics['users']['daily_change'] >= 0 ? 'up' : 'down',
                ],
                'alerts' => [
                    'total' => $metrics['alerts']['unread_count'],
                    'critical' => $metrics['alerts']['critical_count'],
                    'status' => $metrics['alerts']['critical_count'] > 0 ? 'critical' : 'normal',
                ],
            ];
            
            return response()->json([
                'success' => true,
                'data' => $quickStats,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quick stats',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

    /**
     * Get system health status.
     */
    public function getSystemHealth(): JsonResponse
    {
        try {
            $metrics = $this->dashboardService->getRealTimeMetrics();
            
            $healthData = [
                'overall_status' => $metrics['overview']['system_health'],
                'performance' => $metrics['performance'],
                'alerts' => $metrics['alerts'],
                'timestamp' => now()->toISOString(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $healthData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch system health',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

    /**
     * Get live activity feed.
     */
    public function activityFeed(Request $request)
    {
        try {
            $limit = min($request->get('limit', 20), 50);
            
            $activities = $this->dashboardService->getActivityFeed($limit);
            
            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch activity feed'
            ], 500);
        }
    }
}
