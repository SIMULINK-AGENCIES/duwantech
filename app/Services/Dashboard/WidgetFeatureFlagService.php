<?php

namespace App\Services\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class WidgetFeatureFlagService
{
    private array $flags = [];
    private int $cacheTimeout = 3600; // 1 hour
    
    // Flag contexts
    const CONTEXT_GLOBAL = 'global';
    const CONTEXT_USER = 'user';
    const CONTEXT_ROLE = 'role';
    const CONTEXT_TENANT = 'tenant';
    const CONTEXT_AB_TEST = 'ab_test';
    
    // Flag strategies
    const STRATEGY_BOOLEAN = 'boolean';
    const STRATEGY_PERCENTAGE = 'percentage';
    const STRATEGY_WHITELIST = 'whitelist';
    const STRATEGY_BLACKLIST = 'blacklist';
    const STRATEGY_TIME_WINDOW = 'time_window';
    const STRATEGY_CUSTOM = 'custom';
    
    public function __construct()
    {
        $this->loadFeatureFlags();
    }
    
    /**
     * Check if a feature flag is enabled for a user
     */
    public function isEnabled(string $flagName, ?User $user = null, array $context = []): bool
    {
        $cacheKey = $this->getCacheKey($flagName, $user, $context);
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($flagName, $user, $context) {
            return $this->evaluateFlag($flagName, $user, $context);
        });
    }
    
    /**
     * Get flag value (for non-boolean flags)
     */
    public function getValue(string $flagName, ?User $user = null, array $context = [], $default = null)
    {
        if (!$this->isEnabled($flagName, $user, $context)) {
            return $default;
        }
        
        $flag = $this->getFlag($flagName);
        return $flag['value'] ?? $default;
    }
    
    /**
     * Check multiple flags at once
     */
    public function checkMultiple(array $flagNames, ?User $user = null, array $context = []): array
    {
        $results = [];
        
        foreach ($flagNames as $flagName) {
            $results[$flagName] = $this->isEnabled($flagName, $user, $context);
        }
        
        return $results;
    }
    
    /**
     * Register a new feature flag
     */
    public function registerFlag(string $name, array $config): void
    {
        $this->flags[$name] = array_merge([
            'enabled' => false,
            'strategy' => self::STRATEGY_BOOLEAN,
            'context' => self::CONTEXT_GLOBAL,
            'value' => true,
            'conditions' => [],
            'metadata' => []
        ], $config);
    }
    
    /**
     * Enable a feature flag
     */
    public function enableFlag(string $name, ?array $conditions = null): void
    {
        if (isset($this->flags[$name])) {
            $this->flags[$name]['enabled'] = true;
            if ($conditions) {
                $this->flags[$name]['conditions'] = $conditions;
            }
            $this->clearFlagCache($name);
        }
    }
    
    /**
     * Disable a feature flag
     */
    public function disableFlag(string $name): void
    {
        if (isset($this->flags[$name])) {
            $this->flags[$name]['enabled'] = false;
            $this->clearFlagCache($name);
        }
    }
    
    /**
     * Get all flags for a user
     */
    public function getUserFlags(?User $user = null): array
    {
        $flags = [];
        
        foreach ($this->flags as $name => $config) {
            $flags[$name] = [
                'enabled' => $this->isEnabled($name, $user),
                'value' => $this->getValue($name, $user),
                'metadata' => $config['metadata'] ?? []
            ];
        }
        
        return $flags;
    }
    
    /**
     * Get flag configuration
     */
    public function getFlag(string $name): ?array
    {
        return $this->flags[$name] ?? null;
    }
    
    /**
     * Update flag configuration
     */
    public function updateFlag(string $name, array $updates): bool
    {
        if (!isset($this->flags[$name])) {
            return false;
        }
        
        $this->flags[$name] = array_merge($this->flags[$name], $updates);
        $this->clearFlagCache($name);
        
        return true;
    }
    
    /**
     * Remove a feature flag
     */
    public function removeFlag(string $name): bool
    {
        if (isset($this->flags[$name])) {
            unset($this->flags[$name]);
            $this->clearFlagCache($name);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get feature flags for widget
     */
    public function getWidgetFlags(string $widgetId, ?User $user = null): array
    {
        $widgetFlags = [];
        
        foreach ($this->flags as $name => $config) {
            // Check if flag is widget-specific
            if (isset($config['widget_id']) && $config['widget_id'] === $widgetId) {
                $widgetFlags[$name] = $this->isEnabled($name, $user);
            }
            
            // Check if flag has widget context
            if (isset($config['context_data']['widget_id']) && $config['context_data']['widget_id'] === $widgetId) {
                $widgetFlags[$name] = $this->isEnabled($name, $user, ['widget_id' => $widgetId]);
            }
        }
        
        return $widgetFlags;
    }
    
    /**
     * Clear all caches for a flag
     */
    public function clearFlagCache(string $flagName): void
    {
        $pattern = "feature_flag_{$flagName}_*";
        $this->clearCacheByPattern($pattern);
    }
    
    /**
     * Clear all feature flag caches
     */
    public function clearAllCaches(): void
    {
        $pattern = "feature_flag_*";
        $this->clearCacheByPattern($pattern);
    }
    
    // Private methods
    
    private function loadFeatureFlags(): void
    {
        // Load from configuration
        $configFlags = Config::get('features', []);
        
        foreach ($configFlags as $name => $config) {
            $this->registerFlag($name, $config);
        }
        
        // Load widget-specific flags
        $this->loadWidgetFlags();
    }
    
    private function loadWidgetFlags(): void
    {
        // Common widget feature flags
        $widgetFlags = [
            'widget_analytics' => [
                'enabled' => true,
                'strategy' => self::STRATEGY_BOOLEAN,
                'context' => self::CONTEXT_GLOBAL,
                'metadata' => ['description' => 'Enable analytics tracking for widgets']
            ],
            'widget_caching' => [
                'enabled' => true,
                'strategy' => self::STRATEGY_BOOLEAN,
                'context' => self::CONTEXT_GLOBAL,
                'metadata' => ['description' => 'Enable caching for widget data']
            ],
            'widget_real_time' => [
                'enabled' => false,
                'strategy' => self::STRATEGY_PERCENTAGE,
                'context' => self::CONTEXT_USER,
                'value' => 25, // 25% rollout
                'metadata' => ['description' => 'Enable real-time updates for widgets']
            ],
            'widget_beta_features' => [
                'enabled' => false,
                'strategy' => self::STRATEGY_WHITELIST,
                'context' => self::CONTEXT_USER,
                'conditions' => [
                    'user_ids' => [],
                    'roles' => ['beta_tester', 'admin']
                ],
                'metadata' => ['description' => 'Enable beta features for widgets']
            ]
        ];
        
        foreach ($widgetFlags as $name => $config) {
            $this->registerFlag($name, $config);
        }
    }
    
    private function evaluateFlag(string $flagName, ?User $user, array $context): bool
    {
        $flag = $this->getFlag($flagName);
        
        if (!$flag) {
            return false;
        }
        
        if (!$flag['enabled']) {
            return false;
        }
        
        $strategy = $flag['strategy'];
        
        switch ($strategy) {
            case self::STRATEGY_BOOLEAN:
                return $this->evaluateBooleanStrategy($flag, $user, $context);
                
            case self::STRATEGY_PERCENTAGE:
                return $this->evaluatePercentageStrategy($flag, $user, $context);
                
            case self::STRATEGY_WHITELIST:
                return $this->evaluateWhitelistStrategy($flag, $user, $context);
                
            case self::STRATEGY_BLACKLIST:
                return $this->evaluateBlacklistStrategy($flag, $user, $context);
                
            case self::STRATEGY_TIME_WINDOW:
                return $this->evaluateTimeWindowStrategy($flag, $user, $context);
                
            case self::STRATEGY_CUSTOM:
                return $this->evaluateCustomStrategy($flag, $user, $context);
                
            default:
                return false;
        }
    }
    
    private function evaluateBooleanStrategy(array $flag, ?User $user, array $context): bool
    {
        return $flag['value'] ?? true;
    }
    
    private function evaluatePercentageStrategy(array $flag, ?User $user, array $context): bool
    {
        $percentage = $flag['value'] ?? 0;
        
        if ($percentage <= 0) {
            return false;
        }
        
        if ($percentage >= 100) {
            return true;
        }
        
        // Use user ID for consistent results
        $seed = $user ? $user->id : time();
        $hash = crc32($flag['name'] ?? '' . $seed);
        $userPercentage = abs($hash) % 100;
        
        return $userPercentage < $percentage;
    }
    
    private function evaluateWhitelistStrategy(array $flag, ?User $user, array $context): bool
    {
        $conditions = $flag['conditions'] ?? [];
        
        // Check user IDs
        if (isset($conditions['user_ids']) && $user) {
            if (in_array($user->id, $conditions['user_ids'])) {
                return true;
            }
        }
        
        // Check user emails
        if (isset($conditions['emails']) && $user) {
            if (in_array($user->email, $conditions['emails'])) {
                return true;
            }
        }
        
        // Check roles
        if (isset($conditions['roles']) && $user) {
            $userRoles = $this->getUserRoles($user);
            if (array_intersect($conditions['roles'], $userRoles)) {
                return true;
            }
        }
        
        // Check context values
        if (isset($conditions['context'])) {
            foreach ($conditions['context'] as $key => $values) {
                if (isset($context[$key]) && in_array($context[$key], (array) $values)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    private function evaluateBlacklistStrategy(array $flag, ?User $user, array $context): bool
    {
        // Blacklist is inverse of whitelist
        return !$this->evaluateWhitelistStrategy($flag, $user, $context);
    }
    
    private function evaluateTimeWindowStrategy(array $flag, ?User $user, array $context): bool
    {
        $conditions = $flag['conditions'] ?? [];
        $timezone = $conditions['timezone'] ?? 'UTC';
        $now = now($timezone);
        
        // Check date range
        if (isset($conditions['start_date']) || isset($conditions['end_date'])) {
            $startDate = isset($conditions['start_date']) ? 
                \Carbon\Carbon::parse($conditions['start_date'], $timezone) : null;
            $endDate = isset($conditions['end_date']) ? 
                \Carbon\Carbon::parse($conditions['end_date'], $timezone) : null;
            
            if ($startDate && $now->lt($startDate)) {
                return false;
            }
            
            if ($endDate && $now->gt($endDate)) {
                return false;
            }
        }
        
        // Check time of day
        if (isset($conditions['time_ranges'])) {
            $currentTime = $now->format('H:i');
            $inTimeRange = false;
            
            foreach ($conditions['time_ranges'] as $range) {
                $start = $range['start'] ?? '00:00';
                $end = $range['end'] ?? '23:59';
                
                if ($currentTime >= $start && $currentTime <= $end) {
                    $inTimeRange = true;
                    break;
                }
            }
            
            if (!$inTimeRange) {
                return false;
            }
        }
        
        // Check days of week
        if (isset($conditions['days_of_week'])) {
            $currentDay = strtolower($now->format('l'));
            if (!in_array($currentDay, array_map('strtolower', $conditions['days_of_week']))) {
                return false;
            }
        }
        
        return true;
    }
    
    private function evaluateCustomStrategy(array $flag, ?User $user, array $context): bool
    {
        $callback = $flag['callback'] ?? null;
        
        if (!$callback || !is_callable($callback)) {
            return false;
        }
        
        try {
            return (bool) call_user_func($callback, $flag, $user, $context);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function getCacheKey(string $flagName, ?User $user, array $context): string
    {
        $userKey = $user ? $user->id : 'anonymous';
        $contextKey = md5(serialize($context));
        
        return "feature_flag_{$flagName}_{$userKey}_{$contextKey}";
    }
    
    private function getUserRoles(User $user): array
    {
        // This should integrate with your role system
        return method_exists($user, 'roles') ? 
            $user->roles()->pluck('name')->toArray() : [];
    }
    
    private function clearCacheByPattern(string $pattern): void
    {
        // Implementation depends on cache driver
        try {
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::deleteMultiple($keys);
            }
        } catch (\Exception $e) {
            // Fallback: just clear specific patterns we know about
            Cache::flush();
        }
    }
}
