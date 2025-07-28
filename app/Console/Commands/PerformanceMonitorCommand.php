<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OptimizedAnalyticsService;
use App\Services\CacheOptimizationService;
use App\Services\QueueOptimizationService;
use Illuminate\Support\Facades\Log;

class PerformanceMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'performance:monitor 
                            {--optimize : Run optimization tasks}
                            {--cache : Optimize cache only}
                            {--queue : Optimize queue only}
                            {--warmup : Warmup caches}
                            {--stats : Show performance statistics}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor and optimize application performance';

    protected OptimizedAnalyticsService $analyticsService;
    protected CacheOptimizationService $cacheService;
    protected QueueOptimizationService $queueService;

    /**
     * Create a new command instance.
     */
    public function __construct(
        OptimizedAnalyticsService $analyticsService,
        CacheOptimizationService $cacheService,
        QueueOptimizationService $queueService
    ) {
        parent::__construct();
        $this->analyticsService = $analyticsService;
        $this->cacheService = $cacheService;
        $this->queueService = $queueService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Performance Monitoring Tool');
        $this->info('================================');

        if ($this->option('stats')) {
            $this->showStats();
            return 0;
        }

        if ($this->option('cache')) {
            $this->optimizeCache();
            return 0;
        }

        if ($this->option('queue')) {
            $this->optimizeQueue();
            return 0;
        }

        if ($this->option('warmup')) {
            $this->warmupCaches();
            return 0;
        }

        if ($this->option('optimize')) {
            $this->runFullOptimization();
            return 0;
        }

        // Default: Show current status
        $this->showCurrentStatus();
        return 0;
    }

    /**
     * Show performance statistics
     */
    protected function showStats(): void
    {
        $this->info('📊 Performance Statistics');
        $this->line('');

        // Cache stats
        $cacheStats = $this->cacheService->getCacheStats();
        $this->table(['Metric', 'Value'], [
            ['Cache Memory Used', $cacheStats['memory_used'] ?? 'N/A'],
            ['Cache Hit Rate', ($cacheStats['hit_rate'] ?? 0) . '%'],
            ['Total Keys', $cacheStats['total_keys'] ?? 0],
        ]);

        // Queue stats
        $queueStats = $this->queueService->getQueueStats();
        $this->line('');
        $this->info('📋 Queue Statistics');
        
        $queueData = [];
        foreach ($queueStats as $queue => $stats) {
            if (is_array($stats) && isset($stats['waiting'])) {
                $queueData[] = [
                    ucfirst(str_replace('_', ' ', $queue)),
                    $stats['waiting'] ?? 0,
                    $stats['delayed'] ?? 0,
                    $stats['reserved'] ?? 0,
                ];
            }
        }
        
        if (!empty($queueData)) {
            $this->table(['Queue', 'Waiting', 'Delayed', 'Reserved'], $queueData);
        }

        // Memory usage
        $this->line('');
        $this->info('💾 Memory Usage');
        $this->table(['Metric', 'Value'], [
            ['Current Usage', $this->formatBytes(memory_get_usage(true))],
            ['Peak Usage', $this->formatBytes(memory_get_peak_usage(true))],
            ['Memory Limit', ini_get('memory_limit')],
        ]);
    }

    /**
     * Show current performance status
     */
    protected function showCurrentStatus(): void
    {
        $this->info('📈 Current Performance Status');
        $this->line('');

        // Check cache health
        $cacheStats = $this->cacheService->getCacheStats();
        $hitRate = $cacheStats['hit_rate'] ?? 0;
        
        if ($hitRate >= 80) {
            $this->info("✅ Cache: Excellent ({$hitRate}% hit rate)");
        } elseif ($hitRate >= 60) {
            $this->warn("⚠️  Cache: Good ({$hitRate}% hit rate)");
        } else {
            $this->error("❌ Cache: Poor ({$hitRate}% hit rate) - Consider optimization");
        }

        // Check queue health
        $queueHealth = $this->queueService->monitorQueueHealth();
        $status = $queueHealth['status'] ?? 'unknown';
        
        if ($status === 'healthy') {
            $this->info('✅ Queues: Healthy');
        } else {
            $alertCount = count($queueHealth['alerts'] ?? []);
            $this->warn("⚠️  Queues: {$alertCount} issues detected");
            
            foreach ($queueHealth['alerts'] ?? [] as $alert) {
                $icon = $alert['level'] === 'critical' ? '🔴' : '🟡';
                $this->line("   {$icon} {$alert['message']}");
            }
        }

        // Memory status
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        
        if ($memoryLimit > 0) {
            $percentage = ($memoryUsage / $memoryLimit) * 100;
            
            if ($percentage < 70) {
                $this->info("✅ Memory: Normal ({$this->formatBytes($memoryUsage)})");
            } elseif ($percentage < 90) {
                $this->warn("⚠️  Memory: High usage ({$this->formatBytes($memoryUsage)})");
            } else {
                $this->error("❌ Memory: Critical usage ({$this->formatBytes($memoryUsage)})");
            }
        } else {
            $this->info("✅ Memory: {$this->formatBytes($memoryUsage)} used");
        }

        $this->line('');
        $this->info('Use --optimize to run full optimization');
        $this->info('Use --stats to see detailed statistics');
    }

    /**
     * Optimize cache performance
     */
    protected function optimizeCache(): void
    {
        $this->info('🔧 Optimizing Cache...');
        
        $this->withProgressBar(['Analyzing cache', 'Cleaning expired keys', 'Optimizing memory'], function ($task) {
            usleep(500000); // Simulate work
            
            if ($task === 'Optimizing memory') {
                $result = $this->cacheService->optimizeCache();
                
                if (isset($result['keys_cleaned'])) {
                    Log::info('Cache optimization completed via command', $result);
                }
            }
        });
        
        $this->line('');
        $this->info('✅ Cache optimization completed');
    }

    /**
     * Optimize queue performance
     */
    protected function optimizeQueue(): void
    {
        $this->info('🔧 Optimizing Queues...');
        
        $optimization = $this->queueService->optimizeQueues();
        
        if (isset($optimization['optimizations'])) {
            $optimizations = $optimization['optimizations'];
            
            if (empty($optimizations)) {
                $this->info('✅ Queues are already optimized');
            } else {
                $this->warn('⚠️  Found optimization opportunities:');
                
                foreach ($optimizations as $opt) {
                    $this->line("   • {$opt['recommendation']}");
                }
            }
        }
        
        // Clean up old jobs
        $this->info('🧹 Cleaning up old jobs...');
        $cleanup = $this->queueService->cleanupQueues();
        
        if (isset($cleanup['old_failed_jobs']) && $cleanup['old_failed_jobs'] > 0) {
            $this->info("   Removed {$cleanup['old_failed_jobs']} old failed jobs");
        }
        
        $this->info('✅ Queue optimization completed');
    }

    /**
     * Warmup critical caches
     */
    protected function warmupCaches(): void
    {
        $this->info('🔥 Warming up caches...');
        
        $this->withProgressBar(['Loading analytics', 'Preloading critical data', 'Finalizing'], function ($task) {
            if ($task === 'Loading analytics') {
                $this->cacheService->warmupCaches();
            } elseif ($task === 'Preloading critical data') {
                $this->cacheService->preloadCriticalCaches();
            }
            
            usleep(800000); // Simulate work
        });
        
        $this->line('');
        $this->info('✅ Cache warmup completed');
    }

    /**
     * Run full optimization
     */
    protected function runFullOptimization(): void
    {
        $this->info('🚀 Running Full Performance Optimization');
        $this->line('');

        $this->optimizeCache();
        $this->line('');
        
        $this->optimizeQueue();
        $this->line('');
        
        $this->warmupCaches();
        $this->line('');
        
        $this->info('🎉 Full optimization completed successfully!');
        
        // Show final status
        $this->line('');
        $this->showCurrentStatus();
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Parse memory limit string to bytes
     */
    protected function parseMemoryLimit(string $limit): int
    {
        if (!$limit || $limit === '-1') return 0;
        
        $value = (int) $limit;
        $unit = strtoupper(substr($limit, -1));
        
        return match($unit) {
            'G' => $value * 1024 * 1024 * 1024,
            'M' => $value * 1024 * 1024,
            'K' => $value * 1024,
            default => $value,
        };
    }
}
