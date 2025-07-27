<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdvancedAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdvancedAnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AdvancedAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get real-time sales metrics
     */
    public function realTimeSalesMetrics(): JsonResponse
    {
        try {
            $metrics = $this->analyticsService->getRealTimeSalesMetrics();
            
            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sales metrics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversion tracking metrics
     */
    public function conversionMetrics(): JsonResponse
    {
        try {
            $metrics = $this->analyticsService->getConversionMetrics();
            
            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch conversion metrics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance indicators
     */
    public function performanceIndicators(): JsonResponse
    {
        try {
            $indicators = $this->analyticsService->getPerformanceIndicators();
            
            return response()->json([
                'success' => true,
                'data' => $indicators
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch performance indicators',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trend analysis data
     */
    public function trendAnalysis(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30d');
            
            // Validate period
            $validPeriods = ['7d', '30d', '90d', '1y'];
            if (!in_array($period, $validPeriods)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid period. Allowed values: ' . implode(', ', $validPeriods)
                ], 400);
            }
            
            $trends = $this->analyticsService->getTrendAnalysis($period);
            
            return response()->json([
                'success' => true,
                'data' => $trends
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trend analysis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics dashboard overview
     */
    public function dashboardOverview(): JsonResponse
    {
        try {
            $overview = [
                'sales_metrics' => $this->analyticsService->getRealTimeSalesMetrics(),
                'conversion_metrics' => $this->analyticsService->getConversionMetrics(),
                'performance_indicators' => $this->analyticsService->getPerformanceIndicators(),
                'trend_summary' => $this->analyticsService->getTrendAnalysis('7d')
            ];
            
            return response()->json([
                'success' => true,
                'data' => $overview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard overview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics data for specific metric
     */
    public function getMetric(Request $request, string $metric): JsonResponse
    {
        try {
            $data = null;
            $period = $request->get('period', '30d');
            
            switch ($metric) {
                case 'sales':
                    $data = $this->analyticsService->getRealTimeSalesMetrics();
                    break;
                case 'conversion':
                    $data = $this->analyticsService->getConversionMetrics();
                    break;
                case 'performance':
                    $data = $this->analyticsService->getPerformanceIndicators();
                    break;
                case 'trends':
                    $data = $this->analyticsService->getTrendAnalysis($period);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid metric requested'
                    ], 400);
            }
            
            return response()->json([
                'success' => true,
                'metric' => $metric,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to fetch {$metric} metric",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics export data
     */
    public function exportData(Request $request): JsonResponse
    {
        try {
            $format = $request->get('format', 'json');
            $period = $request->get('period', '30d');
            $metrics = $request->get('metrics', ['sales', 'conversion', 'performance']);
            
            $exportData = [];
            
            if (in_array('sales', $metrics)) {
                $exportData['sales'] = $this->analyticsService->getRealTimeSalesMetrics();
            }
            
            if (in_array('conversion', $metrics)) {
                $exportData['conversion'] = $this->analyticsService->getConversionMetrics();
            }
            
            if (in_array('performance', $metrics)) {
                $exportData['performance'] = $this->analyticsService->getPerformanceIndicators();
            }
            
            if (in_array('trends', $metrics)) {
                $exportData['trends'] = $this->analyticsService->getTrendAnalysis($period);
            }
            
            return response()->json([
                'success' => true,
                'format' => $format,
                'period' => $period,
                'export_data' => $exportData,
                'generated_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export analytics data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
