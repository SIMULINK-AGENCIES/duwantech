<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeolocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeographicMapController extends Controller
{
    protected $geolocationService;

    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    /**
     * Get active user locations for map display
     */
    public function activeLocations(): JsonResponse
    {
        try {
            $locations = $this->geolocationService->getActiveUserLocations();
            
            return response()->json([
                'success' => true,
                'data' => $locations,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch active locations'
            ], 500);
        }
    }

    /**
     * Get location statistics for analytics
     */
    public function locationStats(): JsonResponse
    {
        try {
            $stats = $this->geolocationService->getLocationStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch location statistics'
            ], 500);
        }
    }

    /**
     * Get heatmap data for map visualization
     */
    public function heatmapData(): JsonResponse
    {
        try {
            $heatmap = $this->geolocationService->getHeatmapData();
            
            return response()->json([
                'success' => true,
                'data' => $heatmap,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch heatmap data'
            ], 500);
        }
    }

    /**
     * Get real-time location updates
     */
    public function realTimeUpdates(): JsonResponse
    {
        try {
            $updates = $this->geolocationService->getRealTimeUpdates();
            
            return response()->json([
                'success' => true,
                'data' => $updates,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch real-time updates'
            ], 500);
        }
    }

    /**
     * Get location overview with top countries and cities
     */
    public function locationOverview(): JsonResponse
    {
        try {
            $locations = $this->geolocationService->getActiveUserLocations();
            $stats = $this->geolocationService->getLocationStats();
            
            // Extract top locations for overview
            $topCountries = array_slice($stats['countries'], 0, 10);
            $topCities = array_slice($stats['cities'], 0, 10);
            
            $overview = [
                'summary' => $locations['summary'],
                'top_countries' => $topCountries,
                'top_cities' => $topCities,
                'hourly_activity' => $stats['hourly_activity'] ?? [],
                'total_sessions_24h' => $stats['total_sessions'] ?? 0
            ];
            
            return response()->json([
                'success' => true,
                'data' => $overview,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch location overview'
            ], 500);
        }
    }

    /**
     * Get geographical distribution data
     */
    public function geographicalDistribution(): JsonResponse
    {
        try {
            $locations = $this->geolocationService->getActiveUserLocations();
            
            // Group by regions for continental view
            $continents = [
                'Africa' => ['country_codes' => ['KE', 'UG', 'TZ', 'NG', 'ZA', 'GH', 'ET', 'MA', 'EG'], 'count' => 0, 'users' => []],
                'Asia' => ['country_codes' => ['IN', 'CN', 'JP', 'SG', 'TH', 'MY', 'PH', 'ID', 'VN'], 'count' => 0, 'users' => []],
                'Europe' => ['country_codes' => ['GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'SE', 'NO', 'DK'], 'count' => 0, 'users' => []],
                'North America' => ['country_codes' => ['US', 'CA', 'MX'], 'count' => 0, 'users' => []],
                'South America' => ['country_codes' => ['BR', 'AR', 'CL', 'CO', 'PE'], 'count' => 0, 'users' => []],
                'Oceania' => ['country_codes' => ['AU', 'NZ', 'FJ'], 'count' => 0, 'users' => []]
            ];
            
            foreach ($locations['locations'] as $location) {
                $countryCode = $location['country_code'];
                
                foreach ($continents as $continent => &$data) {
                    if (in_array($countryCode, $data['country_codes'])) {
                        $data['count']++;
                        $data['users'][] = [
                            'user_name' => $location['user_name'],
                            'country' => $location['country'],
                            'city' => $location['city'],
                            'is_authenticated' => $location['is_authenticated']
                        ];
                        break;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'continents' => $continents,
                    'total_users' => count($locations['locations']),
                    'unique_countries' => $locations['summary']['unique_countries'],
                    'unique_cities' => $locations['summary']['unique_cities']
                ],
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch geographical distribution'
            ], 500);
        }
    }
}
