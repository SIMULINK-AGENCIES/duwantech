<?php

namespace App\Services;

use App\Models\ActiveSession;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    protected $ipApiUrl = 'http://ip-api.com/json/';
    protected $cachePrefix = 'geolocation:';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Get location data for an IP address
     */
    public function getLocationData(string $ipAddress): array
    {
        // Skip localhost and private IPs
        if ($this->isLocalIP($ipAddress)) {
            return $this->getDefaultLocation();
        }

        $cacheKey = $this->cachePrefix . $ipAddress;
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($ipAddress) {
            try {
                $response = Http::timeout(5)->get($this->ipApiUrl . $ipAddress, [
                    'fields' => 'status,message,country,countryCode,region,regionName,city,lat,lon,timezone,isp'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success') {
                        return [
                            'country' => $data['country'] ?? 'Unknown',
                            'country_code' => $data['countryCode'] ?? 'XX',
                            'region' => $data['regionName'] ?? 'Unknown',
                            'city' => $data['city'] ?? 'Unknown',
                            'latitude' => $data['lat'] ?? 0,
                            'longitude' => $data['lon'] ?? 0,
                            'timezone' => $data['timezone'] ?? 'UTC',
                            'isp' => $data['isp'] ?? 'Unknown',
                            'accuracy' => 'city',
                            'updated_at' => now()
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Geolocation API failed for IP: ' . $ipAddress, [
                    'error' => $e->getMessage()
                ]);
            }

            return $this->getDefaultLocation();
        });
    }

    /**
     * Get all active user locations
     */
    public function getActiveUserLocations(): array
    {
        $cacheKey = 'dashboard:active_user_locations';
        
        return Cache::remember($cacheKey, 60, function () {
            $sessions = ActiveSession::with('user')
                ->where('last_activity', '>=', now()->subMinutes(10))
                ->whereNotNull('location_data')
                ->get();

            $locations = [];
            $countryCounts = [];
            $cityCounts = [];

            foreach ($sessions as $session) {
                $locationData = json_decode($session->location_data, true);
                
                if (!$locationData || !isset($locationData['latitude'], $locationData['longitude'])) {
                    continue;
                }

                $country = $locationData['country'] ?? 'Unknown';
                $city = $locationData['city'] ?? 'Unknown';
                
                // Count by country
                $countryCounts[$country] = ($countryCounts[$country] ?? 0) + 1;
                
                // Count by city
                $cityKey = $city . ', ' . $country;
                $cityCounts[$cityKey] = ($cityCounts[$cityKey] ?? 0) + 1;

                $locations[] = [
                    'id' => $session->id,
                    'user_id' => $session->user_id,
                    'user_name' => $session->user ? $session->user->name : 'Anonymous',
                    'user_email' => $session->user ? $session->user->email : null,
                    'latitude' => (float) $locationData['latitude'],
                    'longitude' => (float) $locationData['longitude'],
                    'country' => $country,
                    'country_code' => $locationData['country_code'] ?? 'XX',
                    'region' => $locationData['region'] ?? 'Unknown',
                    'city' => $city,
                    'timezone' => $locationData['timezone'] ?? 'UTC',
                    'isp' => $locationData['isp'] ?? 'Unknown',
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'is_authenticated' => !is_null($session->user_id),
                    'session_duration' => now()->diffInMinutes($session->created_at),
                    'page_views' => $this->getSessionPageViews($session->id)
                ];
            }

            return [
                'locations' => $locations,
                'summary' => [
                    'total_active_users' => count($locations),
                    'authenticated_users' => collect($locations)->where('is_authenticated', true)->count(),
                    'anonymous_users' => collect($locations)->where('is_authenticated', false)->count(),
                    'countries' => $countryCounts,
                    'cities' => $cityCounts,
                    'unique_countries' => count($countryCounts),
                    'unique_cities' => count($cityCounts)
                ]
            ];
        });
    }

    /**
     * Get location statistics for analytics
     */
    public function getLocationStats(): array
    {
        $cacheKey = 'dashboard:location_stats';
        
        return Cache::remember($cacheKey, 300, function () {
            // Get data for the last 24 hours
            $sessions = ActiveSession::where('created_at', '>=', now()->subHours(24))
                ->whereNotNull('location_data')
                ->get();

            $countries = [];
            $cities = [];
            $hourlyActivity = [];

            foreach ($sessions as $session) {
                $locationData = json_decode($session->location_data, true);
                
                if (!$locationData) continue;

                $country = $locationData['country'] ?? 'Unknown';
                $city = $locationData['city'] ?? 'Unknown';
                $hour = $session->created_at->format('H:00');

                // Count by country
                if (!isset($countries[$country])) {
                    $countries[$country] = [
                        'name' => $country,
                        'code' => $locationData['country_code'] ?? 'XX',
                        'count' => 0,
                        'authenticated' => 0,
                        'anonymous' => 0
                    ];
                }
                $countries[$country]['count']++;
                
                if ($session->user_id) {
                    $countries[$country]['authenticated']++;
                } else {
                    $countries[$country]['anonymous']++;
                }

                // Count by city
                $cityKey = $city . ', ' . $country;
                if (!isset($cities[$cityKey])) {
                    $cities[$cityKey] = [
                        'name' => $city,
                        'country' => $country,
                        'latitude' => $locationData['latitude'] ?? 0,
                        'longitude' => $locationData['longitude'] ?? 0,
                        'count' => 0
                    ];
                }
                $cities[$cityKey]['count']++;

                // Hourly activity
                $hourlyActivity[$hour] = ($hourlyActivity[$hour] ?? 0) + 1;
            }

            // Sort by count
            uasort($countries, fn($a, $b) => $b['count'] <=> $a['count']);
            uasort($cities, fn($a, $b) => $b['count'] <=> $a['count']);

            return [
                'countries' => array_values($countries),
                'cities' => array_values($cities),
                'hourly_activity' => $hourlyActivity,
                'total_sessions' => $sessions->count(),
                'total_countries' => count($countries),
                'total_cities' => count($cities)
            ];
        });
    }

    /**
     * Get heatmap data for map visualization
     */
    public function getHeatmapData(): array
    {
        $locations = $this->getActiveUserLocations();
        
        $heatmapPoints = [];
        
        foreach ($locations['locations'] as $location) {
            $heatmapPoints[] = [
                'lat' => $location['latitude'],
                'lng' => $location['longitude'],
                'count' => 1, // Each user counts as 1 point
                'radius' => $location['is_authenticated'] ? 20 : 15, // Larger radius for authenticated users
                'intensity' => $location['is_authenticated'] ? 1 : 0.7
            ];
        }

        return [
            'points' => $heatmapPoints,
            'max_intensity' => 1,
            'gradient' => [
                0.4 => '#3B82F6',  // Blue for low activity
                0.6 => '#10B981',  // Green for medium activity
                0.8 => '#F59E0B',  // Yellow for high activity
                1.0 => '#EF4444'   // Red for very high activity
            ]
        ];
    }

    /**
     * Check if IP is local/private
     */
    protected function isLocalIP(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    /**
     * Get default location for local/unknown IPs
     */
    protected function getDefaultLocation(): array
    {
        return [
            'country' => 'Kenya',
            'country_code' => 'KE',
            'region' => 'Nairobi',
            'city' => 'Nairobi',
            'latitude' => -1.286389,
            'longitude' => 36.817223,
            'timezone' => 'Africa/Nairobi',
            'isp' => 'Local Network',
            'accuracy' => 'country',
            'updated_at' => now()
        ];
    }

    /**
     * Get page views for a session (mock data for now)
     */
    protected function getSessionPageViews(int $sessionId): int
    {
        // In a real implementation, this would count actual page views
        return rand(1, 10);
    }

    /**
     * Update user location data
     */
    public function updateUserLocation(int $sessionId, array $locationData): void
    {
        ActiveSession::where('id', $sessionId)
            ->update([
                'location_data' => json_encode($locationData),
                'updated_at' => now()
            ]);

        // Clear related caches
        Cache::forget('dashboard:active_user_locations');
        Cache::forget('dashboard:location_stats');
    }

    /**
     * Get real-time location updates
     */
    public function getRealTimeUpdates(): array
    {
        // Get recent location changes (last 5 minutes)
        $recentSessions = ActiveSession::with('user')
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->whereNotNull('location_data')
            ->orderBy('last_activity', 'desc')
            ->limit(50)
            ->get();

        $updates = [];
        foreach ($recentSessions as $session) {
            $locationData = json_decode($session->location_data, true);
            
            if (!$locationData) continue;

            $updates[] = [
                'session_id' => $session->id,
                'user_name' => $session->user ? $session->user->name : 'Anonymous User',
                'user_avatar' => $session->user ? $session->user->avatar : null,
                'latitude' => $locationData['latitude'],
                'longitude' => $locationData['longitude'],
                'city' => $locationData['city'],
                'country' => $locationData['country'],
                'timestamp' => $session->last_activity,
                'is_new' => $session->created_at->gte(now()->subMinutes(5)),
                'activity_type' => $session->created_at->gte(now()->subMinutes(1)) ? 'new_visitor' : 'active_visitor'
            ];
        }

        return $updates;
    }
}
