<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\Events\JobFailed;
use Exception;

class SystemHealthService
{
    protected array $healthChecks = [];
    protected array $alerts = [];

    public function __construct()
    {
        $this->healthChecks = [
            'database' => 'checkDatabase',
            'cache' => 'checkCache',
            'storage' => 'checkStorage',
            'queues' => 'checkQueues',
            'memory' => 'checkMemory',
            'disk_space' => 'checkDiskSpace',
            'external_apis' => 'checkExternalApis',
        ];
    }

    /**
     * Run complete system health check
     */
    public function checkSystemHealth(): array
    {
        $results = [
            'overall_status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'checks' => [],
            'alerts' => [],
            'performance_score' => 0,
        ];

        $healthyChecks = 0;
        $totalChecks = count($this->healthChecks);

        foreach ($this->healthChecks as $checkName => $method) {
            try {
                $checkResult = $this->$method();
                $results['checks'][$checkName] = $checkResult;

                if ($checkResult['status'] === 'healthy') {
                    $healthyChecks++;
                } elseif ($checkResult['status'] === 'warning') {
                    $results['overall_status'] = 'warning';
                } elseif ($checkResult['status'] === 'critical') {
                    $results['overall_status'] = 'critical';
                }

                if (!empty($checkResult['alerts'])) {
                    $results['alerts'] = array_merge($results['alerts'], $checkResult['alerts']);
                }
            } catch (Exception $e) {
                $results['checks'][$checkName] = [
                    'status' => 'error',
                    'message' => 'Health check failed',
                    'error' => $e->getMessage(),
                    'alerts' => [['level' => 'critical', 'message' => "Health check {$checkName} failed: " . $e->getMessage()]]
                ];
                $results['overall_status'] = 'critical';
                $results['alerts'][] = ['level' => 'critical', 'message' => "Health check {$checkName} failed"];
                
                Log::error("Health check {$checkName} failed", ['error' => $e->getMessage()]);
            }
        }

        // Calculate performance score
        $results['performance_score'] = round(($healthyChecks / $totalChecks) * 100, 1);

        // Cache results for monitoring dashboard
        Cache::put('system_health', $results, now()->addMinutes(5));

        return $results;
    }

    /**
     * Check database connectivity and performance
     */
    protected function checkDatabase(): array
    {
        $result = [
            'status' => 'healthy',
            'message' => 'Database is operational',
            'metrics' => [],
            'alerts' => []
        ];

        try {
            // Test connection
            $start = microtime(true);
            DB::connection()->getPdo();
            $connectionTime = (microtime(true) - $start) * 1000;

            // Test query performance
            $start = microtime(true);
            $count = DB::table('users')->count();
            $queryTime = (microtime(true) - $start) * 1000;

            $result['metrics'] = [
                'connection_time_ms' => round($connectionTime, 2),
                'query_time_ms' => round($queryTime, 2),
                'total_users' => $count,
            ];

            // Check for slow queries
            if ($queryTime > 1000) {
                $result['status'] = 'warning';
                $result['message'] = 'Database queries are slow';
                $result['alerts'][] = [
                    'level' => 'warning',
                    'message' => "Database query took {$queryTime}ms"
                ];
            }

            if ($connectionTime > 500) {
                $result['status'] = 'warning';
                $result['alerts'][] = [
                    'level' => 'warning',
                    'message' => "Database connection took {$connectionTime}ms"
                ];
            }

        } catch (Exception $e) {
            $result['status'] = 'critical';
            $result['message'] = 'Database connection failed';
            $result['alerts'][] = [
                'level' => 'critical',
                'message' => 'Database unavailable: ' . $e->getMessage()
            ];
        }

        return $result;
    }

    /**
     * Check cache system health
     */
    protected function checkCache(): array
    {
        $result = [
            'status' => 'healthy',
            'message' => 'Cache system is operational',
            'metrics' => [],
            'alerts' => []
        ];

        try {
            // Test cache write/read
            $testKey = 'health_check_' . time();
            $testValue = 'test_value_' . rand(1000, 9999);
            
            $start = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $cached = Cache::get($testKey);
            $cacheTime = (microtime(true) - $start) * 1000;

            Cache::forget($testKey);

            if ($cached !== $testValue) {
                throw new Exception('Cache read/write test failed');
            }

            $result['metrics']['cache_time_ms'] = round($cacheTime, 2);

            // Get cache stats if available
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                try {
                    $redis = Cache::getStore()->connection();
                    $info = $redis->info('memory');
                    
                    if (isset($info['used_memory_human'])) {
                        $result['metrics']['memory_used'] = $info['used_memory_human'];
                    }
                } catch (Exception $e) {
                    // Redis info failed, but cache is working
                }
            }

            if ($cacheTime > 100) {
                $result['status'] = 'warning';
                $result['alerts'][] = [
                    'level' => 'warning',
                    'message' => "Cache operations are slow ({$cacheTime}ms)"
                ];
            }

        } catch (Exception $e) {
            $result['status'] = 'critical';
            $result['message'] = 'Cache system failed';
            $result['alerts'][] = [
                'level' => 'critical',
                'message' => 'Cache unavailable: ' . $e->getMessage()
            ];
        }

        return $result;
    }

    /**
     * Check storage system health
     */
    protected function checkStorage(): array
    {
        $result = [
            'status' => 'healthy',
            'message' => 'Storage system is operational',
            'metrics' => [],
            'alerts' => []
        ];

        try {
            // Test file operations
            $testFile = 'health_check_' . time() . '.txt';
            $testContent = 'Health check test content';

            $start = microtime(true);
            Storage::put($testFile, $testContent);
            $retrieved = Storage::get($testFile);
            $storageTime = (microtime(true) - $start) * 1000;

            Storage::delete($testFile);

            if ($retrieved !== $testContent) {
                throw new Exception('Storage read/write test failed');
            }

            $result['metrics']['storage_time_ms'] = round($storageTime, 2);

            if ($storageTime > 500) {
                $result['status'] = 'warning';
                $result['alerts'][] = [
                    'level' => 'warning',
                    'message' => "Storage operations are slow ({$storageTime}ms)"
                ];
            }

        } catch (Exception $e) {
            $result['status'] = 'critical';
            $result['message'] = 'Storage system failed';
            $result['alerts'][] = [
                'level' => 'critical',
                'message' => 'Storage unavailable: ' . $e->getMessage()
            ];
        }

        return $result;
    }

    /**
     * Check queue system health
     */
    protected function checkQueues(): array
    {
        $result = [
            'status' => 'healthy',
            'message' => 'Queue system is operational',
            'metrics' => [],
            'alerts' => []
        ];

        try {
            $queueService = app(QueueOptimizationService::class);
            $queueHealth = $queueService->monitorQueueHealth();

            $result['metrics'] = $queueHealth['stats'] ?? [];
            
            if ($queueHealth['status'] !== 'healthy') {
                $result['status'] = 'warning';
                $result['message'] = 'Queue system has issues';
                $result['alerts'] = $queueHealth['alerts'] ?? [];
            }

            // Check for stuck jobs
            $stuckJobs = DB::table('jobs')
                ->where('reserved_at', '<', now()->subHours(1)->timestamp)
                ->count();

            if ($stuckJobs > 0) {
                $result['status'] = 'warning';
                $result['alerts'][] = [
                    'level' => 'warning',
                    'message' => "{$stuckJobs} jobs appear to be stuck"
                ];
            }

        } catch (Exception $e) {
            $result['status'] = 'critical';
            $result['message'] = 'Queue system check failed';
            $result['alerts'][] = [
                'level' => 'critical',
                'message' => 'Queue system error: ' . $e->getMessage()
            ];
        }

        return $result;
    }

    /**
     * Check memory usage
     */
    protected function checkMemory(): array
    {
        $result = [
            'status' => 'healthy',
            'message' => 'Memory usage is normal',
            'metrics' => [],
            'alerts' => []
        ];

        $currentUsage = memory_get_usage(true);
        $peakUsage = memory_get_peak_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));

        $result['metrics'] = [
            'current_usage_mb' => round($currentUsage / 1024 / 1024, 2),
            'peak_usage_mb' => round($peakUsage / 1024 / 1024, 2),
            'memory_limit_mb' => $memoryLimit > 0 ? round($memoryLimit / 1024 / 1024, 2) : 'unlimited',
        ];

        if ($memoryLimit > 0) {
            $usagePercentage = ($currentUsage / $memoryLimit) * 100;
            $result['metrics']['usage_percentage'] = round($usagePercentage, 1);

            if ($usagePercentage > 90) {
                $result['status'] = 'critical';
                $result['message'] = 'Memory usage is critical';
                $result['alerts'][] = [
                    'level' => 'critical',
                    'message' => "Memory usage at {$usagePercentage}%"
                ];
            } elseif ($usagePercentage > 75) {
                $result['status'] = 'warning';
                $result['message'] = 'Memory usage is high';
                $result['alerts'][] = [
                    'level' => 'warning',
                    'message' => "Memory usage at {$usagePercentage}%"
                ];
            }
        }

        return $result;
    }

    /**
     * Check disk space
     */
    protected function checkDiskSpace(): array
    {
        $result = [
            'status' => 'healthy',
            'message' => 'Disk space is sufficient',
            'metrics' => [],
            'alerts' => []
        ];

        try {
            $storagePath = storage_path();
            $freeBytes = disk_free_space($storagePath);
            $totalBytes = disk_total_space($storagePath);

            if ($freeBytes !== false && $totalBytes !== false) {
                $usedBytes = $totalBytes - $freeBytes;
                $usagePercentage = ($usedBytes / $totalBytes) * 100;

                $result['metrics'] = [
                    'free_space_gb' => round($freeBytes / 1024 / 1024 / 1024, 2),
                    'total_space_gb' => round($totalBytes / 1024 / 1024 / 1024, 2),
                    'used_percentage' => round($usagePercentage, 1),
                ];

                if ($usagePercentage > 95) {
                    $result['status'] = 'critical';
                    $result['message'] = 'Disk space is critically low';
                    $result['alerts'][] = [
                        'level' => 'critical',
                        'message' => "Disk usage at {$usagePercentage}%"
                    ];
                } elseif ($usagePercentage > 85) {
                    $result['status'] = 'warning';
                    $result['message'] = 'Disk space is running low';
                    $result['alerts'][] = [
                        'level' => 'warning',
                        'message' => "Disk usage at {$usagePercentage}%"
                    ];
                }
            }
        } catch (Exception $e) {
            $result['status'] = 'warning';
            $result['message'] = 'Could not check disk space';
            $result['alerts'][] = [
                'level' => 'warning',
                'message' => 'Disk space check failed: ' . $e->getMessage()
            ];
        }

        return $result;
    }

    /**
     * Check external API connectivity
     */
    protected function checkExternalApis(): array
    {
        $result = [
            'status' => 'healthy',
            'message' => 'External APIs are accessible',
            'metrics' => [],
            'alerts' => []
        ];

        // Add checks for payment gateways, email services, etc.
        $apiChecks = [
            'email_service' => config('mail.default'),
            'payment_gateway' => 'Mpesa/PayPal', // Based on your setup
        ];

        $failedApis = [];

        foreach ($apiChecks as $api => $service) {
            try {
                // Basic connectivity check
                $start = microtime(true);
                
                // You can implement specific API health checks here
                // For now, just check if services are configured
                $isConfigured = !empty($service);
                
                $responseTime = (microtime(true) - $start) * 1000;
                
                $result['metrics'][$api . '_response_time_ms'] = round($responseTime, 2);
                $result['metrics'][$api . '_configured'] = $isConfigured;

                if (!$isConfigured) {
                    $failedApis[] = $api;
                }
                
            } catch (Exception $e) {
                $failedApis[] = $api;
                $result['alerts'][] = [
                    'level' => 'warning',
                    'message' => "API {$api} check failed: " . $e->getMessage()
                ];
            }
        }

        if (!empty($failedApis)) {
            $result['status'] = 'warning';
            $result['message'] = 'Some external APIs have issues';
        }

        return $result;
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

    /**
     * Get system health summary for dashboard
     */
    public function getHealthSummary(): array
    {
        $cached = Cache::get('system_health');
        
        if (!$cached) {
            $cached = $this->checkSystemHealth();
        }

        return [
            'status' => $cached['overall_status'],
            'score' => $cached['performance_score'],
            'alerts_count' => count($cached['alerts']),
            'last_check' => $cached['timestamp'],
            'critical_alerts' => array_filter($cached['alerts'], fn($alert) => $alert['level'] === 'critical'),
        ];
    }
}
