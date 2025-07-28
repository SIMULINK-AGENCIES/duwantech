<?php

namespace App\Services;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QueueOptimizationService
{
    protected array $queueConfig = [
        'analytics' => [
            'connection' => 'redis',
            'priority' => 'low',
            'retry_after' => 300,
            'max_tries' => 3,
        ],
        'notifications' => [
            'connection' => 'redis',
            'priority' => 'high',
            'retry_after' => 60,
            'max_tries' => 5,
        ],
        'emails' => [
            'connection' => 'redis',
            'priority' => 'medium',
            'retry_after' => 120,
            'max_tries' => 3,
        ],
        'reports' => [
            'connection' => 'redis',
            'priority' => 'low',
            'retry_after' => 600,
            'max_tries' => 2,
        ],
        'default' => [
            'connection' => 'redis',
            'priority' => 'medium',
            'retry_after' => 180,
            'max_tries' => 3,
        ],
    ];

    /**
     * Dispatch job with optimized queue settings
     */
    public function dispatch($job, string $queueType = 'default', ?int $delay = null): void
    {
        $config = $this->queueConfig[$queueType] ?? $this->queueConfig['default'];
        
        $queueJob = $job
            ->onConnection($config['connection'])
            ->onQueue($this->getQueueName($config['priority']))
            ->tries($config['max_tries'])
            ->retryUntil(now()->addSeconds($config['retry_after']));
            
        if ($delay) {
            $queueJob->delay($delay);
        }
        
        $queueJob->dispatch();
        
        Log::debug("Job dispatched to {$queueType} queue", [
            'job' => get_class($job),
            'queue' => $this->getQueueName($config['priority']),
            'delay' => $delay,
        ]);
    }

    /**
     * Get queue names based on priority
     */
    protected function getQueueName(string $priority): string
    {
        return match($priority) {
            'high' => 'high_priority',
            'medium' => 'medium_priority',
            'low' => 'low_priority',
            default => 'default',
        };
    }

    /**
     * Get queue statistics and health metrics
     */
    public function getQueueStats(): array
    {
        try {
            $redis = Redis::connection();
            
            $queues = ['high_priority', 'medium_priority', 'low_priority', 'default', 'failed'];
            $stats = [];
            
            foreach ($queues as $queue) {
                $waiting = $redis->llen("queues:{$queue}");
                $delayed = $redis->zcard("queues:{$queue}:delayed");
                $reserved = $redis->zcard("queues:{$queue}:reserved");
                
                $stats[$queue] = [
                    'waiting' => $waiting,
                    'delayed' => $delayed,
                    'reserved' => $reserved,
                    'total' => $waiting + $delayed + $reserved,
                ];
            }
            
            // Get failed jobs count from database
            $failedJobs = DB::table('failed_jobs')->count();
            $stats['failed']['count'] = $failedJobs;
            
            // Calculate processing rates
            $stats['processing_rate'] = $this->calculateProcessingRate();
            $stats['average_wait_time'] = $this->calculateAverageWaitTime();
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error("Failed to get queue stats", ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Calculate job processing rate (jobs per minute)
     */
    protected function calculateProcessingRate(): float
    {
        try {
            // This would typically be tracked in a separate metrics system
            // For now, we'll estimate based on recent job completion
            $recentJobs = DB::table('jobs')
                ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                ->count();
                
            return round($recentJobs / 5, 2); // jobs per minute
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Calculate average wait time for jobs
     */
    protected function calculateAverageWaitTime(): float
    {
        try {
            $redis = Redis::connection();
            $queues = ['high_priority', 'medium_priority', 'low_priority', 'default'];
            
            $totalWaitTime = 0;
            $totalJobs = 0;
            
            foreach ($queues as $queue) {
                $jobs = $redis->lrange("queues:{$queue}", 0, -1);
                
                foreach ($jobs as $job) {
                    $jobData = json_decode($job, true);
                    if (isset($jobData['pushedAt'])) {
                        $waitTime = time() - $jobData['pushedAt'];
                        $totalWaitTime += $waitTime;
                        $totalJobs++;
                    }
                }
            }
            
            return $totalJobs > 0 ? round($totalWaitTime / $totalJobs, 2) : 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Optimize queue performance by rebalancing workload
     */
    public function optimizeQueues(): array
    {
        try {
            $stats = $this->getQueueStats();
            $optimizations = [];
            
            // Check for queue imbalances
            $highPriorityBacklog = $stats['high_priority']['waiting'] ?? 0;
            $mediumPriorityBacklog = $stats['medium_priority']['waiting'] ?? 0;
            $lowPriorityBacklog = $stats['low_priority']['waiting'] ?? 0;
            
            // If high priority queue is backing up, recommend more workers
            if ($highPriorityBacklog > 100) {
                $optimizations[] = [
                    'type' => 'scale_workers',
                    'queue' => 'high_priority',
                    'recommendation' => 'Increase high priority workers',
                    'current_backlog' => $highPriorityBacklog,
                ];
            }
            
            // If low priority queue is too large, consider pausing it temporarily
            if ($lowPriorityBacklog > 1000) {
                $optimizations[] = [
                    'type' => 'pause_queue',
                    'queue' => 'low_priority',
                    'recommendation' => 'Consider pausing low priority queue temporarily',
                    'current_backlog' => $lowPriorityBacklog,
                ];
            }
            
            // Check failed jobs
            $failedJobsCount = $stats['failed']['count'] ?? 0;
            if ($failedJobsCount > 50) {
                $optimizations[] = [
                    'type' => 'failed_jobs',
                    'recommendation' => 'Review and retry failed jobs',
                    'failed_count' => $failedJobsCount,
                ];
            }
            
            Log::info("Queue optimization analysis completed", [
                'optimizations_found' => count($optimizations),
                'stats' => $stats,
            ]);
            
            return [
                'stats' => $stats,
                'optimizations' => $optimizations,
                'recommendations' => $this->generateRecommendations($stats),
            ];
            
        } catch (\Exception $e) {
            Log::error("Queue optimization failed", ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Generate performance recommendations
     */
    protected function generateRecommendations(array $stats): array
    {
        $recommendations = [];
        
        $totalWaiting = array_sum(array_column($stats, 'waiting'));
        $avgWaitTime = $stats['average_wait_time'] ?? 0;
        
        if ($totalWaiting > 500) {
            $recommendations[] = "Consider increasing the number of queue workers";
        }
        
        if ($avgWaitTime > 300) { // 5 minutes
            $recommendations[] = "Average wait time is high - optimize job processing or add workers";
        }
        
        if (($stats['failed']['count'] ?? 0) > 0) {
            $recommendations[] = "Review failed jobs and implement better error handling";
        }
        
        $processingRate = $stats['processing_rate'] ?? 0;
        if ($processingRate < 1) {
            $recommendations[] = "Low processing rate detected - check worker health";
        }
        
        return $recommendations;
    }

    /**
     * Clean up old completed jobs and optimize queue storage
     */
    public function cleanupQueues(): array
    {
        try {
            $redis = Redis::connection();
            
            $cleaned = [
                'expired_jobs' => 0,
                'old_failed_jobs' => 0,
                'reserved_timeout' => 0,
            ];
            
            // Clean up expired delayed jobs
            $queues = ['high_priority', 'medium_priority', 'low_priority', 'default'];
            foreach ($queues as $queue) {
                $expired = $redis->zremrangebyscore("queues:{$queue}:delayed", 0, time());
                $cleaned['expired_jobs'] += $expired;
                
                // Clean up timed out reserved jobs
                $timeout = $redis->zremrangebyscore("queues:{$queue}:reserved", 0, time() - 3600);
                $cleaned['reserved_timeout'] += $timeout;
            }
            
            // Clean up old failed jobs (older than 7 days)
            $oldFailedJobs = DB::table('failed_jobs')
                ->where('failed_at', '<', Carbon::now()->subDays(7))
                ->delete();
            $cleaned['old_failed_jobs'] = $oldFailedJobs;
            
            Log::info("Queue cleanup completed", $cleaned);
            return $cleaned;
            
        } catch (\Exception $e) {
            Log::error("Queue cleanup failed", ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Monitor queue health and send alerts if needed
     */
    public function monitorQueueHealth(): array
    {
        $stats = $this->getQueueStats();
        $alerts = [];
        
        // Check for critical queue backlogs
        foreach (['high_priority', 'medium_priority'] as $queue) {
            $waiting = $stats[$queue]['waiting'] ?? 0;
            if ($waiting > 200) {
                $alerts[] = [
                    'level' => 'critical',
                    'message' => "Queue {$queue} has {$waiting} waiting jobs",
                    'queue' => $queue,
                    'count' => $waiting,
                ];
            }
        }
        
        // Check failed jobs
        $failedCount = $stats['failed']['count'] ?? 0;
        if ($failedCount > 20) {
            $alerts[] = [
                'level' => 'warning',
                'message' => "High number of failed jobs: {$failedCount}",
                'type' => 'failed_jobs',
                'count' => $failedCount,
            ];
        }
        
        // Check processing rate
        $processingRate = $stats['processing_rate'] ?? 0;
        if ($processingRate < 0.5) {
            $alerts[] = [
                'level' => 'warning',
                'message' => "Low processing rate: {$processingRate} jobs/minute",
                'type' => 'low_processing_rate',
                'rate' => $processingRate,
            ];
        }
        
        return [
            'status' => empty($alerts) ? 'healthy' : 'issues_detected',
            'alerts' => $alerts,
            'stats' => $stats,
            'timestamp' => now()->toISOString(),
        ];
    }
}
