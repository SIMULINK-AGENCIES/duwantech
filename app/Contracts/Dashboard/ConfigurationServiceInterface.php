<?php

namespace App\Contracts\Dashboard;

interface ConfigurationServiceInterface
{
    /**
     * Get user's dashboard configuration
     */
    public function getUserConfiguration(int $userId): array;
    
    /**
     * Save user's dashboard configuration
     */
    public function saveUserConfiguration(int $userId, array $config): bool;
    
    /**
     * Get default configuration for new users
     */
    public function getDefaultConfiguration(): array;
    
    /**
     * Get global dashboard settings
     */
    public function getGlobalSettings(): array;
    
    /**
     * Update global dashboard settings
     */
    public function updateGlobalSettings(array $settings): bool;
    
    /**
     * Get configuration value by key
     */
    public function get(string $key, $default = null);
    
    /**
     * Set configuration value by key
     */
    public function set(string $key, $value): bool;
    
    /**
     * Validate configuration data
     */
    public function validateConfiguration(array $config): array;
    
    /**
     * Reset user configuration to defaults
     */
    public function resetUserConfiguration(int $userId): bool;
    
    /**
     * Export user configuration
     */
    public function exportUserConfiguration(int $userId): array;
    
    /**
     * Import user configuration
     */
    public function importUserConfiguration(int $userId, array $configData): bool;
    
    /**
     * Clear configuration cache
     */
    public function clearCache(int $userId = null): void;
    
    /**
     * Get configuration schema for validation
     */
    public function getConfigurationSchema(): array;
}
