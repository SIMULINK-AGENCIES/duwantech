<?php

namespace App\Services;

use App\Models\ActiveSession;
use App\Models\ActivityLog;
use App\Models\User;
use App\Events\UserOfflineEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserActivityService
{
    /**
     * Get location data from IP address using ipapi.co service.
     */
    public function getLocationData(string $ipAddress): ?array
    {
        // Skip for localhost/private IPs
        if ($this->isPrivateIp($ipAddress)) {
            return [
                'country' => 'Local',
                'city' => 'Development',
                'lat' => null,
                'lng' => null,
                'timezone' => config('app.timezone'),
            ];
        }

        // Cache location data for 24 hours to avoid excessive API calls
        $cacheKey = "location_data_{$ipAddress}";
        
        return Cache::remember($cacheKey, 86400, function () use ($ipAddress) {
            try {
                $response = Http::timeout(5)->get("http://ipapi.co/{$ipAddress}/json/");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    return [
                        'country' => $data['country_name'] ?? 'Unknown',
                        'city' => $data['city'] ?? 'Unknown',
                        'lat' => $data['latitude'] ?? null,
                        'lng' => $data['longitude'] ?? null,
                        'timezone' => $data['timezone'] ?? config('app.timezone'),
                        'region' => $data['region'] ?? null,
                        'postal' => $data['postal'] ?? null,
                        'country_code' => $data['country_code'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Failed to get location data for IP: ' . $ipAddress, [
                    'error' => $e->getMessage()
                ]);
            }

            // Return default data if API call fails
            return [
                'country' => 'Unknown',
                'city' => 'Unknown',
                'lat' => null,
                'lng' => null,
                'timezone' => config('app.timezone'),
            ];
        });
    }

    /**
     * Get current active users count.
     */
    public function getActiveUsersCount(): int
    {
        return Cache::remember('active_users_count', 60, function () {
            return ActiveSession::active()->count();
        });
    }

    /**
     * Get authenticated active users count.
     */
    public function getAuthenticatedActiveUsersCount(): int
    {
        return Cache::remember('authenticated_active_users_count', 60, function () {
            return ActiveSession::active()->authenticated()->count();
        });
    }

    /**
     * Get guest active users count.
     */
    public function getGuestActiveUsersCount(): int
    {
        return Cache::remember('guest_active_users_count', 60, function () {
            return ActiveSession::active()->guest()->count();
        });
    }

    /**
     * Log user activity.
     */
    public function logActivity(User $user, Request $request, ActiveSession $session): void
    {
        try {
            // Determine activity type based on request
            $action = $this->determineActivityAction($request);
            
            if ($action) {
                ActivityLog::logActivity(
                    $action,
                    $user->id,
                    null,
                    null,
                    [
                        'session_id' => $session->session_id,
                        'page_url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'location' => $session->location,
                        'timestamp' => now()->toISOString(),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to log user activity', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clean up old sessions and broadcast offline events.
     */
    public function cleanupOldSessions(): int
    {
        $cutoffTime = Carbon::now()->subMinutes(30); // 30 minutes threshold
        $oldSessions = ActiveSession::where('last_activity', '<', $cutoffTime)->get();
        
        $cleanedCount = 0;
        
        foreach ($oldSessions as $session) {
            try {
                $user = $session->user;
                $sessionDuration = $session->created_at->diffInSeconds($session->last_activity);
                
                // Broadcast user offline event
                $totalActiveUsers = $this->getActiveUsersCount() - 1; // Subtract this session
                broadcast(new UserOfflineEvent($user, $session->session_id, $totalActiveUsers, $sessionDuration));
                
                // Delete the session
                $session->delete();
                $cleanedCount++;
                
            } catch (\Exception $e) {
                Log::error('Failed to cleanup session', [
                    'session_id' => $session->session_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Clear related caches
        $this->clearActivityCaches();

        return $cleanedCount;
    }

    /**
     * Get activity statistics for the dashboard.
     */
    public function getActivityStats(): array
    {
        return Cache::remember('activity_stats', 300, function () { // Cache for 5 minutes
            return [
                'total_active_users' => $this->getActiveUsersCount(),
                'authenticated_users' => $this->getAuthenticatedActiveUsersCount(),
                'guest_users' => $this->getGuestActiveUsersCount(),
                'users_today' => ActiveSession::today()->distinct('user_id')->count('user_id'),
                'sessions_today' => ActiveSession::today()->count(),
                'top_countries' => $this->getTopCountries(),
                'recent_activity' => $this->getRecentActivity(),
            ];
        });
    }

    /**
     * Get top countries by active sessions.
     */
    public function getTopCountries(int $limit = 5): array
    {
        return ActiveSession::active()
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(location, "$.country")) as country, COUNT(*) as count')
            ->whereNotNull('location')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get recent activity logs.
     */
    public function getRecentActivity(int $limit = 10): array
    {
        return ActivityLog::with('user')
            ->recent()
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user' => $log->user ? [
                        'id' => $log->user->id,
                        'name' => $log->user->name,
                    ] : null,
                    'created_at' => $log->created_at->toISOString(),
                    'time_ago' => $log->time_ago,
                ];
            })
            ->toArray();
    }

    /**
     * Force user offline (for admin actions).
     */
    public function forceUserOffline(string $sessionId): bool
    {
        try {
            $session = ActiveSession::where('session_id', $sessionId)->first();
            
            if ($session) {
                $user = $session->user;
                $sessionDuration = $session->created_at->diffInSeconds($session->last_activity);
                $totalActiveUsers = $this->getActiveUsersCount() - 1;
                
                // Broadcast offline event
                broadcast(new UserOfflineEvent($user, $sessionId, $totalActiveUsers, $sessionDuration));
                
                // Delete session
                $session->delete();
                
                // Clear caches
                $this->clearActivityCaches();
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to force user offline', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * Check if IP address is private/local.
     */
    protected function isPrivateIp(string $ipAddress): bool
    {
        return filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }

    /**
     * Determine activity action based on request.
     */
    protected function determineActivityAction(Request $request): ?string
    {
        $path = $request->path();
        $method = $request->method();

        // Login/Logout detection
        if (str_contains($path, 'login') && $method === 'POST') {
            return ActivityLog::ACTION_LOGIN;
        }
        
        if (str_contains($path, 'logout')) {
            return ActivityLog::ACTION_LOGOUT;
        }

        // Order actions
        if (str_contains($path, 'orders')) {
            if ($method === 'POST') {
                return ActivityLog::ACTION_ORDER_CREATED;
            }
            if (in_array($method, ['PUT', 'PATCH'])) {
                return ActivityLog::ACTION_ORDER_UPDATED;
            }
        }

        // Product views
        if (str_contains($path, 'products/') && $method === 'GET') {
            return ActivityLog::ACTION_PRODUCT_VIEWED;
        }

        // Cart actions
        if (str_contains($path, 'cart')) {
            return ActivityLog::ACTION_CART_UPDATED;
        }

        // Profile actions
        if (str_contains($path, 'profile') && in_array($method, ['PUT', 'PATCH'])) {
            return ActivityLog::ACTION_PROFILE_UPDATED;
        }

        // Don't log every single page view to avoid spam
        return null;
    }

    /**
     * Clear activity-related caches.
     */
    protected function clearActivityCaches(): void
    {
        Cache::forget('active_users_count');
        Cache::forget('authenticated_active_users_count');
        Cache::forget('guest_active_users_count');
        Cache::forget('activity_stats');
    }
}
