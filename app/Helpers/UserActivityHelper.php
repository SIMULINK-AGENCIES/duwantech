<?php

namespace App\Helpers;

use App\Models\ActiveSession;
use App\Models\User;
use App\Services\GeolocationService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UserActivityHelper
{
    protected $geolocationService;

    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }
    /**
     * Get current active users count with caching.
     */
    public static function getActiveUsersCount(): int
    {
        return Cache::remember('active_users_count', 60, function () {
            return ActiveSession::active()->count();
        });
    }

    /**
     * Get authenticated active users count.
     */
    public static function getAuthenticatedUsersCount(): int
    {
        return Cache::remember('authenticated_users_count', 60, function () {
            return ActiveSession::active()->authenticated()->count();
        });
    }

    /**
     * Get guest users count.
     */
    public static function getGuestUsersCount(): int
    {
        return Cache::remember('guest_users_count', 60, function () {
            return ActiveSession::active()->guest()->count();
        });
    }

    /**
     * Get users online today count.
     */
    public static function getUsersTodayCount(): int
    {
        return Cache::remember('users_today_count', 300, function () {
            return ActiveSession::today()
                ->distinct('user_id')
                ->whereNotNull('user_id')
                ->count('user_id');
        });
    }

    /**
     * Get total sessions today count.
     */
    public static function getSessionsTodayCount(): int
    {
        return Cache::remember('sessions_today_count', 300, function () {
            return ActiveSession::today()->count();
        });
    }

    /**
     * Get live statistics for the counter widget.
     */
    public static function getLiveStats(): array
    {
        return [
            'active_users' => self::getActiveUsersCount(),
            'authenticated_users' => self::getAuthenticatedUsersCount(),
            'guest_users' => self::getGuestUsersCount(),
            'users_today' => self::getUsersTodayCount(),
            'sessions_today' => self::getSessionsTodayCount(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get top countries for active users.
     */
    public static function getTopCountries(int $limit = 5): array
    {
        return Cache::remember("top_countries_{$limit}", 300, function () use ($limit) {
            return ActiveSession::active()
                ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(location, "$.country")) as country, COUNT(*) as count')
                ->whereNotNull('location')
                ->groupBy('country')
                ->orderByDesc('count')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return [
                        'country' => $item->country,
                        'count' => $item->count,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get activity trends for the last 24 hours.
     */
    public static function getActivityTrends(): array
    {
        return Cache::remember('activity_trends_24h', 600, function () {
            $trends = [];
            $now = Carbon::now();
            
            // Get hourly data for the last 24 hours
            for ($i = 23; $i >= 0; $i--) {
                $hour = $now->copy()->subHours($i);
                $hourStart = $hour->startOfHour();
                $hourEnd = $hour->endOfHour();
                
                $sessions = ActiveSession::whereBetween('created_at', [$hourStart, $hourEnd])->count();
                $users = ActiveSession::whereBetween('created_at', [$hourStart, $hourEnd])
                    ->distinct('user_id')
                    ->whereNotNull('user_id')
                    ->count('user_id');
                
                $trends[] = [
                    'hour' => $hour->format('H:00'),
                    'sessions' => $sessions,
                    'users' => $users,
                    'timestamp' => $hour->toISOString(),
                ];
            }
            
            return $trends;
        });
    }

    /**
     * Get recent online users (authenticated only).
     */
    public static function getRecentOnlineUsers(int $limit = 10): array
    {
        return Cache::remember("recent_online_users_{$limit}", 120, function () use ($limit) {
            return ActiveSession::active()
                ->authenticated()
                ->with('user:id,name,email,avatar')
                ->orderByDesc('last_activity')
                ->limit($limit)
                ->get()
                ->map(function ($session) {
                    return [
                        'id' => $session->user->id,
                        'name' => $session->user->name,
                        'email' => $session->user->email,
                        'avatar' => $session->user->avatar ?? null,
                        'location' => $session->location_string,
                        'last_activity' => $session->last_activity->toISOString(),
                        'time_ago' => $session->time_since_last_activity,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get user activity status.
     */
    public static function getUserActivityStatus(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            return ['online' => false, 'last_seen' => null];
        }

        $activeSession = ActiveSession::where('user_id', $userId)
            ->active()
            ->orderByDesc('last_activity')
            ->first();

        return [
            'online' => $activeSession !== null,
            'last_seen' => $activeSession ? $activeSession->last_activity->toISOString() : null,
            'location' => $activeSession ? $activeSession->location_string : null,
            'session_count' => ActiveSession::where('user_id', $userId)->active()->count(),
        ];
    }

    /**
     * Get browser and device statistics.
     */
    public static function getBrowserStats(): array
    {
        return Cache::remember('browser_stats', 600, function () {
            $browsers = [];
            $devices = [];
            
            ActiveSession::active()
                ->get(['user_agent'])
                ->each(function ($session) use (&$browsers, &$devices) {
                    $userAgent = $session->user_agent;
                    
                    // Simple browser detection
                    if (str_contains($userAgent, 'Chrome')) {
                        $browsers['Chrome'] = ($browsers['Chrome'] ?? 0) + 1;
                    } elseif (str_contains($userAgent, 'Firefox')) {
                        $browsers['Firefox'] = ($browsers['Firefox'] ?? 0) + 1;
                    } elseif (str_contains($userAgent, 'Safari')) {
                        $browsers['Safari'] = ($browsers['Safari'] ?? 0) + 1;
                    } elseif (str_contains($userAgent, 'Edge')) {
                        $browsers['Edge'] = ($browsers['Edge'] ?? 0) + 1;
                    } else {
                        $browsers['Other'] = ($browsers['Other'] ?? 0) + 1;
                    }
                    
                    // Simple device detection
                    if (str_contains($userAgent, 'Mobile')) {
                        $devices['Mobile'] = ($devices['Mobile'] ?? 0) + 1;
                    } elseif (str_contains($userAgent, 'Tablet')) {
                        $devices['Tablet'] = ($devices['Tablet'] ?? 0) + 1;
                    } else {
                        $devices['Desktop'] = ($devices['Desktop'] ?? 0) + 1;
                    }
                });
            
            return [
                'browsers' => $browsers,
                'devices' => $devices,
            ];
        });
    }

    /**
     * Clear all cached statistics.
     */
    public static function clearCaches(): void
    {
        $keys = [
            'active_users_count',
            'authenticated_users_count',
            'guest_users_count',
            'users_today_count',
            'sessions_today_count',
            'top_countries_5',
            'activity_trends_24h',
            'recent_online_users_10',
            'browser_stats',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Format number with appropriate suffix (K, M, etc.).
     */
    public static function formatNumber(int $number): string
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        
        return (string) $number;
    }

    /**
     * Get growth percentage compared to yesterday.
     */
    public static function getGrowthPercentage(): array
    {
        $today = ActiveSession::today()->count();
        $yesterday = ActiveSession::whereDate('created_at', Carbon::yesterday())->count();
        
        $growth = 0;
        if ($yesterday > 0) {
            $growth = (($today - $yesterday) / $yesterday) * 100;
        }
        
        return [
            'percentage' => round($growth, 1),
            'direction' => $growth > 0 ? 'up' : ($growth < 0 ? 'down' : 'neutral'),
            'today' => $today,
            'yesterday' => $yesterday,
        ];
    }
}
