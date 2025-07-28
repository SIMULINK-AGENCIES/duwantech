<?php

namespace App\Listeners;

use App\Events\NewActivityEvent;
use App\Events\UserOnlineEvent;
use App\Events\UserOfflineEvent;
use App\Models\AdminNotification;
use App\Models\ActiveSession;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UserActivityListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            // Handle different types of user activity events
            if ($event instanceof NewActivityEvent) {
                $this->handleNewActivity($event);
            } elseif ($event instanceof UserOnlineEvent) {
                $this->handleUserOnline($event);
            } elseif ($event instanceof UserOfflineEvent) {
                $this->handleUserOffline($event);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process user activity', [
                'event_type' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger queue retry
            throw $e;
        }
    }

    /**
     * Handle new activity events
     */
    protected function handleNewActivity(NewActivityEvent $event): void
    {
        $activity = $event->activity;
        
        // Check if this activity requires admin notification
        if ($this->shouldNotifyAdmin($activity)) {
            $this->createAdminNotification($activity);
        }

        // Update user session tracking
        $this->updateUserSession($activity);

        // Track suspicious activities
        $this->detectSuspiciousActivity($activity);

        // Update user statistics
        $this->updateUserStatistics($activity);

        Log::info('User activity processed', [
            'activity_id' => $activity->id,
            'user_id' => $activity->user_id,
            'action' => $activity->action,
        ]);
    }

    /**
     * Handle user online events
     */
    protected function handleUserOnline(UserOnlineEvent $event): void
    {
        $user = $event->user;
        $session = $event->session;

        // Session is already created/updated in the event, just log the activity
        if ($user && $session) {
            // Log user online activity
            ActivityLogger::log(
                'user_online',
                'User came online',
                $user->id,
                [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'session_id' => $session->session_id,
                    'total_active_users' => $event->totalActiveUsers,
                ]
            );

            // Check for multiple concurrent sessions
            $this->checkConcurrentSessions($user);

            Log::info('User online event processed', [
                'user_id' => $user->id,
                'session_id' => $session->session_id,
                'total_active_users' => $event->totalActiveUsers,
            ]);
        }

        // Update online users cache
        $this->updateOnlineUsersCache();
    }

    /**
     * Handle user offline events
     */
    protected function handleUserOffline(UserOfflineEvent $event): void
    {
        $user = $event->user;
        $sessionId = $event->sessionId;

        // Update active session
        ActiveSession::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->update([
                'is_active' => false,
                'logout_at' => now(),
            ]);

        // Log user offline activity
        ActivityLogger::log(
            'user_offline',
            'User went offline',
            $user->id,
            [
                'user_name' => $user->name,
                'session_id' => $sessionId,
                'session_duration' => $this->calculateSessionDuration($user->id, $sessionId),
            ]
        );

        // Update online users cache
        $this->updateOnlineUsersCache();

        Log::info('User offline event processed', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
        ]);
    }

    /**
     * Check if activity should trigger admin notification
     */
    protected function shouldNotifyAdmin($activity): bool
    {
        $notifiableActions = [
            'order_placed',
            'payment_completed',
            'payment_failed',
            'user_registered',
            'admin_login',
            'suspicious_activity',
            'bulk_order',
            'high_value_order',
            'security_breach_attempt',
        ];

        return in_array($activity->action, $notifiableActions) || 
               $this->isHighValueActivity($activity);
    }

    /**
     * Check if activity is high value
     */
    protected function isHighValueActivity($activity): bool
    {
        // Check for high-value orders
        if (isset($activity->data['amount']) && $activity->data['amount'] > 50000) {
            return true;
        }

        // Check for bulk activities
        if (isset($activity->data['quantity']) && $activity->data['quantity'] > 10) {
            return true;
        }

        return false;
    }

    /**
     * Create admin notification for activity
     */
    protected function createAdminNotification($activity): void
    {
        $notificationData = $this->generateActivityNotificationContent($activity);

        AdminNotification::create([
            'title' => $notificationData['title'],
            'message' => $notificationData['message'],
            'type' => $notificationData['type'],
            'data' => [
                'activity_id' => $activity->id,
                'user_id' => $activity->user_id,
                'user_name' => $activity->user->name ?? 'Unknown',
                'action' => $activity->action,
                'activity_data' => $activity->data,
                'ip_address' => $activity->ip_address,
                'user_agent' => $activity->user_agent,
                'created_at' => $activity->created_at->toISOString(),
            ],
            'read_at' => null,
            'action_url' => $this->getActivityActionUrl($activity),
        ]);
    }

    /**
     * Generate notification content based on activity
     */
    protected function generateActivityNotificationContent($activity): array
    {
        $userName = $activity->user->name ?? 'Unknown User';

        switch ($activity->action) {
            case 'order_placed':
                return [
                    'title' => 'New Order Placed',
                    'message' => "{$userName} placed a new order",
                    'type' => 'order',
                ];

            case 'user_registered':
                return [
                    'title' => 'New User Registration',
                    'message' => "{$userName} registered on the platform",
                    'type' => 'user_activity',
                ];

            case 'admin_login':
                return [
                    'title' => 'Admin Login',
                    'message' => "Admin {$userName} logged into the system",
                    'type' => 'security',
                ];

            case 'suspicious_activity':
                return [
                    'title' => 'Suspicious Activity Detected',
                    'message' => "Suspicious activity detected from {$userName}",
                    'type' => 'security_alert',
                ];

            default:
                return [
                    'title' => 'User Activity',
                    'message' => "{$userName} performed: {$activity->description}",
                    'type' => 'user_activity',
                ];
        }
    }

    /**
     * Get action URL for activity
     */
    protected function getActivityActionUrl($activity): ?string
    {
        switch ($activity->action) {
            case 'order_placed':
                return isset($activity->data['order_id']) 
                    ? route('admin.orders.show', $activity->data['order_id']) 
                    : null;

            case 'user_registered':
                return route('admin.users.show', $activity->user_id);

            default:
                return null;
        }
    }

    /**
     * Update user session tracking
     */
    protected function updateUserSession($activity): void
    {
        if ($activity->user_id) {
            ActiveSession::where('user_id', $activity->user_id)
                ->where('is_active', true)
                ->update(['last_activity' => now()]);
        }
    }

    /**
     * Detect suspicious activities
     */
    protected function detectSuspiciousActivity($activity): void
    {
        $suspiciousPatterns = [
            'multiple_failed_logins',
            'rapid_consecutive_orders',
            'unusual_ip_activity',
            'admin_access_outside_hours',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if ($this->matchesSuspiciousPattern($activity, $pattern)) {
                $this->flagSuspiciousActivity($activity, $pattern);
                break;
            }
        }
    }

    /**
     * Check if activity matches suspicious pattern
     */
    protected function matchesSuspiciousPattern($activity, $pattern): bool
    {
        switch ($pattern) {
            case 'multiple_failed_logins':
                return $activity->action === 'login_failed' && 
                       $this->getRecentFailedLogins($activity->user_id) >= 5;

            case 'rapid_consecutive_orders':
                return $activity->action === 'order_placed' && 
                       $this->getRecentOrderCount($activity->user_id) >= 5;

            case 'unusual_ip_activity':
                return $this->isUnusualIpActivity($activity);

            case 'admin_access_outside_hours':
                return $activity->action === 'admin_login' && 
                       $this->isOutsideBusinessHours();

            default:
                return false;
        }
    }

    /**
     * Flag suspicious activity
     */
    protected function flagSuspiciousActivity($activity, $pattern): void
    {
        ActivityLogger::log(
            'suspicious_activity',
            "Suspicious pattern detected: {$pattern}",
            $activity->user_id,
            [
                'original_activity_id' => $activity->id,
                'pattern' => $pattern,
                'ip_address' => $activity->ip_address,
                'severity' => 'medium',
            ]
        );

        Log::warning('Suspicious activity detected', [
            'pattern' => $pattern,
            'activity_id' => $activity->id,
            'user_id' => $activity->user_id,
        ]);
    }

    /**
     * Update user statistics
     */
    protected function updateUserStatistics($activity): void
    {
        $cacheKey = "user_stats_{$activity->user_id}";
        $stats = Cache::get($cacheKey, [
            'total_activities' => 0,
            'last_activity' => null,
            'daily_activities' => 0,
        ]);

        $stats['total_activities']++;
        $stats['last_activity'] = now()->toISOString();

        // Reset daily count if it's a new day
        if (!isset($stats['last_daily_reset']) || 
            Carbon::parse($stats['last_daily_reset'])->isYesterday()) {
            $stats['daily_activities'] = 1;
            $stats['last_daily_reset'] = now()->startOfDay()->toISOString();
        } else {
            $stats['daily_activities']++;
        }

        Cache::put($cacheKey, $stats, now()->addDays(7));
    }

    /**
     * Update online users cache
     */
    protected function updateOnlineUsersCache(): void
    {
        $onlineCount = ActiveSession::where('is_active', true)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->distinct('user_id')
            ->count();

        Cache::put('online_users_count', $onlineCount, now()->addMinutes(1));
    }

    /**
     * Check for concurrent sessions
     */
    protected function checkConcurrentSessions($user): void
    {
        $activeSessions = ActiveSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();

        if ($activeSessions > 3) {
            ActivityLogger::log(
                'multiple_concurrent_sessions',
                'User has multiple concurrent sessions',
                $user->id,
                [
                    'active_sessions_count' => $activeSessions,
                    'severity' => 'low',
                ]
            );
        }
    }

    /**
     * Calculate session duration
     */
    protected function calculateSessionDuration($userId, $sessionId): ?int
    {
        $session = ActiveSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->first();

        if ($session && $session->login_at) {
            return now()->diffInMinutes($session->login_at);
        }

        return null;
    }

    /**
     * Get recent failed login count
     */
    protected function getRecentFailedLogins($userId): int
    {
        return ActivityLog::where('user_id', $userId)
            ->where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHour())
            ->count();
    }

    /**
     * Get recent order count
     */
    protected function getRecentOrderCount($userId): int
    {
        return ActivityLog::where('user_id', $userId)
            ->where('action', 'order_placed')
            ->where('created_at', '>=', now()->subHour())
            ->count();
    }

    /**
     * Check if IP activity is unusual
     */
    protected function isUnusualIpActivity($activity): bool
    {
        if (!$activity->user_id || !$activity->ip_address) {
            return false;
        }

        $recentIps = ActivityLog::where('user_id', $activity->user_id)
            ->where('created_at', '>=', now()->subDays(7))
            ->distinct('ip_address')
            ->pluck('ip_address')
            ->toArray();

        return !in_array($activity->ip_address, $recentIps) && count($recentIps) > 0;
    }

    /**
     * Check if current time is outside business hours
     */
    protected function isOutsideBusinessHours(): bool
    {
        $currentHour = now()->hour;
        $businessStart = config('app.business_hours.start', 8);
        $businessEnd = config('app.business_hours.end', 18);

        return $currentHour < $businessStart || $currentHour > $businessEnd;
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('UserActivityListener job failed', [
            'event_type' => get_class($event),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Create a failure notification
        try {
            AdminNotification::create([
                'title' => 'User Activity Processing Failed',
                'message' => 'Failed to process user activity event',
                'type' => 'system_error',
                'data' => [
                    'event_type' => get_class($event),
                    'error' => $exception->getMessage(),
                    'failed_at' => now()->toISOString(),
                ],
                'read_at' => null,
            ]);
        } catch (\Exception $e) {
            Log::critical('Failed to create user activity failure notification', [
                'original_error' => $exception->getMessage(),
                'notification_error' => $e->getMessage(),
            ]);
        }
    }
}
