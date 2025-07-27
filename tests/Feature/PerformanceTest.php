<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Services\OptimizedAnalyticsService;
use App\Services\CacheOptimizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;

class PerformanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected $performanceThresholds = [
        'database_query_time' => 100, // milliseconds
        'cache_hit_rate' => 80, // percentage
        'page_load_time' => 2000, // milliseconds
        'memory_usage' => 128 * 1024 * 1024, // 128MB in bytes
        'api_response_time' => 500, // milliseconds
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        
        // Create test data for performance testing
        $this->createPerformanceTestData();
    }

    /**
     * Create substantial test data for performance testing
     */
    protected function createPerformanceTestData(): void
    {
        // Create users
        User::factory(100)->create();
        
        // Create products
        Product::factory(200)->create();
        
        // Create orders with relationships
        Order::factory(500)->create();
    }

    /** @test */
    public function database_query_performance_is_acceptable()
    {
        // Test database query performance
        $startTime = microtime(true);
        
        // Complex query that should be optimized
        $orders = Order::with(['user'])
            ->where('created_at', '>=', now()->subDays(30))
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        $queryTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        $this->assertNotEmpty($orders);
        $this->assertLessThan(
            $this->performanceThresholds['database_query_time'],
            $queryTime,
            "Database query took {$queryTime}ms, exceeding threshold of {$this->performanceThresholds['database_query_time']}ms"
        );
    }

    /** @test */
    public function analytics_service_performance_is_optimized()
    {
        $service = app(OptimizedAnalyticsService::class);
        
        // Clear cache to test actual query performance
        Cache::flush();
        
        // Track database queries
        $queriesCount = 0;
        $totalQueryTime = 0;
        
        DB::listen(function ($query) use (&$queriesCount, &$totalQueryTime) {
            $queriesCount++;
            $totalQueryTime += $query->time;
        });
        
        $startTime = microtime(true);
        
        // Test all analytics methods
        $salesMetrics = $service->getRealTimeSalesMetrics();
        $conversionMetrics = $service->getConversionMetrics();
        $performanceIndicators = $service->getPerformanceIndicators();
        $trendAnalysis = $service->getTrendAnalysis();
        
        $totalTime = (microtime(true) - $startTime) * 1000;
        
        // Performance assertions
        $this->assertLessThan(10, $queriesCount, 'Analytics should use minimal database queries');
        $this->assertLessThan(500, $totalTime, 'Analytics operations should complete quickly');
        $this->assertLessThan(200, $totalQueryTime, 'Database queries should be fast');
        
        // Verify data structure
        $this->assertIsArray($salesMetrics);
        $this->assertIsArray($conversionMetrics);
        $this->assertIsArray($performanceIndicators);
        $this->assertIsArray($trendAnalysis);
    }

    /** @test */
    public function cache_performance_meets_requirements()
    {
        $cacheService = app(CacheOptimizationService::class);
        
        // Test cache write performance
        $startTime = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $cacheService->remember("test_key_{$i}", function () use ($i) {
                return "test_value_{$i}";
            }, 300);
        }
        
        $cacheWriteTime = (microtime(true) - $startTime) * 1000;
        
        // Test cache read performance
        $startTime = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $value = Cache::get("test_key_{$i}");
            $this->assertEquals("test_value_{$i}", $value);
        }
        
        $cacheReadTime = (microtime(true) - $startTime) * 1000;
        
        // Performance assertions
        $this->assertLessThan(500, $cacheWriteTime, 'Cache write operations should be fast');
        $this->assertLessThan(100, $cacheReadTime, 'Cache read operations should be very fast');
        
        // Check cache hit rate
        $stats = $cacheService->getCacheStats();
        if (isset($stats['hit_rate'])) {
            $this->assertGreaterThan(
                $this->performanceThresholds['cache_hit_rate'],
                $stats['hit_rate'],
                'Cache hit rate should meet performance requirements'
            );
        }
    }

    /** @test */
    public function admin_dashboard_loads_within_acceptable_time()
    {
        $startTime = microtime(true);
        
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
        
        $loadTime = (microtime(true) - $startTime) * 1000;
        
        $response->assertStatus(200);
        $this->assertLessThan(
            $this->performanceThresholds['page_load_time'],
            $loadTime,
            "Dashboard loaded in {$loadTime}ms, exceeding threshold of {$this->performanceThresholds['page_load_time']}ms"
        );
    }

    /** @test */
    public function performance_dashboard_loads_efficiently()
    {
        $startTime = microtime(true);
        
        $response = $this->actingAs($this->admin)
            ->get(route('admin.performance.index'));
        
        $loadTime = (microtime(true) - $startTime) * 1000;
        
        $response->assertStatus(200);
        $this->assertLessThan(
            $this->performanceThresholds['page_load_time'],
            $loadTime,
            "Performance dashboard loaded in {$loadTime}ms, exceeding threshold"
        );
    }

    /** @test */
    public function api_endpoints_respond_quickly()
    {
        $endpoints = [
            ['method' => 'GET', 'uri' => route('admin.performance.metrics')],
            ['method' => 'GET', 'uri' => route('admin.performance.health.summary')],
            ['method' => 'GET', 'uri' => route('admin.performance.cache')],
            ['method' => 'GET', 'uri' => route('admin.performance.queue')],
        ];
        
        foreach ($endpoints as $endpoint) {
            $startTime = microtime(true);
            
            $response = $this->actingAs($this->admin)
                ->json($endpoint['method'], $endpoint['uri']);
            
            $responseTime = (microtime(true) - $startTime) * 1000;
            
            $response->assertStatus(200);
            $this->assertLessThan(
                $this->performanceThresholds['api_response_time'],
                $responseTime,
                "API endpoint {$endpoint['uri']} responded in {$responseTime}ms, exceeding threshold"
            );
        }
    }

    /** @test */
    public function memory_usage_is_within_limits()
    {
        $initialMemory = memory_get_usage(true);
        
        // Perform memory-intensive operations
        $service = app(OptimizedAnalyticsService::class);
        $metrics = $service->getRealTimeSalesMetrics();
        $conversion = $service->getConversionMetrics();
        $performance = $service->getPerformanceIndicators();
        
        // Load dashboard
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
        
        $finalMemory = memory_get_usage(true);
        $memoryUsed = $finalMemory - $initialMemory;
        
        $this->assertLessThan(
            $this->performanceThresholds['memory_usage'],
            $memoryUsed,
            "Memory usage of {$memoryUsed} bytes exceeds threshold"
        );
    }

    /** @test */
    public function bulk_operations_perform_efficiently()
    {
        // Test bulk order processing
        $orders = Order::factory(50)->create(['status' => 'pending']);
        
        $startTime = microtime(true);
        
        // Bulk update orders
        Order::whereIn('id', $orders->pluck('id'))
            ->update(['status' => 'processing']);
        
        $bulkUpdateTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(200, $bulkUpdateTime, 'Bulk operations should be fast');
        
        // Verify updates
        $updatedCount = Order::whereIn('id', $orders->pluck('id'))
            ->where('status', 'processing')
            ->count();
        
        $this->assertEquals(50, $updatedCount);
    }

    /** @test */
    public function concurrent_user_simulation_performance()
    {
        // Simulate multiple concurrent users
        $users = User::factory(10)->create();
        $startTime = microtime(true);
        
        foreach ($users as $user) {
            // Simulate user activities
            $this->actingAs($user)
                ->get('/dashboard');
            
            // Create some activity
            Order::factory()->create(['user_id' => $user->id]);
        }
        
        $totalTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(5000, $totalTime, 'Concurrent user operations should complete within 5 seconds');
    }

    /** @test */
    public function queue_processing_performance_is_adequate()
    {
        Queue::fake();
        
        $startTime = microtime(true);
        
        // Dispatch multiple jobs
        for ($i = 0; $i < 50; $i++) {
            \App\Jobs\TestPerformanceJob::dispatch("test_data_{$i}");
        }
        
        $dispatchTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(100, $dispatchTime, 'Job dispatching should be fast');
        
        // Verify jobs were queued
        Queue::assertPushed(\App\Jobs\TestPerformanceJob::class, 50);
    }

    /** @test */
    public function database_connection_pooling_works_efficiently()
    {
        // Test multiple database connections
        $connections = [];
        $startTime = microtime(true);
        
        for ($i = 0; $i < 10; $i++) {
            $connection = DB::connection();
            $connections[] = $connection;
            
            // Simple query to test connection
            $result = $connection->select('SELECT 1 as test');
            $this->assertNotEmpty($result);
        }
        
        $connectionTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(500, $connectionTime, 'Database connections should be established quickly');
    }

    /** @test */
    public function cache_invalidation_performance_is_acceptable()
    {
        $cacheService = app(CacheOptimizationService::class);
        
        // Set up cache data
        for ($i = 0; $i < 100; $i++) {
            Cache::put("test_cache_{$i}", "value_{$i}", 300);
        }
        
        $startTime = microtime(true);
        
        // Test cache clearing
        Cache::flush();
        
        $clearTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(200, $clearTime, 'Cache clearing should be fast');
        
        // Verify cache was cleared
        for ($i = 0; $i < 100; $i++) {
            $this->assertNull(Cache::get("test_cache_{$i}"));
        }
    }

    /** @test */
    public function session_management_performance_is_optimal()
    {
        $startTime = microtime(true);
        
        // Create multiple sessions
        for ($i = 0; $i < 20; $i++) {
            $user = User::factory()->create();
            $this->actingAs($user);
            
            // Simulate session activity
            session(['test_key' => "test_value_{$i}"]);
        }
        
        $sessionTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(1000, $sessionTime, 'Session operations should be fast');
    }

    /** @test */
    public function real_time_event_broadcasting_performance()
    {
        $startTime = microtime(true);
        
        // Test event broadcasting performance
        for ($i = 0; $i < 10; $i++) {
            $order = Order::factory()->create();
            event(new \App\Events\NewOrderEvent($order, $i + 1));
        }
        
        $broadcastTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(500, $broadcastTime, 'Event broadcasting should be fast');
    }

    /** @test */
    public function performance_monitoring_overhead_is_minimal()
    {
        // Test the performance impact of monitoring itself
        $startTime = microtime(true);
        
        $healthService = app(\App\Services\SystemHealthService::class);
        $health = $healthService->checkSystemHealth();
        
        $monitoringTime = (microtime(true) - $startTime) * 1000;
        
        $this->assertLessThan(1000, $monitoringTime, 'Performance monitoring should have minimal overhead');
        $this->assertIsArray($health);
        $this->assertArrayHasKey('overall_status', $health);
    }

    /** @test */
    public function load_testing_simulation()
    {
        // Simulate high load conditions
        $startTime = microtime(true);
        $requestCount = 50;
        
        for ($i = 0; $i < $requestCount; $i++) {
            $user = $i % 10 == 0 ? $this->admin : User::inRandomOrder()->first();
            
            $response = $this->actingAs($user)
                ->get('/dashboard');
            
            $response->assertStatus(200);
            
            // Simulate some processing
            usleep(10000); // 10ms delay
        }
        
        $totalTime = (microtime(true) - $startTime) * 1000;
        $averageTime = $totalTime / $requestCount;
        
        $this->assertLessThan(100, $averageTime, "Average request time should be under 100ms, got {$averageTime}ms");
    }

    /**
     * Generate performance report
     */
    public function generate_performance_report()
    {
        $report = [
            'timestamp' => now()->toISOString(),
            'test_environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'performance_thresholds' => $this->performanceThresholds,
            'system_info' => [
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
            ]
        ];
        
        file_put_contents(
            storage_path('logs/performance_report.json'),
            json_encode($report, JSON_PRETTY_PRINT)
        );
        
        return $report;
    }
}
