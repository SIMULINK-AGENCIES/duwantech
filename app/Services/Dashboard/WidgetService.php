<?php

namespace App\Services\Dashboard;

use App\Contracts\Dashboard\WidgetServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class WidgetService implements WidgetServiceInterface
{
    private array $registeredWidgets = [];
    private int $cacheTimeout = 3600; // 1 hour
    
    public function __construct()
    {
        $this->initializeDefaults();
    }
    
    /**
     * Get user's active widgets with caching
     */
    public function getUserWidgets(int $userId): array
    {
        $cacheKey = "user_widgets_{$userId}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($userId) {
            $user = User::find($userId);
            if (!$user || !$user->dashboard_preferences) {
                Log::info("Loading default widgets for user", ['user_id' => $userId]);
                return $this->getDefaultWidgets();
            }
            
            $preferences = $user->dashboard_preferences;
            $widgets = $preferences['widgets'] ?? $this->getDefaultWidgets();
            
            // Validate and sanitize widgets
            $validatedWidgets = $this->validateUserWidgets($widgets);
            
            Log::info("Loaded user widgets from cache", [
                'user_id' => $userId,
                'widget_count' => count($validatedWidgets)
            ]);
            
            return $validatedWidgets;
        });
    }
    
    /**
     * Save user's widget configuration with validation
     */
    public function saveUserWidgets(int $userId, array $widgets): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                Log::warning("User not found when saving widgets", ['user_id' => $userId]);
                return false;
            }
            
            // Validate widget configuration
            $validationResult = $this->validateWidgetConfig($widgets);
            if (!empty($validationResult)) {
                Log::warning("Widget validation failed", [
                    'user_id' => $userId,
                    'errors' => $validationResult
                ]);
                return false;
            }
            
            // Check permissions for each widget
            $accessibleWidgets = [];
            foreach ($widgets as $widget) {
                if ($this->canUserAccessWidget($userId, $widget['id'])) {
                    $accessibleWidgets[] = $widget;
                } else {
                    Log::warning("User cannot access widget", [
                        'user_id' => $userId,
                        'widget_id' => $widget['id']
                    ]);
                }
            }
            
            $preferences = $user->dashboard_preferences ?? [];
            $preferences['widgets'] = $accessibleWidgets;
            $preferences['updated_at'] = now();
            
            $success = $user->update(['dashboard_preferences' => $preferences]);
            
            if ($success) {
                // Clear user cache
                $this->clearUserCache($userId);
                
                Log::info("Successfully saved user widgets", [
                    'user_id' => $userId,
                    'widget_count' => count($accessibleWidgets)
                ]);
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error("Error saving user widgets", [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get all available widgets with caching and filtering
     */
    public function getAvailableWidgets(string $category = null): array
    {
        $cacheKey = $category ? "available_widgets_{$category}" : "available_widgets_all";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($category) {
            $widgets = $this->registeredWidgets;
            
            if ($category) {
                $widgets = array_filter($widgets, function($widget) use ($category) {
                    return $widget['category'] === $category;
                });
            }
            
            // Add additional metadata
            foreach ($widgets as $id => &$widget) {
                $widget['id'] = $id;
                $widget['is_premium'] = $widget['is_premium'] ?? false;
                $widget['permissions'] = $widget['permissions'] ?? [];
                $widget['refresh_interval'] = $widget['refresh_interval'] ?? 300; // 5 minutes default
            }
            
            return $widgets;
        });
    }
    
    /**
     * Register a new widget type with validation
     */
    public function register(string $id, array $config): void
    {
        // Validate widget configuration
        $requiredFields = ['name', 'description', 'category', 'size'];
        foreach ($requiredFields as $field) {
            if (!isset($config[$field])) {
                throw new \InvalidArgumentException("Widget configuration missing required field: {$field}");
            }
        }
        
        // Set defaults
        $config['id'] = $id;
        $config['is_premium'] = $config['is_premium'] ?? false;
        $config['permissions'] = $config['permissions'] ?? [];
        $config['refresh_interval'] = $config['refresh_interval'] ?? 300;
        $config['cache_enabled'] = $config['cache_enabled'] ?? true;
        
        $this->registeredWidgets[$id] = $config;
        
        // Clear available widgets cache
        Cache::forget('available_widgets_all');
        foreach ($this->getCategories() as $category) {
            Cache::forget("available_widgets_{$category}");
        }
        
        Log::info("Registered new widget", ['widget_id' => $id, 'category' => $config['category']]);
    }
    
    /**
     * Get widget configuration by ID
     */
    public function getWidget(string $id): ?array
    {
        return $this->registeredWidgets[$id] ?? null;
    }
    
    /**
     * Validate widget configuration
     */
    public function validateWidgetConfig(array $widgets): array
    {
        $errors = [];
        
        foreach ($widgets as $index => $widget) {
            $validator = Validator::make($widget, [
                'id' => 'required|string',
                'position' => 'required|array',
                'position.x' => 'required|integer|min:0',
                'position.y' => 'required|integer|min:0',
                'position.width' => 'required|integer|min:1|max:12',
                'position.height' => 'required|integer|min:1',
                'config' => 'sometimes|array',
                'enabled' => 'boolean'
            ]);
            
            if ($validator->fails()) {
                $errors["widget_{$index}"] = $validator->errors()->toArray();
            }
            
            // Check if widget ID exists
            if (!isset($this->registeredWidgets[$widget['id']])) {
                $errors["widget_{$index}"]['id'] = ["Widget '{$widget['id']}' does not exist"];
            }
        }
        
        return $errors;
    }
    
    /**
     * Get widget categories
     */
    public function getCategories(): array
    {
        return Cache::remember('widget_categories', $this->cacheTimeout, function() {
            $categories = [];
            foreach ($this->registeredWidgets as $widget) {
                $category = $widget['category'];
                if (!in_array($category, $categories)) {
                    $categories[] = $category;
                }
            }
            return $categories;
        });
    }
    
    /**
     * Update widget positions for user
     */
    public function updatePositions(int $userId, array $positions): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return false;
            }
            
            $preferences = $user->dashboard_preferences ?? [];
            $widgets = $preferences['widgets'] ?? [];
            
            // Update positions
            foreach ($widgets as &$widget) {
                if (isset($positions[$widget['id']])) {
                    $widget['position'] = $positions[$widget['id']];
                }
            }
            
            $preferences['widgets'] = $widgets;
            $preferences['updated_at'] = now();
            
            $success = $user->update(['dashboard_preferences' => $preferences]);
            
            if ($success) {
                $this->clearUserCache($userId);
                Log::info("Updated widget positions", ['user_id' => $userId]);
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error("Error updating widget positions", [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Check if user can access widget
     */
    public function canUserAccessWidget(int $userId, string $widgetId): bool
    {
        $widget = $this->getWidget($widgetId);
        if (!$widget) {
            return false;
        }
        
        // Check if widget has permissions
        if (empty($widget['permissions'])) {
            return true;
        }
        
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        
        // Check each permission
        foreach ($widget['permissions'] as $permission) {
            if (!Gate::forUser($user)->allows($permission)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get widget data for specific widget
     */
    public function getWidgetData(string $widgetId, int $userId): array
    {
        $widget = $this->getWidget($widgetId);
        if (!$widget || !$this->canUserAccessWidget($userId, $widgetId)) {
            return [];
        }
        
        $cacheKey = "widget_data_{$widgetId}_{$userId}";
        $cacheTimeout = $widget['refresh_interval'] ?? 300;
        
        if (!($widget['cache_enabled'] ?? true)) {
            return $this->fetchWidgetData($widget, $userId);
        }
        
        return Cache::remember($cacheKey, $cacheTimeout, function() use ($widget, $userId) {
            return $this->fetchWidgetData($widget, $userId);
        });
    }
    
    /**
     * Clear widget cache for user
     */
    public function clearUserCache(int $userId): void
    {
        $cacheKeys = [
            "user_widgets_{$userId}",
        ];
        
        // Clear individual widget data caches
        foreach ($this->registeredWidgets as $widgetId => $widget) {
            $cacheKeys[] = "widget_data_{$widgetId}_{$userId}";
        }
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        Log::info("Cleared widget cache for user", ['user_id' => $userId]);
    }
    
    /**
     * Get widget usage analytics
     */
    public function getUsageAnalytics(): array
    {
        return Cache::remember('widget_usage_analytics', 1800, function() { // 30 minutes
            $analytics = [
                'total_widgets' => count($this->registeredWidgets),
                'categories' => [],
                'popular_widgets' => [],
                'user_adoption' => []
            ];
            
            // Category breakdown
            foreach ($this->getCategories() as $category) {
                $categoryWidgets = array_filter($this->registeredWidgets, function($widget) use ($category) {
                    return $widget['category'] === $category;
                });
                $analytics['categories'][$category] = count($categoryWidgets);
            }
            
            // Get popular widgets (would need actual usage data from database)
            // This is a placeholder implementation
            $analytics['popular_widgets'] = array_keys(array_slice($this->registeredWidgets, 0, 5));
            
            return $analytics;
        });
    }
    
    /**
     * Get default widgets for new users
     */
    private function getDefaultWidgets(): array
    {
        return [
            [
                'id' => 'revenue-kpi',
                'position' => ['x' => 0, 'y' => 0, 'width' => 3, 'height' => 2],
                'config' => [],
                'enabled' => true
            ],
            [
                'id' => 'orders-kpi',
                'position' => ['x' => 3, 'y' => 0, 'width' => 3, 'height' => 2],
                'config' => [],
                'enabled' => true
            ],
            [
                'id' => 'revenue-chart',
                'position' => ['x' => 0, 'y' => 2, 'width' => 6, 'height' => 4],
                'config' => [],
                'enabled' => true
            ],
            [
                'id' => 'recent-orders',
                'position' => ['x' => 6, 'y' => 0, 'width' => 6, 'height' => 6],
                'config' => ['limit' => 10],
                'enabled' => true
            ]
        ];
    }
    
    /**
     * Validate user widgets against registered widgets
     */
    private function validateUserWidgets(array $widgets): array
    {
        $validWidgets = [];
        
        foreach ($widgets as $widget) {
            if (isset($this->registeredWidgets[$widget['id']])) {
                $validWidgets[] = $widget;
            } else {
                Log::warning("Invalid widget found in user configuration", [
                    'widget_id' => $widget['id']
                ]);
            }
        }
        
        return $validWidgets;
    }
    
    /**
     * Fetch widget data from external source
     */
    private function fetchWidgetData(array $widget, int $userId): array
    {
        if (!isset($widget['data_endpoint'])) {
            return ['error' => 'No data endpoint configured'];
        }
        
        try {
            $response = Http::timeout(10)->get($widget['data_endpoint'], [
                'user_id' => $userId,
                'widget_id' => $widget['id']
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::warning("Widget data fetch failed", [
                'widget_id' => $widget['id'],
                'status' => $response->status()
            ]);
            
            return ['error' => 'Failed to fetch widget data'];
        } catch (\Exception $e) {
            Log::error("Widget data fetch exception", [
                'widget_id' => $widget['id'],
                'error' => $e->getMessage()
            ]);
            
            return ['error' => 'Widget data unavailable'];
        }
    }
    
    /**
     * Initialize default widgets
     */
    private function initializeDefaults(): void
    {
        // KPI Widgets
        $this->register('revenue-kpi', [
            'name' => 'Revenue KPI',
            'description' => 'Key revenue metrics and trends',
            'category' => 'kpi',
            'size' => 'small',
            'data_endpoint' => '/api/widgets/revenue-kpi',
            'permissions' => ['view-revenue'],
            'refresh_interval' => 300
        ]);
        
        $this->register('orders-kpi', [
            'name' => 'Orders KPI', 
            'description' => 'Order volume and status metrics',
            'category' => 'kpi',
            'size' => 'small',
            'data_endpoint' => '/api/widgets/orders-kpi',
            'permissions' => ['view-orders'],
            'refresh_interval' => 300
        ]);
        
        $this->register('users-kpi', [
            'name' => 'Users KPI',
            'description' => 'User registration and activity metrics',
            'category' => 'kpi',
            'size' => 'small',
            'data_endpoint' => '/api/widgets/users-kpi',
            'permissions' => ['view-users'],
            'refresh_interval' => 600
        ]);
        
        // Chart Widgets
        $this->register('revenue-chart', [
            'name' => 'Revenue Chart',
            'description' => 'Revenue trends over time',
            'category' => 'charts',
            'size' => 'large',
            'data_endpoint' => '/api/widgets/revenue-chart',
            'permissions' => ['view-revenue'],
            'refresh_interval' => 600
        ]);
        
        $this->register('sales-funnel', [
            'name' => 'Sales Funnel',
            'description' => 'Conversion funnel analysis',
            'category' => 'charts', 
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/sales-funnel',
            'permissions' => ['view-analytics'],
            'refresh_interval' => 900
        ]);
        
        $this->register('traffic-sources', [
            'name' => 'Traffic Sources',
            'description' => 'Website traffic source breakdown',
            'category' => 'charts',
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/traffic-sources',
            'permissions' => ['view-analytics'],
            'refresh_interval' => 1800
        ]);
        
        // Data Widgets
        $this->register('recent-orders', [
            'name' => 'Recent Orders',
            'description' => 'Latest customer orders',
            'category' => 'data',
            'size' => 'large',
            'data_endpoint' => '/api/widgets/recent-orders',
            'permissions' => ['view-orders'],
            'refresh_interval' => 60
        ]);
        
        $this->register('top-products', [
            'name' => 'Top Products',
            'description' => 'Best performing products',
            'category' => 'data',
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/top-products',
            'permissions' => ['view-products'],
            'refresh_interval' => 1800
        ]);
        
        $this->register('customer-activity', [
            'name' => 'Customer Activity',
            'description' => 'Recent customer interactions',
            'category' => 'data',
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/customer-activity',
            'permissions' => ['view-customers'],
            'refresh_interval' => 300
        ]);
        
        // System Widgets
        $this->register('system-health', [
            'name' => 'System Health',
            'description' => 'System performance indicators',
            'category' => 'system',
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/system-health',
            'permissions' => ['view-system'],
            'refresh_interval' => 120
        ]);
        
        $this->register('activity-feed', [
            'name' => 'Activity Feed',
            'description' => 'Recent system activities',
            'category' => 'system',
            'size' => 'large',
            'data_endpoint' => '/api/widgets/activity-feed',
            'permissions' => ['view-system'],
            'refresh_interval' => 60
        ]);
        
        $this->register('storage-usage', [
            'name' => 'Storage Usage',
            'description' => 'Disk space and storage metrics',
            'category' => 'system',
            'size' => 'small',
            'data_endpoint' => '/api/widgets/storage-usage',
            'permissions' => ['view-system'],
            'refresh_interval' => 3600
        ]);
        
        // Notification Widgets
        $this->register('alerts', [
            'name' => 'System Alerts',
            'description' => 'Important system notifications',
            'category' => 'notifications',
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/alerts',
            'permissions' => ['view-alerts'],
            'refresh_interval' => 30
        ]);
        
        $this->register('tasks', [
            'name' => 'Pending Tasks',
            'description' => 'Tasks requiring attention',
            'category' => 'notifications',
            'size' => 'medium',
            'data_endpoint' => '/api/widgets/tasks',
            'permissions' => ['view-tasks'],
            'refresh_interval' => 300
        ]);
    }
}
