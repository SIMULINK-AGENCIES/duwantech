<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestPerformanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $testData;

    /**
     * Create a new job instance.
     */
    public function __construct($testData)
    {
        $this->testData = $testData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate some work for performance testing
        usleep(100000); // 100ms delay
        
        Log::info('Performance test job executed', [
            'data' => $this->testData,
            'memory_usage' => memory_get_usage(true),
            'execution_time' => microtime(true)
        ]);
    }
}
