<?php

namespace App\Services\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class WidgetPermissionManager
{
    private array $registeredWidgets = [];
    private array $permissionCache = [];
    private int $cacheTimeout = 3600; // 1 hour
    
    // Permission levels
    const PERMISSION_NONE = 0;
    const PERMISSION_VIEW = 1;
    const PERMISSION_CONFIGURE = 2;
    const PERMISSION_MANAGE = 3;
    const PERMISSION_ADMIN = 4;
    
    // Feature flag contexts
    const FEATURE_CONTEXT_GLOBAL = 'global';
    const FEATURE_CONTEXT_USER = 'user';
    const FEATURE_CONTEXT_ROLE = 'role';
    const FEATURE_CONTEXT_TENANT = 'tenant';
    
    /**
     * Set registered widgets for permission checking
     */
    public function setRegisteredWidgets(array $widgets): void
    {
        $this->registeredWidgets = $widgets;
    }
    
    /**
     * Check if user can access a widget
     */
    public function canUserAccessWidget(User $user, string $widgetId, string $action = 'view'): bool
    {
        $cacheKey = "widget_permission_{$user->id}_{$widgetId}_{$action}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($user, $widgetId, $action) {
            return $this->performPermissionCheck($user, $widgetId, $action);
        });
    }
    
    /**
     * Perform the actual permission check
     */
    private function performPermissionCheck(User $user, string $widgetId, string $action): bool
    {
        if (!isset($this->registeredWidgets[$widgetId])) {
            Log::warning("Permission check for unknown widget", ['widget_id' => $widgetId, 'user_id' => $user->id]);
            return false;
        }
        
        $widget = $this->registeredWidgets[$widgetId];
        
        // 1. Check if widget is enabled
        if (!$this->isWidgetEnabled($widget, $user)) {
            return false;
        }
        
        // 2. Check feature flags
        if (!$this->checkFeatureFlags($widget, $user)) {
            return false;
        }
        
        // 3. Check basic permissions
        if (!$this->checkBasicPermissions($widget, $user, $action)) {
            return false;
        }
        
        // 4. Check role-based permissions
        if (!$this->checkRolePermissions($widget, $user, $action)) {
            return false;
        }
        
        // 5. Check custom permission rules
        if (!$this->checkCustomPermissions($widget, $user, $action)) {
            return false;
        }
        
        // 6. Check tenant/organization permissions
        if (!$this->checkTenantPermissions($widget, $user, $action)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if widget is enabled
     */
    private function isWidgetEnabled(array $widget, User $user): bool
    {
        // Global enable/disable
        if (isset($widget['enabled']) && !$widget['enabled']) {
            return false;
        }
        
        // Status check
        if (isset($widget['status']) && $widget['status'] !== 'active') {
            return false;
        }
        
        // Maintenance mode check
        if (isset($widget['maintenance_mode']) && $widget['maintenance_mode']) {
            // Only admins can access during maintenance
            return $this->isUserAdmin($user);
        }
        
        return true;
    }
    
    /**
     * Check feature flags for widget access
     */
    private function checkFeatureFlags(array $widget, User $user): bool
    {
        $featureFlags = $widget['feature_flags'] ?? [];
        
        if (empty($featureFlags)) {
            return true;
        }
        
        foreach ($featureFlags as $flag) {
            if (is_string($flag)) {
                // Simple flag name
                if (!$this->isFeatureFlagEnabled($flag, $user)) {
                    return false;
                }
            } elseif (is_array($flag)) {
                // Complex flag with context
                $flagName = $flag['name'] ?? '';
                $context = $flag['context'] ?? self::FEATURE_CONTEXT_GLOBAL;
                $required = $flag['required'] ?? true;
                
                $enabled = $this->isFeatureFlagEnabledWithContext($flagName, $context, $user);
                
                if ($required && !$enabled) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Check basic permissions
     */
    private function checkBasicPermissions(array $widget, User $user, string $action): bool
    {
        $permissions = $widget['permissions'] ?? [];
        
        if (empty($permissions)) {
            return true; // No specific permissions required
        }
        
        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                // Simple permission string
                if ($user->can($permission)) {
                    return true;
                }
            } elseif (is_array($permission)) {
                // Permission with action mapping
                $requiredPermissions = $this->getPermissionsForAction($permission, $action);
                
                foreach ($requiredPermissions as $perm) {
                    if ($user->can($perm)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check role-based permissions
     */
    private function checkRolePermissions(array $widget, User $user, string $action): bool
    {
        $rolePermissions = $widget['role_permissions'] ?? [];
        
        if (empty($rolePermissions)) {
            return true; // No role restrictions
        }
        
        $userRoles = $this->getUserRoles($user);
        
        foreach ($rolePermissions as $roleConfig) {
            $allowedRoles = $roleConfig['roles'] ?? [];
            $deniedRoles = $roleConfig['denied_roles'] ?? [];
            $actions = $roleConfig['actions'] ?? ['view'];
            
            // Check if current action is covered by this rule
            if (!in_array($action, $actions) && !in_array('*', $actions)) {
                continue;
            }
            
            // Check denied roles first
            foreach ($deniedRoles as $deniedRole) {
                if (in_array($deniedRole, $userRoles)) {
                    return false;
                }
            }
            
            // Check allowed roles
            if (!empty($allowedRoles)) {
                $hasAllowedRole = false;
                foreach ($allowedRoles as $allowedRole) {
                    if (in_array($allowedRole, $userRoles)) {
                        $hasAllowedRole = true;
                        break;
                    }
                }
                
                if (!$hasAllowedRole) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Check custom permission rules
     */
    private function checkCustomPermissions(array $widget, User $user, string $action): bool
    {
        $customRules = $widget['custom_permissions'] ?? [];
        
        if (empty($customRules)) {
            return true;
        }
        
        foreach ($customRules as $rule) {
            $ruleType = $rule['type'] ?? 'callback';
            
            switch ($ruleType) {
                case 'callback':
                    if (!$this->evaluateCallbackRule($rule, $user, $action)) {
                        return false;
                    }
                    break;
                    
                case 'gate':
                    if (!$this->evaluateGateRule($rule, $user, $action)) {
                        return false;
                    }
                    break;
                    
                case 'expression':
                    if (!$this->evaluateExpressionRule($rule, $user, $action)) {
                        return false;
                    }
                    break;
                    
                case 'time_based':
                    if (!$this->evaluateTimeBasedRule($rule, $user, $action)) {
                        return false;
                    }
                    break;
            }
        }
        
        return true;
    }
    
    /**
     * Check tenant/organization permissions
     */
    private function checkTenantPermissions(array $widget, User $user, string $action): bool
    {
        $tenantConfig = $widget['tenant_permissions'] ?? [];
        
        if (empty($tenantConfig)) {
            return true;
        }
        
        $userTenant = $this->getUserTenant($user);
        
        if (!$userTenant) {
            return !($tenantConfig['require_tenant'] ?? false);
        }
        
        // Check tenant-specific permissions
        $tenantPermissions = $tenantConfig['permissions'] ?? [];
        $tenantRoles = $tenantConfig['roles'] ?? [];
        
        if (!empty($tenantPermissions)) {
            foreach ($tenantPermissions as $permission) {
                if (!$this->userHasTenantPermission($user, $userTenant, $permission)) {
                    return false;
                }
            }
        }
        
        if (!empty($tenantRoles)) {
            $userTenantRoles = $this->getUserTenantRoles($user, $userTenant);
            $hasRequiredRole = false;
            
            foreach ($tenantRoles as $role) {
                if (in_array($role, $userTenantRoles)) {
                    $hasRequiredRole = true;
                    break;
                }
            }
            
            if (!$hasRequiredRole) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get filtered widgets based on user permissions
     */
    public function getAccessibleWidgets(User $user, string $action = 'view'): array
    {
        $accessible = [];
        
        foreach ($this->registeredWidgets as $widgetId => $widget) {
            if ($this->canUserAccessWidget($user, $widgetId, $action)) {
                $accessible[$widgetId] = $widget;
            }
        }
        
        return $accessible;
    }
    
    /**
     * Get user's permission level for a widget
     */
    public function getUserWidgetPermissionLevel(User $user, string $widgetId): int
    {
        $levels = [
            'admin' => self::PERMISSION_ADMIN,
            'manage' => self::PERMISSION_MANAGE,
            'configure' => self::PERMISSION_CONFIGURE,
            'view' => self::PERMISSION_VIEW,
        ];
        
        foreach ($levels as $action => $level) {
            if ($this->canUserAccessWidget($user, $widgetId, $action)) {
                return $level;
            }
        }
        
        return self::PERMISSION_NONE;
    }
    
    /**
     * Bulk permission check for multiple widgets
     */
    public function checkBulkPermissions(User $user, array $widgetIds, string $action = 'view'): array
    {
        $results = [];
        
        foreach ($widgetIds as $widgetId) {
            $results[$widgetId] = $this->canUserAccessWidget($user, $widgetId, $action);
        }
        
        return $results;
    }
    
    /**
     * Get permission audit trail for a user and widget
     */
    public function getPermissionAuditTrail(User $user, string $widgetId, string $action = 'view'): array
    {
        $trail = [
            'user_id' => $user->id,
            'widget_id' => $widgetId,
            'action' => $action,
            'timestamp' => now(),
            'checks' => []
        ];
        
        if (!isset($this->registeredWidgets[$widgetId])) {
            $trail['result'] = false;
            $trail['reason'] = 'Widget not found';
            return $trail;
        }
        
        $widget = $this->registeredWidgets[$widgetId];
        
        // Perform checks and record results
        $trail['checks']['widget_enabled'] = $this->isWidgetEnabled($widget, $user);
        $trail['checks']['feature_flags'] = $this->checkFeatureFlags($widget, $user);
        $trail['checks']['basic_permissions'] = $this->checkBasicPermissions($widget, $user, $action);
        $trail['checks']['role_permissions'] = $this->checkRolePermissions($widget, $user, $action);
        $trail['checks']['custom_permissions'] = $this->checkCustomPermissions($widget, $user, $action);
        $trail['checks']['tenant_permissions'] = $this->checkTenantPermissions($widget, $user, $action);
        
        $trail['result'] = array_reduce($trail['checks'], function($carry, $check) {
            return $carry && $check;
        }, true);
        
        return $trail;
    }
    
    /**
     * Clear permission cache for user
     */
    public function clearUserPermissionCache(User $user, ?string $widgetId = null): void
    {
        if ($widgetId) {
            $pattern = "widget_permission_{$user->id}_{$widgetId}_*";
        } else {
            $pattern = "widget_permission_{$user->id}_*";
        }
        
        $this->clearCacheByPattern($pattern);
    }
    
    /**
     * Register permission gates for widgets
     */
    public function registerPermissionGates(): void
    {
        foreach ($this->registeredWidgets as $widgetId => $widget) {
            $this->registerWidgetGates($widgetId, $widget);
        }
    }
    
    // Helper methods
    
    private function isFeatureFlagEnabled(string $flagName, User $user): bool
    {
        return $this->isFeatureFlagEnabledWithContext($flagName, self::FEATURE_CONTEXT_GLOBAL, $user);
    }
    
    private function isFeatureFlagEnabledWithContext(string $flagName, string $context, User $user): bool
    {
        // This would integrate with your feature flag system
        // Examples: LaunchDarkly, Flipper, custom implementation
        
        switch ($context) {
            case self::FEATURE_CONTEXT_GLOBAL:
                return Config::get("features.{$flagName}", false);
                
            case self::FEATURE_CONTEXT_USER:
                return $user->hasFeatureFlag($flagName) ?? Config::get("features.{$flagName}", false);
                
            case self::FEATURE_CONTEXT_ROLE:
                $roles = $this->getUserRoles($user);
                return $this->checkRoleFeatureFlags($flagName, $roles);
                
            case self::FEATURE_CONTEXT_TENANT:
                $tenant = $this->getUserTenant($user);
                return $this->checkTenantFeatureFlags($flagName, $tenant);
                
            default:
                return false;
        }
    }
    
    private function getPermissionsForAction(array $permissionConfig, string $action): array
    {
        return $permissionConfig[$action] ?? $permissionConfig['default'] ?? [];
    }
    
    private function getUserRoles(User $user): array
    {
        // This should integrate with your role system (Spatie, custom, etc.)
        return $user->roles()->pluck('name')->toArray();
    }
    
    private function isUserAdmin(User $user): bool
    {
        $adminRoles = ['admin', 'super-admin', 'administrator'];
        $userRoles = $this->getUserRoles($user);
        
        return !empty(array_intersect($adminRoles, $userRoles));
    }
    
    private function getUserTenant(User $user): ?string
    {
        // Implement based on your multi-tenancy setup
        return $user->tenant_id ?? null;
    }
    
    private function userHasTenantPermission(User $user, string $tenant, string $permission): bool
    {
        // Implement tenant-specific permission checking
        return $user->can($permission) || $user->can("{$permission}:{$tenant}");
    }
    
    private function getUserTenantRoles(User $user, string $tenant): array
    {
        // Implement tenant-specific role retrieval
        return $user->rolesForTenant($tenant)->pluck('name')->toArray() ?? [];
    }
    
    private function evaluateCallbackRule(array $rule, User $user, string $action): bool
    {
        $callback = $rule['callback'] ?? null;
        
        if (!$callback || !is_callable($callback)) {
            return true;
        }
        
        return call_user_func($callback, $user, $action, $rule);
    }
    
    private function evaluateGateRule(array $rule, User $user, string $action): bool
    {
        $gateName = $rule['gate'] ?? '';
        $parameters = $rule['parameters'] ?? [];
        
        return Gate::forUser($user)->allows($gateName, $parameters);
    }
    
    private function evaluateExpressionRule(array $rule, User $user, string $action): bool
    {
        $expression = $rule['expression'] ?? '';
        
        // Simple expression evaluator - could be enhanced with a proper parser
        $variables = [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'action' => $action,
            'is_admin' => $this->isUserAdmin($user),
            'roles' => $this->getUserRoles($user),
        ];
        
        return $this->evaluateExpression($expression, $variables);
    }
    
    private function evaluateTimeBasedRule(array $rule, User $user, string $action): bool
    {
        $timezone = $rule['timezone'] ?? 'UTC';
        $allowedTimes = $rule['allowed_times'] ?? [];
        $deniedTimes = $rule['denied_times'] ?? [];
        
        $now = now($timezone);
        
        // Check denied times first
        foreach ($deniedTimes as $timeRange) {
            if ($this->isTimeInRange($now, $timeRange)) {
                return false;
            }
        }
        
        // Check allowed times
        if (!empty($allowedTimes)) {
            foreach ($allowedTimes as $timeRange) {
                if ($this->isTimeInRange($now, $timeRange)) {
                    return true;
                }
            }
            return false; // Not in any allowed time range
        }
        
        return true; // No time restrictions
    }
    
    private function registerWidgetGates(string $widgetId, array $widget): void
    {
        Gate::define("widget.{$widgetId}.view", function (User $user) use ($widgetId) {
            return $this->canUserAccessWidget($user, $widgetId, 'view');
        });
        
        Gate::define("widget.{$widgetId}.configure", function (User $user) use ($widgetId) {
            return $this->canUserAccessWidget($user, $widgetId, 'configure');
        });
        
        Gate::define("widget.{$widgetId}.manage", function (User $user) use ($widgetId) {
            return $this->canUserAccessWidget($user, $widgetId, 'manage');
        });
    }
    
    private function checkRoleFeatureFlags(string $flagName, array $roles): bool
    {
        // Implement role-based feature flag checking
        foreach ($roles as $role) {
            if (Config::get("features.roles.{$role}.{$flagName}", false)) {
                return true;
            }
        }
        return false;
    }
    
    private function checkTenantFeatureFlags(string $flagName, ?string $tenant): bool
    {
        if (!$tenant) {
            return false;
        }
        
        return Config::get("features.tenants.{$tenant}.{$flagName}", false);
    }
    
    private function evaluateExpression(string $expression, array $variables): bool
    {
        // Simple expression evaluation - in production, use a proper expression parser
        // This is a basic implementation for demonstration
        
        foreach ($variables as $key => $value) {
            $expression = str_replace("{{$key}}", json_encode($value), $expression);
        }
        
        // Basic safety check - only allow simple comparisons
        if (preg_match('/^[a-zA-Z0-9_\s\'"=!<>()&|,\[\]]+$/', $expression)) {
            try {
                return eval("return {$expression};");
            } catch (Exception $e) {
                Log::warning("Expression evaluation failed", ['expression' => $expression, 'error' => $e->getMessage()]);
                return false;
            }
        }
        
        return false;
    }
    
    private function isTimeInRange(\Carbon\Carbon $time, array $range): bool
    {
        $start = $range['start'] ?? '00:00';
        $end = $range['end'] ?? '23:59';
        $days = $range['days'] ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        // Check if current day is allowed
        if (!in_array(strtolower($time->format('l')), array_map('strtolower', $days))) {
            return false;
        }
        
        // Check if current time is in range
        $currentTime = $time->format('H:i');
        return $currentTime >= $start && $currentTime <= $end;
    }
    
    private function clearCacheByPattern(string $pattern): void
    {
        // This would depend on your cache implementation
        // Basic implementation for demonstration
        $keys = Cache::getRedis()->keys($pattern);
        if (!empty($keys)) {
            Cache::deleteMultiple($keys);
        }
    }
}
