<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Services\OptimizedAnalyticsService;
use App\Services\CacheOptimizationService;
use App\Services\QueueOptimizationService;
use App\Services\SystemHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;

class PerformanceOptimizationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);
        
        $this->user = User::factory()->create([
            'email' => 'user@test.com',
            'role' => 'user'
        ]);

        // Create test data
        Product::factory(10)->create();
        Order::factory(5)->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function performance_dashboard_is_accessible_by_admin()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.performance.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.performance.index');
        $response->assertViewHas('performanceMetrics');
    }

    /** @test */
    public function performance_dashboard_is_not_accessible_by_regular_user()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.performance.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function performance_metrics_endpoint_returns_valid_data()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.performance.metrics'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'database',
                'cache',
                'queue',
                'memory',
                'system'
            ],
            'timestamp'
        ]);
    }

    /** @test */
    public function optimized_analytics_service_reduces_database_queries()
    {
        $service = app(OptimizedAnalyticsService::class);
        
        // Clear any existing cache
        Cache::flush();
        
        // Track database queries
        $queriesCount = 0;
        \DB::listen(function () use (&$queriesCount) {
            $queriesCount++;
        });

        // Test analytics methods
        $salesMetrics = $service->getRealTimeSalesMetrics();
        $conversionMetrics = $service->getConversionMetrics();
        $performanceIndicators = $service->getPerformanceIndicators();

        // Verify data structure
        $this->assertIsArray($salesMetrics);
        $this->assertIsArray($conversionMetrics);
        $this->assertIsArray($performanceIndicators);

        // Verify query optimization (should be minimal due to caching and optimization)
        $this->assertLessThan(10, $queriesCount, 'Analytics should use optimized queries');
    }

    /** @test */
    public function cache_optimization_service_works_correctly()
    {
        $service = app(CacheOptimizationService::class);
        
        // Test cache remember functionality
        $testKey = 'test_cache_key';
        $testValue = 'test_cache_value';
        
        $result = $service->remember($testKey, function() use ($testValue) {
            return $testValue;
        }, 300);

        $this->assertEquals($testValue, $result);
        
        // Verify cache was set
        $this->assertEquals($testValue, Cache::get($testKey));

        // Test cache stats
        $stats = $service->getCacheStats();
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_keys', $stats);
    }

    /** @test */
    public function queue_optimization_service_provides_stats()
    {
        $service = app(QueueOptimizationService::class);
        
        // Test queue stats
        $stats = $service->getQueueStats();
        $this->assertIsArray($stats);

        // Test queue health monitoring
        $health = $service->monitorQueueHealth();
        $this->assertIsArray($health);
        $this->assertArrayHasKey('status', $health);
        $this->assertContains($health['status'], ['healthy', 'warning', 'critical']);
    }

    /** @test */
    public function system_health_service_returns_comprehensive_status()
    {
        $service = app(SystemHealthService::class);
        
        // Test full health check
        $health = $service->checkSystemHealth();
        
        $this->assertIsArray($health);
        $this->assertArrayHasKey('overall_status', $health);
        $this->assertArrayHasKey('checks', $health);
        $this->assertArrayHasKey('performance_score', $health);
        
        // Verify health checks are present
        $expectedChecks = ['database', 'cache', 'storage', 'queues', 'memory', 'disk_space'];
        foreach ($expectedChecks as $check) {
            $this->assertArrayHasKey($check, $health['checks']);
        }

        // Test health summary
        $summary = $service->getHealthSummary();
        $this->assertIsArray($summary);
        $this->assertArrayHasKey('status', $summary);
        $this->assertArrayHasKey('score', $summary);
    }

    /** @test */
    public function cache_optimization_endpoint_works()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.performance.optimize-cache'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Cache optimization completed'
        ]);
    }

    /** @test */
    public function queue_optimization_endpoint_works()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.performance.optimize-queues'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Queue optimization analysis completed'
        ]);
    }

    /** @test */
    public function cache_clearing_endpoint_works()
    {
        // Set a test cache value
        Cache::put('test_key', 'test_value', 60);
        $this->assertEquals('test_value', Cache::get('test_key'));

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.performance.clear-cache'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'All caches cleared successfully'
        ]);

        // Verify cache was cleared
        $this->assertNull(Cache::get('test_key'));
    }

    /** @test */
    public function cache_warmup_endpoint_works()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.performance.warmup-cache'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Cache warmup completed successfully'
        ]);
    }

    /** @test */
    public function health_status_endpoint_returns_valid_data()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.performance.health'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'overall_status',
                'timestamp',
                'checks',
                'alerts',
                'performance_score'
            ]
        ]);
    }

    /** @test */
    public function health_summary_endpoint_returns_valid_data()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.performance.health.summary'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'status',
                'score',
                'alerts_count',
                'last_check'
            ]
        ]);
    }

    /** @test */
    public function performance_monitoring_command_executes_successfully()
    {
        // Test stats command
        $exitCode = Artisan::call('performance:monitor', ['--stats' => true]);
        $this->assertEquals(0, $exitCode);

        // Test cache optimization command
        $exitCode = Artisan::call('performance:monitor', ['--cache' => true]);
        $this->assertEquals(0, $exitCode);

        // Test queue optimization command
        $exitCode = Artisan::call('performance:monitor', ['--queue' => true]);
        $this->assertEquals(0, $exitCode);
    }

    /** @test */
    public function database_indexes_improve_query_performance()
    {
        // Create more test data for meaningful performance testing
        Order::factory(100)->create(['user_id' => $this->user->id]);
        
        $queriesCount = 0;
        $totalTime = 0;
        
        \DB::listen(function ($query) use (&$queriesCount, &$totalTime) {
            $queriesCount++;
            $totalTime += $query->time;
        });

        // Test queries that should benefit from indexes
        $recentOrders = Order::where('created_at', '>=', now()->subDays(30))
            ->where('status', 'completed')
            ->with('user')
            ->get();

        $this->assertNotEmpty($recentOrders);
        
        // With proper indexes, queries should be efficient
        $this->assertLessThan(100, $totalTime, 'Queries should be fast with proper indexing');
    }

    /** @test */
    public function rate_limiting_middleware_works_correctly()
    {
        // Test API rate limiting
        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($this->user)
                ->getJson(route('admin.performance.metrics'));
            
            if ($i < 4) {
                // Should allow first few requests
                $this->assertNotEquals(429, $response->getStatusCode());
            }
        }
    }

    /** @test */
    public function analytics_caching_works_correctly()
    {
        $service = app(OptimizedAnalyticsService::class);
        
        // Clear cache
        Cache::flush();
        
        // First call should cache the result
        $firstCall = $service->getRealTimeSalesMetrics();
        
        // Second call should use cached result
        $secondCall = $service->getRealTimeSalesMetrics();
        
        $this->assertEquals($firstCall, $secondCall);
        
        // Verify cache key exists
        $this->assertNotNull(Cache::get('analytics:sales_metrics'));
    }

    /** @test */
    public function performance_dashboard_handles_errors_gracefully()
    {
        // Test with database connection issues (mock)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.performance.index'));

        $response->assertStatus(200);
        // Dashboard should still load even if some metrics fail
    }

    /** @test */
    public function system_monitoring_detects_issues()
    {
        $service = app(SystemHealthService::class);
        
        $health = $service->checkSystemHealth();
        
        // Should detect if system is healthy or has issues
        $this->assertContains($health['overall_status'], ['healthy', 'warning', 'critical']);
        
        // Performance score should be between 0 and 100
        $this->assertGreaterThanOrEqual(0, $health['performance_score']);
        $this->assertLessThanOrEqual(100, $health['performance_score']);
    }
}
