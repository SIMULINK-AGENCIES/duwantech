<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AdvancedRateLimiting
{
    /**
     * Handle an incoming request with intelligent rate limiting
     */
    public function handle(Request $request, Closure $next, string $limiter = 'default'): SymfonyResponse
    {
        $key = $this->resolveRequestSignature($request, $limiter);
        
        // Different limits based on endpoint type
        $limits = $this->getLimitsForEndpoint($request, $limiter);
        
        // Check if request should be rate limited
        if ($this->shouldRateLimit($request, $limiter)) {
            $executed = RateLimiter::attempt(
                $key,
                $limits['max_attempts'],
                function () use ($next, $request) {
                    return $next($request);
                },
                $limits['decay_minutes'] * 60
            );

            if (!$executed) {
                return $this->buildRateLimitResponse($key, $limits);
            }

            return $executed;
        }

        return $next($request);
    }

    /**
     * Resolve the rate limiting key for the request
     */
    protected function resolveRequestSignature(Request $request, string $limiter): string
    {
        $user = $request->user();
        
        // Authenticated users get per-user limits
        if ($user) {
            return sprintf('%s:%s:%s', $limiter, $user->id, $request->ip());
        }
        
        // Anonymous users get per-IP limits
        return sprintf('%s:ip:%s', $limiter, $request->ip());
    }

    /**
     * Get rate limits based on endpoint and user type
     */
    protected function getLimitsForEndpoint(Request $request, string $limiter): array
    {
        $user = $request->user();
        $isAdmin = $user && $user->hasRole('admin');
        $isPremium = $user && $user->hasRole('premium');
        
        $limits = [
            'api' => [
                'admin' => ['max_attempts' => 1000, 'decay_minutes' => 1],
                'premium' => ['max_attempts' => 300, 'decay_minutes' => 1],
                'user' => ['max_attempts' => 100, 'decay_minutes' => 1],
                'guest' => ['max_attempts' => 30, 'decay_minutes' => 1],
            ],
            'analytics' => [
                'admin' => ['max_attempts' => 500, 'decay_minutes' => 1],
                'premium' => ['max_attempts' => 100, 'decay_minutes' => 1],
                'user' => ['max_attempts' => 30, 'decay_minutes' => 1],
                'guest' => ['max_attempts' => 10, 'decay_minutes' => 1],
            ],
            'dashboard' => [
                'admin' => ['max_attempts' => 200, 'decay_minutes' => 1],
                'premium' => ['max_attempts' => 100, 'decay_minutes' => 1],
                'user' => ['max_attempts' => 60, 'decay_minutes' => 1],
                'guest' => ['max_attempts' => 20, 'decay_minutes' => 1],
            ],
            'auth' => [
                'admin' => ['max_attempts' => 20, 'decay_minutes' => 1],
                'premium' => ['max_attempts' => 15, 'decay_minutes' => 1],
                'user' => ['max_attempts' => 10, 'decay_minutes' => 1],
                'guest' => ['max_attempts' => 5, 'decay_minutes' => 1],
            ],
            'default' => [
                'admin' => ['max_attempts' => 300, 'decay_minutes' => 1],
                'premium' => ['max_attempts' => 200, 'decay_minutes' => 1],
                'user' => ['max_attempts' => 100, 'decay_minutes' => 1],
                'guest' => ['max_attempts' => 60, 'decay_minutes' => 1],
            ],
        ];

        $endpointLimits = $limits[$limiter] ?? $limits['default'];
        
        if ($isAdmin) {
            return $endpointLimits['admin'];
        } elseif ($isPremium) {
            return $endpointLimits['premium'];
        } elseif ($user) {
            return $endpointLimits['user'];
        } else {
            return $endpointLimits['guest'];
        }
    }

    /**
     * Determine if the request should be rate limited
     */
    protected function shouldRateLimit(Request $request, string $limiter): bool
    {
        // Skip rate limiting for certain conditions
        if ($this->isWhitelistedIP($request->ip())) {
            return false;
        }

        // Skip for internal API calls
        if ($request->hasHeader('X-Internal-Request')) {
            return false;
        }

        // Skip for health checks
        if ($request->is('health', 'status', 'ping')) {
            return false;
        }

        return true;
    }

    /**
     * Check if IP is whitelisted
     */
    protected function isWhitelistedIP(string $ip): bool
    {
        $whitelist = config('rate-limiting.whitelist', [
            '127.0.0.1',
            '::1',
        ]);

        return in_array($ip, $whitelist);
    }

    /**
     * Build rate limit exceeded response
     */
    protected function buildRateLimitResponse(string $key, array $limits): JsonResponse
    {
        $retryAfter = RateLimiter::availableIn($key);
        $remaining = RateLimiter::remaining($key, $limits['max_attempts']);
        
        $response = response()->json([
            'error' => 'Rate limit exceeded',
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
            'limit' => $limits['max_attempts'],
            'remaining' => max(0, $remaining),
            'reset' => now()->addSeconds($retryAfter)->timestamp,
        ], 429);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $limits['max_attempts'],
            'X-RateLimit-Remaining' => max(0, $remaining),
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
            'Retry-After' => $retryAfter,
        ]);
    }
}

/**
 * Specialized rate limiting for login attempts
 */
class LoginRateLimiting
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $key = $this->throttleKey($request);
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'error' => 'Too many login attempts',
                'message' => "Please try again in {$seconds} seconds.",
                'retry_after' => $seconds,
            ], 429);
        }

        $response = $next($request);
        
        // If login failed, record the attempt
        if ($response->getStatusCode() === 422) {
            RateLimiter::hit($key, 300); // 5 minute lockout
        } else {
            RateLimiter::clear($key);
        }

        return $response;
    }

    protected function throttleKey(Request $request): string
    {
        return sprintf(
            'login:%s:%s',
            $request->ip(),
            strtolower($request->input('email', ''))
        );
    }
}

/**
 * API-specific rate limiting with burst protection
 */
class ApiRateLimiting
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $user = $request->user();
        $key = $user ? "api:user:{$user->id}" : "api:ip:{$request->ip()}";
        
        // Burst protection: Allow up to 100 requests in 1 minute
        $burstKey = "{$key}:burst";
        if (RateLimiter::tooManyAttempts($burstKey, 100)) {
            return $this->buildBurstLimitResponse($burstKey);
        }
        
        // Standard limit: 1000 requests per hour
        $hourlyKey = "{$key}:hourly";
        if (RateLimiter::tooManyAttempts($hourlyKey, 1000)) {
            return $this->buildHourlyLimitResponse($hourlyKey);
        }
        
        RateLimiter::hit($burstKey, 60); // 1 minute window
        RateLimiter::hit($hourlyKey, 3600); // 1 hour window
        
        return $next($request);
    }
    
    protected function buildBurstLimitResponse(string $key): JsonResponse
    {
        return response()->json([
            'error' => 'Rate limit exceeded',
            'message' => 'Too many requests in a short period. Please slow down.',
            'type' => 'burst_limit',
            'retry_after' => RateLimiter::availableIn($key),
        ], 429);
    }
    
    protected function buildHourlyLimitResponse(string $key): JsonResponse
    {
        return response()->json([
            'error' => 'Rate limit exceeded',
            'message' => 'Hourly API limit reached. Please try again later.',
            'type' => 'hourly_limit',
            'retry_after' => RateLimiter::availableIn($key),
        ], 429);
    }
}
