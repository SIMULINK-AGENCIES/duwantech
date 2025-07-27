<?php

use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\GeographicMapController;
use App\Http\Controllers\Api\AdvancedAnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard API Routes
Route::prefix('dashboard')->group(function () {
    Route::get('/metrics', [DashboardApiController::class, 'getMetrics']);
    Route::get('/chart-data', [DashboardApiController::class, 'getChartData']);
    Route::get('/quick-stats', [DashboardApiController::class, 'getQuickStats']);
    Route::get('/system-health', [DashboardApiController::class, 'getSystemHealth']);
    Route::get('/activity-feed', [DashboardApiController::class, 'activityFeed']);
});

// Geographic Map API Routes
Route::prefix('geographic')->group(function () {
    Route::get('/active-locations', [GeographicMapController::class, 'activeLocations']);
    Route::get('/location-stats', [GeographicMapController::class, 'locationStats']);
    Route::get('/heatmap-data', [GeographicMapController::class, 'heatmapData']);
    Route::get('/real-time-updates', [GeographicMapController::class, 'realTimeUpdates']);
    Route::get('/location-overview', [GeographicMapController::class, 'locationOverview']);
    Route::get('/geographical-distribution', [GeographicMapController::class, 'geographicalDistribution']);
});

// Advanced Analytics API Routes
Route::prefix('admin/analytics')->group(function () {
    Route::get('/real-time-sales', [AdvancedAnalyticsController::class, 'realTimeSalesMetrics']);
    Route::get('/conversion', [AdvancedAnalyticsController::class, 'conversionMetrics']);
    Route::get('/performance', [AdvancedAnalyticsController::class, 'performanceIndicators']);
    Route::get('/trends', [AdvancedAnalyticsController::class, 'trendAnalysis']);
    Route::get('/overview', [AdvancedAnalyticsController::class, 'dashboardOverview']);
    Route::get('/metric/{metric}', [AdvancedAnalyticsController::class, 'getMetric']);
    Route::get('/export', [AdvancedAnalyticsController::class, 'exportData']);
});
