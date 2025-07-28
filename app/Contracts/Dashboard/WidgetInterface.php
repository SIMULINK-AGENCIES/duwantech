<?php

namespace App\Contracts\Dashboard;

/**
 * Interface for dashboard widgets
 */
interface WidgetInterface
{
    /**
     * Get widget configuration
     */
    public function getConfig(): array;
    
    /**
     * Get widget data
     */
    public function getData(array $config = []): array;
    
    /**
     * Get widget HTML content
     */
    public function render(array $config = []): string;
    
    /**
     * Validate widget configuration
     */
    public function validateConfig(array $config): array;
    
    /**
     * Get widget configuration schema
     */
    public function getConfigSchema(): array;
    
    /**
     * Handle widget refresh
     */
    public function refresh(array $config = []): array;
    
    /**
     * Check if widget is available for current user
     */
    public function isAvailable(?int $userId = null): bool;
}
