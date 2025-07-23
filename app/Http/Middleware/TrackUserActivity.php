<?php

namespace App\Http\Middleware;

use App\Models\ActiveSession;
use App\Models\User;
use App\Services\UserActivityService;
use App\Events\UserOnlineEvent;
use App\Events\UserOfflineEvent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    protected $userActivityService;

    public function __construct(UserActivityService $userActivityService)
    {
        $this->userActivityService = $userActivityService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tracking for certain routes
        if ($this->shouldSkipTracking($request)) {
            return $next($request);
        }

        // Process the request first
        $response = $next($request);

        // Track user activity after processing the request
        $this->trackActivity($request);

        return $response;
    }

    /**
     * Track user activity for the current request.
     */
    protected function trackActivity(Request $request): void
    {
        try {
            $sessionId = $request->session()->getId();
            $user = Auth::user();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
            $pageUrl = $request->fullUrl();

            // Get or create active session
            $activeSession = ActiveSession::where('session_id', $sessionId)->first();
            $isNewSession = false;

            if (!$activeSession) {
                // Create new session
                $locationData = $this->userActivityService->getLocationData($ipAddress);
                
                $activeSession = ActiveSession::create([
                    'user_id' => $user?->id,
                    'session_id' => $sessionId,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'location' => $locationData,
                    'page_url' => $pageUrl,
                    'last_activity' => now(),
                ]);

                $isNewSession = true;
            } else {
                // Update existing session
                $activeSession->update([
                    'user_id' => $user?->id, // Update in case user logged in/out
                    'page_url' => $pageUrl,
                    'last_activity' => now(),
                ]);
            }

            // Broadcast user online event for new sessions
            if ($isNewSession) {
                $totalActiveUsers = $this->userActivityService->getActiveUsersCount();
                broadcast(new UserOnlineEvent($user, $activeSession, $totalActiveUsers))->toOthers();
            }

            // Log activity if user is authenticated
            if ($user) {
                $this->userActivityService->logActivity($user, $request, $activeSession);
            }

        } catch (\Exception $e) {
            // Log error but don't break the application
            \Log::error('Error tracking user activity: ' . $e->getMessage(), [
                'session_id' => $request->session()->getId(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
        }
    }

    /**
     * Determine if tracking should be skipped for this request.
     */
    protected function shouldSkipTracking(Request $request): bool
    {
        $skipRoutes = [
            'telescope*',
            'horizon*',
            '_debugbar*',
            'health*',
            'livewire*',
            'broadcasting/auth',
        ];

        $skipExtensions = [
            '.css',
            '.js',
            '.png',
            '.jpg',
            '.jpeg',
            '.gif',
            '.svg',
            '.ico',
            '.woff',
            '.woff2',
            '.ttf',
            '.eot',
        ];

        // Skip API calls that are too frequent
        if ($request->is('api/heartbeat') || $request->is('api/ping')) {
            return true;
        }

        // Skip routes
        foreach ($skipRoutes as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        // Skip file extensions
        $path = $request->path();
        foreach ($skipExtensions as $extension) {
            if (str_ends_with($path, $extension)) {
                return true;
            }
        }

        // Skip AJAX requests that don't need tracking
        if ($request->ajax() && $request->is('admin/api/*')) {
            return true;
        }

        // Skip prefetch requests
        if ($request->header('Purpose') === 'prefetch') {
            return true;
        }

        return false;
    }

    /**
     * Handle session termination (called when session ends).
     */
    public function terminate(Request $request, Response $response): void
    {
        // This is called after the response is sent to the browser
        // We can use this for cleanup or additional logging if needed
    }
}
