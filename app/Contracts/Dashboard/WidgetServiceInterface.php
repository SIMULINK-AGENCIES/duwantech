<?php

namespace App\Contracts\Dashboard;

interface WidgetServiceInterface
{
    /**
     * Get user's active widgets
     */
    public function getUserWidgets(int $userId): array;
    
    /**
     * Save user's widget configuration
     */
    public function saveUserWidgets(int $userId, array $widgets): bool;
    
    /**
     * Get all available widgets by category
     */
    public function getAvailableWidgets(string $category = null): array;
    
    /**
     * Register a new widget type
     */
    public function register(string $id, array $config, ?array $configSchema = null): void;
    
    /**
     * Get widget configuration by ID
     */
    public function getWidget(string $id): ?array;
    
    /**
     * Validate widget configuration
     */
    public function validateWidgetConfig(array $widgets): array;
    
    /**
     * Get widget categories
     */
    public function getCategories(): array;
    
    /**
     * Update widget positions for user
     */
    public function updatePositions(int $userId, array $positions): bool;
    
    /**
     * Check if user can access widget
     */
    public function canUserAccessWidget(int $userId, string $widgetId): bool;
    
    /**
     * Get widget data for specific widget
     */
    public function getWidgetData(string $widgetId, int $userId): array;
    
    /**
     * Clear widget cache for user
     */
    public function clearUserCache(int $userId): void;
    
    /**
     * Get widget usage analytics
     */
    public function getUsageAnalytics(): array;
    
    /**
     * Get widget configuration schema
     */
    public function getWidgetConfigurationSchema(string $widgetId): ?array;
    
    /**
     * Validate user configuration for a widget
     */
    public function validateUserConfiguration(string $widgetId, array $userConfig): array;
    
    /**
     * Generate form fields for widget configuration
     */
    public function generateConfigurationForm(string $widgetId, array $currentConfig = []): array;
    
    /**
     * Validate widget dependencies
     */
    public function validateDependencies(array $dependencies): array;
    
    /**
     * Detect circular dependencies for a widget
     */
    public function detectCircularDependencies(string $widgetId, array $dependencies): array;
    
    /**
     * Resolve widget loading order based on dependencies
     */
    public function resolveLoadingOrder(array $widgetIds): array;
    
    /**
     * Get widget dependencies (direct and transitive)
     */
    public function getWidgetDependencies(string $widgetId, bool $recursive = false): array;
    
    /**
     * Get widgets that depend on a specific widget (reverse dependencies)
     */
    public function getReverseDependencies(string $widgetId): array;
    
    /**
     * Check if widgets are compatible for co-existence
     */
    public function checkWidgetCompatibility(array $widgetIds): array;
    
    /**
     * Get dependency graph visualization data
     */
    public function getDependencyGraph(): array;
    
    /**
     * Validate user widget selection including dependencies
     */
    public function validateUserWidgetSelection(array $selectedWidgets, int $userId = null): array;
    
    /**
     * Get widgets ordered by dependencies for safe loading
     */
    public function getWidgetsInLoadingOrder(array $widgetIds): array;
    
    /**
     * Check if user can perform specific action on widget
     */
    public function canUserPerformAction(int $userId, string $widgetId, string $action): bool;
    
    /**
     * Get user's accessible widgets with permission filtering
     */
    public function getUserAccessibleWidgets(int $userId, string $action = 'view'): array;
    
    /**
     * Get user's permission level for a widget
     */
    public function getUserWidgetPermissionLevel(int $userId, string $widgetId): int;
    
    /**
     * Bulk permission check for multiple widgets
     */
    public function checkBulkWidgetPermissions(int $userId, array $widgetIds, string $action = 'view'): array;
    
    /**
     * Get permission audit trail for debugging
     */
    public function getWidgetPermissionAuditTrail(int $userId, string $widgetId, string $action = 'view'): array;
    
    /**
     * Clear permission cache for user
     */
    public function clearUserWidgetPermissionCache(int $userId, ?string $widgetId = null): void;
    
    /**
     * Register permission gates for all widgets
     */
    public function registerWidgetPermissionGates(): void;
    
    /**
     * Get widgets filtered by user permissions and dependencies
     */
    public function getUserWidgetsWithDependencies(int $userId, string $action = 'view'): array;
}
