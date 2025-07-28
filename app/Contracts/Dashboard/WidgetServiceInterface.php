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
    public function register(string $id, array $config): void;
    
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
}
