<?php

namespace App\Services\Dashboard;

use App\Contracts\Dashboard\WidgetServiceInterface;
use App\Models\User;
use App\Services\Dashboard\WidgetConfigurationSchema;
use App\Services\Dashboard\WidgetDependencyManager;
use App\Services\Dashboard\WidgetConfigurationManager;
use App\Services\Dashboard\WidgetPermissionManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class WidgetService implements WidgetServiceInterface
{
    private array $registeredWidgets = [];
    private array $widgetCategories = [];
    private array $discoveredWidgets = [];
    private int $cacheTimeout = 3600; // 1 hour
    private ?WidgetConfigurationSchema $configSchema = null;
    private WidgetDependencyManager $dependencyManager;
    private WidgetConfigurationManager $configurationManager;
    private WidgetPermissionManager $permissionManager;
    
    // Widget discovery paths
    private array $discoveryPaths = [
        'app/Widgets',
        'resources/views/admin/widgets',
        'packages/*/widgets',
    ];
    
    public function __construct(
        ?WidgetConfigurationSchema $configSchema = null,
        ?WidgetDependencyManager $dependencyManager = null,
        ?WidgetConfigurationManager $configurationManager = null,
        ?WidgetPermissionManager $permissionManager = null
    ) {
        $this->configSchema = $configSchema ?? new WidgetConfigurationSchema();
        $this->dependencyManager = $dependencyManager ?? new WidgetDependencyManager();
        $this->configurationManager = $configurationManager ?? new WidgetConfigurationManager($this->configSchema);
        $this->permissionManager = $permissionManager ?? new WidgetPermissionManager();
        $this->initializeCategories();
        $this->discoverWidgets();
        $this->initializeDefaults();
    }
    
    /**
     * Initialize widget categories with metadata
     */
    private function initializeCategories(): void
    {
        $this->widgetCategories = [
            'analytics' => [
                'name' => 'Analytics & Reports',
                'description' => 'Data visualization and reporting widgets',
                'icon' => 'chart-bar',
                'color' => 'blue',
                'permissions' => ['admin.analytics.view'],
                'sort_order' => 1
            ],
            'ecommerce' => [
                'name' => 'E-Commerce',
                'description' => 'Sales, orders, and product management widgets',
                'icon' => 'shopping-cart',
                'color' => 'green',
                'permissions' => ['admin.orders.view', 'admin.products.view'],
                'sort_order' => 2
            ],
            'users' => [
                'name' => 'User Management',
                'description' => 'User activity and management widgets',
                'icon' => 'users',
                'color' => 'purple',
                'permissions' => ['admin.users.view'],
                'sort_order' => 3
            ],
            'system' => [
                'name' => 'System Monitoring',
                'description' => 'Server status and system health widgets',
                'icon' => 'server',
                'color' => 'red',
                'permissions' => ['admin.system.view'],
                'sort_order' => 4
            ],
            'content' => [
                'name' => 'Content Management',
                'description' => 'Content creation and management widgets',
                'icon' => 'document-text',
                'color' => 'yellow',
                'permissions' => ['admin.content.view'],
                'sort_order' => 5
            ],
            'communication' => [
                'name' => 'Communication',
                'description' => 'Messaging, notifications, and communication widgets',
                'icon' => 'mail',
                'color' => 'indigo',
                'permissions' => ['admin.notifications.view'],
                'sort_order' => 6
            ],
            'productivity' => [
                'name' => 'Productivity',
                'description' => 'Task management and productivity widgets',
                'icon' => 'clipboard-list',
                'color' => 'gray',
                'permissions' => [],
                'sort_order' => 7
            ],
            'custom' => [
                'name' => 'Custom Widgets',
                'description' => 'User-defined and third-party widgets',
                'icon' => 'puzzle-piece',
                'color' => 'pink',
                'permissions' => ['admin.widgets.manage'],
                'sort_order' => 8
            ]
        ];
    }
    
    /**
     * Discover widgets from configured paths
     */
    private function discoverWidgets(): void
    {
        $this->discoveredWidgets = [];
        
        foreach ($this->discoveryPaths as $path) {
            // Handle wildcard paths
            if (str_contains($path, '*')) {
                $this->discoverWidgetsFromWildcardPath($path);
            } else {
                $this->discoverWidgetsFromPath(base_path($path));
            }
        }
        
        Log::info('Widget discovery completed', [
            'discovered_count' => count($this->discoveredWidgets),
            'discovery_paths' => $this->discoveryPaths
        ]);
    }
    
    /**
     * Discover widgets from a specific path
     */
    private function discoverWidgetsFromPath(string $path): void
    {
        if (!File::exists($path)) {
            return;
        }
        
        // Look for PHP widget classes
        $phpFiles = File::glob($path . '/*.php');
        foreach ($phpFiles as $file) {
            $this->discoverPhpWidget($file);
        }
        
        // Look for JSON widget definitions
        $jsonFiles = File::glob($path . '/*.json');
        foreach ($jsonFiles as $file) {
            $this->discoverJsonWidget($file);
        }
        
        // Look for Blade widget templates
        $bladeFiles = File::glob($path . '/*.blade.php');
        foreach ($bladeFiles as $file) {
            $this->discoverBladeWidget($file);
        }
    }
    
    /**
     * Discover widgets from wildcard paths (e.g., packages/star/widgets)
     */
    private function discoverWidgetsFromWildcardPath(string $wildcardPath): void
    {
        $basePattern = str_replace('*', '', $wildcardPath);
        $directories = File::glob(base_path(str_replace('*', '*', dirname($wildcardPath))));
        
        foreach ($directories as $directory) {
            $fullPath = $directory . '/' . basename($wildcardPath);
            $this->discoverWidgetsFromPath($fullPath);
        }
    }
    
    /**
     * Discover PHP widget class
     */
    private function discoverPhpWidget(string $filePath): void
    {
        try {
            $content = File::get($filePath);
            $className = $this->extractClassNameFromFile($content);
            
            if ($className && class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                
                // Check if class implements widget interface
                if ($reflection->implementsInterface('App\Contracts\Dashboard\WidgetInterface')) {
                    $instance = $reflection->newInstance();
                    $config = $instance->getConfig();
                    
                    $this->discoveredWidgets[$config['id']] = array_merge($config, [
                        'type' => 'php',
                        'class' => $className,
                        'file_path' => $filePath,
                        'discovered_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to discover PHP widget', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Discover JSON widget definition
     */
    private function discoverJsonWidget(string $filePath): void
    {
        try {
            $content = File::get($filePath);
            $config = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($config['id'])) {
                $this->discoveredWidgets[$config['id']] = array_merge($config, [
                    'type' => 'json',
                    'file_path' => $filePath,
                    'discovered_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to discover JSON widget', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Discover Blade widget template
     */
    private function discoverBladeWidget(string $filePath): void
    {
        try {
            $content = File::get($filePath);
            $config = $this->extractWidgetConfigFromBlade($content);
            
            if ($config && isset($config['id'])) {
                $this->discoveredWidgets[$config['id']] = array_merge($config, [
                    'type' => 'blade',
                    'template' => str_replace([resource_path('views/'), '.blade.php'], ['', ''], $filePath),
                    'file_path' => $filePath,
                    'discovered_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to discover Blade widget', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Extract class name from PHP file content
     */
    private function extractClassNameFromFile(string $content): ?string
    {
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches) &&
            preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            return $namespaceMatches[1] . '\\' . $classMatches[1];
        }
        
        return null;
    }
    
    /**
     * Extract widget configuration from Blade template comments
     */
    private function extractWidgetConfigFromBlade(string $content): ?array
    {
        if (preg_match('/{{--\s*@widget\s*({.*?})\s*--}}/', $content, $matches)) {
            return json_decode($matches[1], true);
        }
        
        return null;
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
     * Get all available widgets with enhanced filtering and permissions
     */
    public function getAvailableWidgets(?string $category = null, ?int $userId = null): array
    {
        $userId = $userId ?? auth()->id();
        
        $widgets = array_merge($this->registeredWidgets, $this->discoveredWidgets);
        
        // Filter by category if specified
        if ($category) {
            $widgets = array_filter($widgets, function($widget) use ($category) {
                return ($widget['category'] ?? 'general') === $category;
            });
        }
        
        // Add enhanced metadata
        foreach ($widgets as $id => &$widget) {
            $widget['id'] = $id;
            $widget['is_premium'] = $widget['is_premium'] ?? false;
            $widget['permissions'] = $widget['permissions'] ?? [];
            $widget['refresh_interval'] = $widget['refresh_interval'] ?? 300;
            $widget['category_info'] = $this->getCategoryInfo($widget['category'] ?? 'general');
            $widget['is_discovered'] = isset($this->discoveredWidgets[$id]);
            $widget['supports_config'] = $this->widgetSupportsConfiguration($widget);
            $widget['last_updated'] = $widget['last_updated'] ?? $widget['discovered_at'] ?? now();
        }
        
        return $widgets;
    }
    
    /**
     * Enhanced widget registration with discovery integration and dependency management
     */
    public function register(string $id, array $config, ?array $configSchema = null): void
    {
        // Validate widget configuration with enhanced schema
        $this->validateWidgetRegistration($id, $config);
        
        // Validate configuration schema if provided
        if ($configSchema !== null) {
            $validationErrors = $this->configSchema->validateConfigurationSchema($configSchema);
            if (!empty($validationErrors)) {
                throw new \InvalidArgumentException(
                    "Invalid configuration schema for widget {$id}: " . 
                    implode(', ', $validationErrors)
                );
            }
        }
        
        // Set enhanced defaults
        $config = array_merge([
            'id' => $id,
            'version' => '1.0.0',
            'is_premium' => false,
            'permissions' => [],
            'refresh_interval' => 300,
            'cache_enabled' => true,
            'supports_resize' => true,
            'supports_config' => false,
            'min_size' => ['width' => 1, 'height' => 1],
            'max_size' => ['width' => 12, 'height' => 8],
            'default_size' => ['width' => 4, 'height' => 3],
            'tags' => [],
            'dependencies' => [],
            'author' => 'System',
            'status' => 'active',
            'registered_at' => now(),
            'config_schema' => $configSchema,
        ], $config);
        
        // Validate dependencies before registration
        if (!empty($config['dependencies'])) {
            $dependencyErrors = $this->validateDependencies($config['dependencies']);
            if (!empty($dependencyErrors)) {
                throw new \InvalidArgumentException("Widget dependency validation failed: " . implode(', ', $dependencyErrors));
            }
            
            // Check for circular dependencies
            $circularDeps = $this->detectCircularDependencies($id, $config['dependencies']);
            if (!empty($circularDeps)) {
                throw new \InvalidArgumentException("Circular dependency detected: " . implode(', ', $circularDeps));
            }
        }
        
        // Validate category exists
        if (!isset($this->widgetCategories[$config['category']])) {
            throw new \InvalidArgumentException("Invalid widget category: {$config['category']}");
        }
        
        $this->registeredWidgets[$id] = $config;
        
        // Update dependency manager with new widget set
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        
        // Update permission manager with new widget set
        $this->permissionManager->setRegisteredWidgets($this->registeredWidgets);
        
        Log::info("Registered enhanced widget", [
            'widget_id' => $id, 
            'category' => $config['category'],
            'version' => $config['version'],
            'has_schema' => $configSchema !== null,
            'dependencies' => $config['dependencies'],
            'permissions' => $config['permissions']
        ]);
    }
    
    /**
     * Get widget configuration by ID
     */
    public function getWidget(string $id): ?array
    {
        return $this->registeredWidgets[$id] ?? $this->discoveredWidgets[$id] ?? null;
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
            
            $preferences = $user->dashboard_preferences ?? [];
            $preferences['widgets'] = $widgets;
            $preferences['updated_at'] = now();
            
            $success = $user->update(['dashboard_preferences' => $preferences]);
            
            if ($success) {
                Cache::forget("user_widgets_{$userId}");
                
                Log::info("Successfully saved user widgets", [
                    'user_id' => $userId,
                    'widget_count' => count($widgets)
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
     * Get widget categories with enhanced metadata
     */
    public function getCategories(): array
    {
        $categories = $this->widgetCategories;
        
        // Add widget counts to each category
        foreach ($categories as $key => &$category) {
            $category['widget_count'] = count($this->getAvailableWidgets($key));
            $category['key'] = $key;
        }
        
        return $categories;
    }
    
    /**
     * Get category information
     */
    public function getCategoryInfo(string $category): array
    {
        return $this->widgetCategories[$category] ?? [
            'name' => ucfirst($category),
            'description' => "Widgets in the {$category} category",
            'icon' => 'puzzle-piece',
            'color' => 'gray',
            'permissions' => [],
            'sort_order' => 999
        ];
    }
    
    /**
     * Validate widget registration with enhanced rules
     */
    private function validateWidgetRegistration(string $id, array $config): void
    {
        $validator = Validator::make($config, [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|string',
            'size' => 'required|array',
            'size.width' => 'required|integer|min:1|max:12',
            'size.height' => 'required|integer|min:1|max:8',
        ]);
        
        if ($validator->fails()) {
            throw new \InvalidArgumentException(
                "Widget registration validation failed: " . 
                implode(', ', $validator->errors()->all())
            );
        }
        
        // Check for duplicate IDs
        if (isset($this->registeredWidgets[$id]) || isset($this->discoveredWidgets[$id])) {
            throw new \InvalidArgumentException("Widget with ID '{$id}' already exists");
        }
    }
    
    /**
     * Check if widget supports configuration
     */
    private function widgetSupportsConfiguration(array $widget): bool
    {
        return $widget['supports_config'] ?? 
               isset($widget['config_schema']) ?? 
               ($widget['type'] === 'php' && method_exists($widget['class'] ?? '', 'getConfigSchema'));
    }
    
    /**
     * Initialize default widgets
     */
    private function initializeDefaults(): void
    {
        // This would load default widget configurations
    }
    
    /**
     * Get default widgets for new users
     */
    private function getDefaultWidgets(): array
    {
        return [
            [
                'id' => 'user_activity',
                'position' => ['x' => 0, 'y' => 0, 'width' => 6, 'height' => 4],
                'config' => [],
                'enabled' => true
            ],
            [
                'id' => 'sales_overview',
                'position' => ['x' => 6, 'y' => 0, 'width' => 6, 'height' => 4],
                'config' => [],
                'enabled' => true
            ]
        ];
    }
    
    /**
     * Validate user widgets
     */
    private function validateUserWidgets(array $widgets): array
    {
        $validated = [];
        
        foreach ($widgets as $widget) {
            if (isset($widget['id']) && $this->getWidget($widget['id'])) {
                $validated[] = $widget;
            }
        }
        
        return $validated;
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
            if (!$this->getWidget($widget['id'])) {
                $errors["widget_{$index}"]['id'] = ["Widget '{$widget['id']}' does not exist"];
            }
        }
        
        return $errors;
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
            
            // Update positions for existing widgets
            foreach ($widgets as &$widget) {
                if (isset($positions[$widget['id']])) {
                    $widget['position'] = $positions[$widget['id']];
                }
            }
            
            $preferences['widgets'] = $widgets;
            $success = $user->update(['dashboard_preferences' => $preferences]);
            
            if ($success) {
                Cache::forget("user_widgets_{$userId}");
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
        
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        
        // Use comprehensive permission checking
        return $this->permissionManager->canUserAccessWidget($user, $widgetId, 'view');
    }
    
    /**
     * Get widget data for specific widget
     */
    public function getWidgetData(string $widgetId, int $userId): array
    {
        $widget = $this->getWidget($widgetId);
        
        if (!$widget) {
            return [];
        }
        
        // If it's a PHP widget, call its getData method
        if ($widget['type'] === 'php' && class_exists($widget['class'])) {
            try {
                $instance = new $widget['class']();
                return $instance->getData();
            } catch (\Exception $e) {
                Log::error("Error getting widget data", [
                    'widget_id' => $widgetId,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        }
        
        // Return default data for other widget types
        return [
            'widget_id' => $widgetId,
            'data' => [],
            'last_updated' => now(),
        ];
    }
    
    /**
     * Clear widget cache for user
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget("user_widgets_{$userId}");
        
        // Clear related caches
        foreach ($this->widgetCategories as $categoryKey => $category) {
            Cache::forget("available_widgets_{$categoryKey}_{$userId}");
        }
    }
    
    /**
     * Get widget usage analytics
     */
    public function getUsageAnalytics(): array
    {
        // This would typically query the database for usage statistics
        // For now, return basic analytics
        return [
            'total_registered_widgets' => count($this->registeredWidgets),
            'total_discovered_widgets' => count($this->discoveredWidgets),
            'total_categories' => count($this->widgetCategories),
            'most_used_widgets' => [],
            'category_distribution' => [],
            'generated_at' => now(),
        ];
    }
    
    /**
     * Validate widget dependencies
     */
    public function validateDependencies(array $dependencies): array
    {
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        return $this->dependencyManager->validateDependencies($dependencies);
    }
    
    /**
     * Generate configuration form for a widget
     */
    public function generateConfigurationForm(string $widgetId, array $currentConfig = []): array
    {
        if (!isset($this->registeredWidgets[$widgetId])) {
            throw new \InvalidArgumentException("Widget '{$widgetId}' not found");
        }
        
        $form = $this->configurationManager->getConfigurationForm($widgetId);
        
        // Merge current config values into form fields
        if (!empty($currentConfig)) {
            foreach ($form['fields'] as &$field) {
                if (isset($currentConfig[$field['name']])) {
                    $field['value'] = $currentConfig[$field['name']];
                }
            }
        }
        
        return $form;
    }
    
    /**
     * Get widget configuration schema
     */
    public function getWidgetConfigurationSchema(string $widgetId): ?array
    {
        if (!isset($this->registeredWidgets[$widgetId])) {
            return null;
        }
        
        $widget = $this->registeredWidgets[$widgetId];
        return $widget['config_schema'] ?? null;
    }
    
    /**
     * Validate user configuration for a widget
     */
    public function validateUserConfiguration(string $widgetId, array $userConfig): array
    {
        if (!isset($this->registeredWidgets[$widgetId])) {
            return [
                'valid' => false,
                'errors' => ["Widget '{$widgetId}' not found"]
            ];
        }
        
        return $this->configurationManager->validateConfiguration($widgetId, $userConfig);
    }
    
    /**
     * Detect circular dependencies for a widget
     */
    public function detectCircularDependencies(string $widgetId, array $dependencies): array
    {
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        return $this->dependencyManager->detectCircularDependencies($widgetId, $dependencies);
    }
    
    /**
     * Resolve widget loading order based on dependencies
     */
    public function resolveLoadingOrder(array $widgetIds): array
    {
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        return $this->dependencyManager->resolveLoadingOrder($widgetIds);
    }
    
    /**
     * Get widget dependencies (direct and transitive)
     */
    public function getWidgetDependencies(string $widgetId, bool $recursive = false): array
    {
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        return $this->dependencyManager->getWidgetDependencies($widgetId, $recursive);
    }
    
    /**
     * Get widgets that depend on a specific widget (reverse dependencies)
     */
    public function getReverseDependencies(string $widgetId): array
    {
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        return $this->dependencyManager->getReverseDependencies($widgetId);
    }
    
    /**
     * Check if widgets are compatible for co-existence
     */
    public function checkWidgetCompatibility(array $widgetIds): array
    {
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        return $this->dependencyManager->checkCompatibility($widgetIds);
    }
    
    /**
     * Get dependency graph visualization data
     */
    public function getDependencyGraph(): array
    {
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        return $this->dependencyManager->getDependencyGraph();
    }
    
    /**
     * Validate user widget selection including dependencies
     */
    public function validateUserWidgetSelection(array $selectedWidgets, int $userId = null): array
    {
        $this->dependencyManager->setRegisteredWidgets($this->registeredWidgets);
        return $this->dependencyManager->validateUserWidgetSelection($selectedWidgets, $userId);
    }
    
    /**
     * Get widgets ordered by dependencies for safe loading
     */
    public function getWidgetsInLoadingOrder(array $widgetIds): array
    {
        $loadingOrder = $this->resolveLoadingOrder($widgetIds);
        $orderedWidgets = [];
        
        foreach ($loadingOrder as $widgetId) {
            if (isset($this->registeredWidgets[$widgetId])) {
                $orderedWidgets[] = $this->registeredWidgets[$widgetId];
            }
        }
        
        return $orderedWidgets;
    }
    
    /**
     * Check if user can perform specific action on widget
     */
    public function canUserPerformAction(int $userId, string $widgetId, string $action): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        
        return $this->permissionManager->canUserAccessWidget($user, $widgetId, $action);
    }
    
    /**
     * Get user's accessible widgets with permission filtering
     */
    public function getUserAccessibleWidgets(int $userId, string $action = 'view'): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }
        
        return $this->permissionManager->getAccessibleWidgets($user, $action);
    }
    
    /**
     * Get user's permission level for a widget
     */
    public function getUserWidgetPermissionLevel(int $userId, string $widgetId): int
    {
        $user = User::find($userId);
        if (!$user) {
            return WidgetPermissionManager::PERMISSION_NONE;
        }
        
        return $this->permissionManager->getUserWidgetPermissionLevel($user, $widgetId);
    }
    
    /**
     * Bulk permission check for multiple widgets
     */
    public function checkBulkWidgetPermissions(int $userId, array $widgetIds, string $action = 'view'): array
    {
        $user = User::find($userId);
        if (!$user) {
            return array_fill_keys($widgetIds, false);
        }
        
        return $this->permissionManager->checkBulkPermissions($user, $widgetIds, $action);
    }
    
    /**
     * Get permission audit trail for debugging
     */
    public function getWidgetPermissionAuditTrail(int $userId, string $widgetId, string $action = 'view'): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [
                'result' => false,
                'reason' => 'User not found',
                'user_id' => $userId,
                'widget_id' => $widgetId,
                'action' => $action
            ];
        }
        
        return $this->permissionManager->getPermissionAuditTrail($user, $widgetId, $action);
    }
    
    /**
     * Clear permission cache for user
     */
    public function clearUserWidgetPermissionCache(int $userId, ?string $widgetId = null): void
    {
        $user = User::find($userId);
        if ($user) {
            $this->permissionManager->clearUserPermissionCache($user, $widgetId);
        }
    }
    
    /**
     * Register permission gates for all widgets
     */
    public function registerWidgetPermissionGates(): void
    {
        $this->permissionManager->registerPermissionGates();
    }
    
    /**
     * Get widgets filtered by user permissions and dependencies
     */
    public function getUserWidgetsWithDependencies(int $userId, string $action = 'view'): array
    {
        $accessibleWidgets = $this->getUserAccessibleWidgets($userId, $action);
        $widgetIds = array_keys($accessibleWidgets);
        
        // Validate that all dependencies are also accessible
        $validatedWidgets = [];
        
        foreach ($accessibleWidgets as $widgetId => $widget) {
            $dependencies = $this->getWidgetDependencies($widgetId, true);
            $allDependenciesAccessible = true;
            
            foreach ($dependencies as $depId) {
                if (!$this->canUserPerformAction($userId, $depId, $action)) {
                    $allDependenciesAccessible = false;
                    break;
                }
            }
            
            if ($allDependenciesAccessible) {
                $validatedWidgets[$widgetId] = $widget;
            }
        }
        
        return $validatedWidgets;
    }
}
