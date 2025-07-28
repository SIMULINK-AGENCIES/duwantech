<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CacheOptimizationService
{
    protected array $cacheConfig = [
        'analytics' => ['ttl' => 300, 'tags' => ['analytics']],
        'dashboard' => ['ttl' => 180, 'tags' => ['dashboard']],
        'products' => ['ttl' => 3600, 'tags' => ['products']],
        'users' => ['ttl' => 1800, 'tags' => ['users']],
        'sessions' => ['ttl' => 600, 'tags' => ['sessions']],
        'real_time' => ['ttl' => 60, 'tags' => ['real_time']],
    ];

    /**
     * Get cached data with automatic refresh and fallback
     */
    public function remember(string $key, callable $callback, string $type = 'default', ?int $ttl = null): mixed
    {
        $config = $this->cacheConfig[$type] ?? ['ttl' => 300, 'tags' => []];
        $finalTtl = $ttl ?? $config['ttl'];
        
        try {
            return Cache::tags($config['tags'])->remember($key, $finalTtl, function () use ($callback, $key) {
                $startTime = microtime(true);
                $result = $callback();
                $executionTime = microtime(true) - $startTime;
                
                // Log slow queries for optimization
                if ($executionTime > 1.0) {
                    Log::warning("Slow cache callback for key: {$key}", [
                        'execution_time' => $executionTime,
                        'key' => $key
                    ]);
                }
                
                return $result;
            });
        } catch (\Exception $e) {
            Log::error("Cache operation failed for key: {$key}", [
                'error' => $e->getMessage(),
                'key' => $key
            ]);
            
            // Fallback to direct execution if cache fails
            return $callback();
        }
    }

    /**
     * Warm up frequently accessed caches
     */
    public function warmupCaches(): void
    {
        $cacheKeys = [
            'analytics:dashboard_overview' => fn() => app(OptimizedAnalyticsService::class)->preloadAnalytics(),
            'products:featured' => fn() => \App\Models\Product::where('featured', true)->limit(10)->get(),
            'users:active_count' => fn() => \App\Models\User::where('last_activity', '>=', Carbon::now()->subHour())->count(),
            'orders:today_count' => fn() => \App\Models\Order::whereDate('created_at', today())->count(),
        ];

        foreach ($cacheKeys as $key => $callback) {
            try {
                $this->remember($key, $callback, 'dashboard');
                Log::info("Cache warmed up: {$key}");
            } catch (\Exception $e) {
                Log::error("Failed to warm up cache: {$key}", ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Clear caches by tags
     */
    public function clearByTag(string $tag): bool
    {
        try {
            Cache::tags([$tag])->flush();
            Log::info("Cache cleared for tag: {$tag}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to clear cache for tag: {$tag}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Clear multiple cache tags
     */
    public function clearMultipleTags(array $tags): bool
    {
        try {
            Cache::tags($tags)->flush();
            Log::info("Cache cleared for tags: " . implode(', ', $tags));
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to clear cache for tags: " . implode(', ', $tags), ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        try {
            $redis = Redis::connection();
            
            $info = $redis->info('memory');
            $keyCount = $redis->dbsize();
            
            return [
                'memory_used' => $info['used_memory_human'] ?? 'N/A',
                'memory_peak' => $info['used_memory_peak_human'] ?? 'N/A',
                'total_keys' => $keyCount,
                'hit_rate' => $this->calculateHitRate(),
                'uptime' => $redis->info('server')['uptime_in_seconds'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error("Failed to get cache stats", ['error' => $e->getMessage()]);
            return [
                'memory_used' => 'Error',
                'memory_peak' => 'Error',
                'total_keys' => 0,
                'hit_rate' => 0,
                'uptime' => 0,
            ];
        }
    }

    /**
     * Calculate cache hit rate
     */
    protected function calculateHitRate(): float
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info('stats');
            
            $hits = (int) ($info['keyspace_hits'] ?? 0);
            $misses = (int) ($info['keyspace_misses'] ?? 0);
            $total = $hits + $misses;
            
            return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Intelligent cache invalidation based on data changes
     */
    public function invalidateRelated(string $model, int $id): void
    {
        $invalidationMap = [
            'Order' => ['analytics', 'dashboard', 'users'],
            'Product' => ['products', 'analytics', 'dashboard'],
            'User' => ['users', 'analytics', 'sessions'],
            'ActivityLog' => ['sessions', 'analytics'],
        ];

        $tags = $invalidationMap[$model] ?? [];
        
        if (!empty($tags)) {
            $this->clearMultipleTags($tags);
            Log::info("Cache invalidated for {$model}:{$id}", ['tags' => $tags]);
        }
    }

    /**
     * Preload critical caches during off-peak hours
     */
    public function preloadCriticalCaches(): void
    {
        $criticalCaches = [
            // Analytics data
            'analytics:sales_metrics' => fn() => app(OptimizedAnalyticsService::class)->getRealTimeSalesMetrics(),
            'analytics:conversion_metrics' => fn() => app(OptimizedAnalyticsService::class)->getConversionMetrics(),
            'analytics:performance_indicators' => fn() => app(OptimizedAnalyticsService::class)->getPerformanceIndicators(),
            
            // Dashboard data
            'dashboard:active_users' => fn() => \App\Models\ActiveSession::where('is_online', true)->count(),
            'dashboard:recent_orders' => fn() => \App\Models\Order::with('user')->latest()->limit(10)->get(),
            
            // Product data
            'products:bestsellers' => fn() => \App\Models\Product::orderBy('sales_count', 'desc')->limit(12)->get(),
            'products:categories' => fn() => \App\Models\Category::withCount('products')->get(),
        ];

        foreach ($criticalCaches as $key => $callback) {
            try {
                // Determine cache type from key
                $type = explode(':', $key)[0];
                
                // Cache with appropriate TTL
                $this->remember($key, $callback, $this->getTtl($type));
                
            } catch (\Exception $e) {
                Log::warning("Failed to preload cache: {$key}", ['error' => $e->getMessage()]);
            }
        }
        
        Log::info('Critical caches preloaded successfully');
    }

    /**
     * Get appropriate TTL for cache type
     */
    protected function getTtl(string $type): int
    {
        return match($type) {
            'analytics' => 900,      // 15 minutes
            'dashboard' => 300,      // 5 minutes
            'products' => 1800,      // 30 minutes
            'users' => 600,          // 10 minutes
            'sessions' => 300,       // 5 minutes
            default => 600,          // 10 minutes default
        };
    }

    /**
     * Clean up expired keys and optimize memory usage
     */
    public function optimizeCache(): array
    {
        try {
            $redis = Redis::connection();
            
            // Get initial stats
            $initialKeys = $redis->dbsize();
            $initialMemory = $redis->info('memory')['used_memory'] ?? 0;
            
            // Clean expired keys
            $redis->eval("
                local cursor = '0'
                local deleted = 0
                repeat
                    local result = redis.call('SCAN', cursor, 'MATCH', '*', 'COUNT', 1000)
                    cursor = result[1]
                    local keys = result[2]
                    for i = 1, #keys do
                        local ttl = redis.call('TTL', keys[i])
                        if ttl == -1 then
                            redis.call('DEL', keys[i])
                            deleted = deleted + 1
                        end
                    end
                until cursor == '0'
                return deleted
            ", 0);
            
            // Get final stats
            $finalKeys = $redis->dbsize();
            $finalMemory = $redis->info('memory')['used_memory'] ?? 0;
            
            $result = [
                'keys_before' => $initialKeys,
                'keys_after' => $finalKeys,
                'keys_cleaned' => $initialKeys - $finalKeys,
                'memory_before' => $initialMemory,
                'memory_after' => $finalMemory,
                'memory_saved' => $initialMemory - $finalMemory,
            ];
            
            Log::info("Cache optimization completed", $result);
            return $result;
            
        } catch (\Exception $e) {
            Log::error("Cache optimization failed", ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
