<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OptimizedAnalyticsService;
use App\Services\CacheOptimizationService;
use App\Services\QueueOptimizationService;
use App\Services\SystemHealthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class PerformanceController extends Controller
{
    protected OptimizedAnalyticsService $analyticsService;
    protected CacheOptimizationService $cacheService;
    protected QueueOptimizationService $queueService;
    protected SystemHealthService $healthService;

    public function __construct(
        OptimizedAnalyticsService $analyticsService,
        CacheOptimizationService $cacheService,
        QueueOptimizationService $queueService,
        SystemHealthService $healthService
    ) {
        $this->analyticsService = $analyticsService;
        $this->cacheService = $cacheService;
        $this->queueService = $queueService;
        $this->healthService = $healthService;
    }

    /**
     * Display performance monitoring dashboard
     */
    public function index()
    {
        $performanceMetrics = $this->getPerformanceMetrics();
        
        return view('admin.performance.index', compact('performanceMetrics'));
    }

    /**
     * Get comprehensive performance metrics
     */
    public function getMetrics(): JsonResponse
    {
        try {
            $metrics = $this->getPerformanceMetrics();
            
            return response()->json([
                'success' => true,
                'data' => $metrics,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get database performance metrics
     */
    public function getDatabaseMetrics(): JsonResponse
    {
        try {
            $metrics = [
                'connections' => $this->getDatabaseConnections(),
                'slow_queries' => $this->getSlowQueries(),
                'table_sizes' => $this->getTableSizes(),
                'index_usage' => $this->getIndexUsage(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $metrics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get cache performance metrics
     */
    public function getCacheMetrics(): JsonResponse
    {
        try {
            $stats = $this->cacheService->getCacheStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get queue performance metrics
     */
    public function getQueueMetrics(): JsonResponse
    {
        try {
            $stats = $this->queueService->getQueueStats();
            $health = $this->queueService->monitorQueueHealth();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'health' => $health,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Optimize cache performance
     */
    public function optimizeCache(): JsonResponse
    {
        try {
            $result = $this->cacheService->optimizeCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache optimization completed',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Optimize queue performance
     */
    public function optimizeQueues(): JsonResponse
    {
        try {
            $result = $this->queueService->optimizeQueues();
            
            return response()->json([
                'success' => true,
                'message' => 'Queue optimization analysis completed',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear all application caches
     */
    public function clearCache(): JsonResponse
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');
            
            // Clear config cache
            Artisan::call('config:clear');
            
            // Clear route cache
            Artisan::call('route:clear');
            
            // Clear view cache
            Artisan::call('view:clear');
            
            // Clear custom analytics cache
            $this->analyticsService->clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Warm up critical caches
     */
    public function warmupCache(): JsonResponse
    {
        try {
            $this->cacheService->warmupCaches();
            $this->cacheService->preloadCriticalCaches();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache warmup completed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run database migrations
     */
    public function runMigrations(): JsonResponse
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Database migrations completed',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get comprehensive performance metrics
     */
    protected function getPerformanceMetrics(): array
    {
        return [
            'database' => [
                'connections' => $this->getDatabaseConnections(),
                'slow_queries_count' => count($this->getSlowQueries()),
                'total_tables' => count($this->getTableSizes()),
            ],
            'cache' => $this->cacheService->getCacheStats(),
            'queue' => $this->queueService->getQueueStats(),
            'memory' => [
                'usage' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit'),
            ],
            'system' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'uptime' => $this->getSystemUptime(),
                'load_average' => $this->getLoadAverage(),
            ],
        ];
    }

    /**
     * Get database connection information
     */
    protected function getDatabaseConnections(): array
    {
        try {
            return [
                'active' => DB::connection()->getPdo() ? 1 : 0,
                'driver' => DB::connection()->getDriverName(),
                'database' => DB::connection()->getDatabaseName(),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get slow queries (mock implementation)
     */
    protected function getSlowQueries(): array
    {
        // This would typically integrate with MySQL slow query log
        // For now, return empty array
        return [];
    }

    /**
     * Get table sizes
     */
    protected function getTableSizes(): array
    {
        try {
            if (DB::connection()->getDriverName() === 'mysql') {
                return DB::select("
                    SELECT 
                        table_name as 'table',
                        ROUND(((data_length + index_length) / 1024 / 1024), 2) as 'size_mb'
                    FROM information_schema.TABLES 
                    WHERE table_schema = DATABASE()
                    ORDER BY (data_length + index_length) DESC
                    LIMIT 10
                ");
            }
            
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get index usage statistics
     */
    protected function getIndexUsage(): array
    {
        try {
            if (DB::connection()->getDriverName() === 'mysql') {
                return DB::select("
                    SELECT 
                        TABLE_NAME as 'table',
                        INDEX_NAME as 'index',
                        CARDINALITY as cardinality
                    FROM information_schema.STATISTICS 
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND CARDINALITY > 0
                    ORDER BY CARDINALITY DESC
                    LIMIT 20
                ");
            }
            
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get system uptime
     */
    protected function getSystemUptime(): string
    {
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                $uptime = file_get_contents('/proc/uptime');
                $seconds = (int) explode(' ', $uptime)[0];
                return gmdate('H:i:s', $seconds);
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get system load average
     */
    protected function getLoadAverage(): array
    {
        try {
            if (function_exists('sys_getloadavg')) {
                $load = sys_getloadavg();
                return [
                    '1min' => round($load[0], 2),
                    '5min' => round($load[1], 2),
                    '15min' => round($load[2], 2),
                ];
            }
            return ['1min' => 0, '5min' => 0, '15min' => 0];
        } catch (\Exception $e) {
            return ['1min' => 0, '5min' => 0, '15min' => 0];
        }
    }

    /**
     * Get system health status
     */
    public function getHealthStatus(): JsonResponse
    {
        try {
            $health = $this->healthService->checkSystemHealth();
            
            return response()->json([
                'success' => true,
                'data' => $health
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get health status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quick health summary
     */
    public function getHealthSummary(): JsonResponse
    {
        try {
            $summary = $this->healthService->getHealthSummary();
            
            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get health summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
