<?php

namespace App\Services\Dashboard;

use App\Contracts\Dashboard\ConfigurationServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ConfigurationService implements ConfigurationServiceInterface
{
    private int $cacheTimeout = 3600; // 1 hour
    private array $globalSettings = [];
    
    public function __construct()
    {
        $this->loadGlobalSettings();
    }
    
    /**
     * Get user's dashboard configuration with caching
     */
    public function getUserConfiguration(int $userId): array
    {
        $cacheKey = "user_config_{$userId}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($userId) {
            $user = User::find($userId);
            if (!$user || !$user->dashboard_preferences) {
                Log::info("Loading default configuration for user", ['user_id' => $userId]);
                return $this->getDefaultConfiguration();
            }
            
            $preferences = $user->dashboard_preferences;
            $config = array_merge(
                $this->getDefaultConfiguration(),
                $preferences['configuration'] ?? []
            );
            
            Log::info("Loaded user configuration from cache", [
                'user_id' => $userId,
                'config_keys' => array_keys($config)
            ]);
            
            return $config;
        });
    }
    
    /**
     * Save user's dashboard configuration with validation
     */
    public function saveUserConfiguration(int $userId, array $config): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                Log::warning("User not found when saving configuration", ['user_id' => $userId]);
                return false;
            }
            
            // Validate configuration
            $validationResult = $this->validateConfiguration($config);
            if (!empty($validationResult)) {
                Log::warning("Configuration validation failed", [
                    'user_id' => $userId,
                    'errors' => $validationResult
                ]);
                return false;
            }
            
            $preferences = $user->dashboard_preferences ?? [];
            $preferences['configuration'] = $config;
            $preferences['updated_at'] = now();
            
            $success = $user->update(['dashboard_preferences' => $preferences]);
            
            if ($success) {
                // Clear user cache
                $this->clearCache($userId);
                
                Log::info("Successfully saved user configuration", [
                    'user_id' => $userId,
                    'config_keys' => array_keys($config)
                ]);
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error("Error saving user configuration", [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get default configuration for new users
     */
    public function getDefaultConfiguration(): array
    {
        return [
            // Layout Settings
            'layout' => [
                'sidebar_collapsed' => false,
                'sidebar_mini' => false,
                'header_fixed' => true,
                'footer_fixed' => false,
                'layout_boxed' => false,
                'sidebar_position' => 'left',
                'header_position' => 'top'
            ],
            
            // Display Settings
            'display' => [
                'items_per_page' => 25,
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
                'timezone' => 'UTC',
                'language' => 'en',
                'currency' => 'USD',
                'number_format' => 'en-US'
            ],
            
            // Notification Settings
            'notifications' => [
                'desktop_enabled' => true,
                'email_enabled' => true,
                'sound_enabled' => true,
                'show_previews' => true,
                'auto_mark_read' => false,
                'frequency' => 'realtime',
                'types' => [
                    'orders' => true,
                    'payments' => true,
                    'inventory' => true,
                    'system' => true,
                    'users' => false
                ]
            ],
            
            // Dashboard Settings
            'dashboard' => [
                'auto_refresh' => true,
                'refresh_interval' => 300, // 5 minutes
                'show_welcome' => true,
                'compact_mode' => false,
                'animation_enabled' => true,
                'grid_snap' => true,
                'widget_border' => true
            ],
            
            // Performance Settings
            'performance' => [
                'lazy_loading' => true,
                'cache_enabled' => true,
                'animation_reduced' => false,
                'preload_data' => true,
                'batch_requests' => true
            ],
            
            // Privacy Settings
            'privacy' => [
                'analytics_enabled' => true,
                'error_reporting' => true,
                'usage_tracking' => true,
                'session_sharing' => false
            ],
            
            // Accessibility Settings
            'accessibility' => [
                'high_contrast' => false,
                'large_text' => false,
                'keyboard_navigation' => true,
                'screen_reader' => false,
                'focus_indicators' => true
            ]
        ];
    }
    
    /**
     * Get global dashboard settings with caching
     */
    public function getGlobalSettings(): array
    {
        return Cache::remember('global_dashboard_settings', $this->cacheTimeout, function() {
            return $this->globalSettings;
        });
    }
    
    /**
     * Update global dashboard settings
     */
    public function updateGlobalSettings(array $settings): bool
    {
        try {
            // Validate settings structure
            $validator = Validator::make($settings, [
                'maintenance_mode' => 'boolean',
                'max_widgets_per_user' => 'integer|min:1|max:50',
                'session_timeout' => 'integer|min:300|max:86400',
                'auto_save_interval' => 'integer|min:30|max:3600',
                'theme_switching_enabled' => 'boolean',
                'custom_css_enabled' => 'boolean'
            ]);
            
            if ($validator->fails()) {
                Log::warning("Global settings validation failed", [
                    'errors' => $validator->errors()->toArray()
                ]);
                return false;
            }
            
            $this->globalSettings = array_merge($this->globalSettings, $settings);
            
            // Clear global settings cache
            Cache::forget('global_dashboard_settings');
            
            Log::info("Updated global dashboard settings", [
                'updated_keys' => array_keys($settings)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Error updating global settings", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get configuration value by key with dot notation support
     */
    public function get(string $key, $default = null)
    {
        $config = $this->getGlobalSettings();
        
        if (strpos($key, '.') === false) {
            return $config[$key] ?? $default;
        }
        
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        
        return $value;
    }
    
    /**
     * Set configuration value by key with dot notation support
     */
    public function set(string $key, $value): bool
    {
        try {
            if (strpos($key, '.') === false) {
                $this->globalSettings[$key] = $value;
            } else {
                $keys = explode('.', $key);
                $config = &$this->globalSettings;
                
                for ($i = 0; $i < count($keys) - 1; $i++) {
                    $segment = $keys[$i];
                    if (!isset($config[$segment]) || !is_array($config[$segment])) {
                        $config[$segment] = [];
                    }
                    $config = &$config[$segment];
                }
                
                $config[end($keys)] = $value;
            }
            
            // Clear cache
            Cache::forget('global_dashboard_settings');
            
            Log::info("Set configuration value", ['key' => $key]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Error setting configuration value", [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Validate configuration data against schema
     */
    public function validateConfiguration(array $config): array
    {
        $schema = $this->getConfigurationSchema();
        $errors = [];
        
        foreach ($schema as $section => $rules) {
            if (!isset($config[$section])) {
                continue;
            }
            
            $validator = Validator::make(
                [$section => $config[$section]],
                [$section => $rules]
            );
            
            if ($validator->fails()) {
                $errors[$section] = $validator->errors()->toArray()[$section] ?? [];
            }
        }
        
        return $errors;
    }
    
    /**
     * Reset user configuration to defaults
     */
    public function resetUserConfiguration(int $userId): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return false;
            }
            
            $preferences = $user->dashboard_preferences ?? [];
            $preferences['configuration'] = $this->getDefaultConfiguration();
            $preferences['reset_at'] = now();
            
            $success = $user->update(['dashboard_preferences' => $preferences]);
            
            if ($success) {
                $this->clearCache($userId);
                Log::info("Reset user configuration to defaults", ['user_id' => $userId]);
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error("Error resetting user configuration", [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Export user configuration as JSON
     */
    public function exportUserConfiguration(int $userId): array
    {
        $config = $this->getUserConfiguration($userId);
        
        return [
            'version' => '1.0',
            'exported_at' => now()->toISOString(),
            'user_id' => $userId,
            'configuration' => $config,
            'schema_version' => $this->getSchemaVersion()
        ];
    }
    
    /**
     * Import user configuration from JSON
     */
    public function importUserConfiguration(int $userId, array $configData): bool
    {
        try {
            // Validate import data structure
            $validator = Validator::make($configData, [
                'version' => 'required|string',
                'configuration' => 'required|array',
                'schema_version' => 'sometimes|string'
            ]);
            
            if ($validator->fails()) {
                Log::warning("Invalid configuration import data", [
                    'user_id' => $userId,
                    'errors' => $validator->errors()->toArray()
                ]);
                return false;
            }
            
            $config = $configData['configuration'];
            
            // Merge with defaults to ensure all required keys exist
            $mergedConfig = array_merge($this->getDefaultConfiguration(), $config);
            
            return $this->saveUserConfiguration($userId, $mergedConfig);
        } catch (\Exception $e) {
            Log::error("Error importing user configuration", [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Clear configuration cache
     */
    public function clearCache(int $userId = null): void
    {
        if ($userId) {
            Cache::forget("user_config_{$userId}");
            Log::info("Cleared configuration cache for user", ['user_id' => $userId]);
        } else {
            Cache::forget('global_dashboard_settings');
            // Clear all user configuration caches (would need to track user IDs in production)
            Log::info("Cleared global configuration cache");
        }
    }
    
    /**
     * Get configuration schema for validation
     */
    public function getConfigurationSchema(): array
    {
        return [
            'layout' => 'array',
            'display' => 'array',
            'notifications' => 'array',
            'dashboard' => 'array',
            'performance' => 'array',
            'privacy' => 'array',
            'accessibility' => 'array'
        ];
    }
    
    /**
     * Get current schema version
     */
    private function getSchemaVersion(): string
    {
        return '1.0.0';
    }
    
    /**
     * Load global settings from configuration
     */
    private function loadGlobalSettings(): void
    {
        $this->globalSettings = [
            'maintenance_mode' => false,
            'max_widgets_per_user' => 20,
            'session_timeout' => 7200, // 2 hours
            'auto_save_interval' => 300, // 5 minutes
            'theme_switching_enabled' => true,
            'custom_css_enabled' => true,
            'widget_library_enabled' => true,
            'export_import_enabled' => true,
            'analytics_enabled' => true,
            'debug_mode' => false,
            'cache_enabled' => true,
            'logging_level' => 'info',
            'backup_enabled' => true,
            'backup_interval' => 86400, // 24 hours
            'security' => [
                'csrf_protection' => true,
                'xss_protection' => true,
                'content_security_policy' => true,
                'session_regeneration' => true
            ],
            'features' => [
                'real_time_updates' => true,
                'push_notifications' => true,
                'offline_mode' => false,
                'mobile_app' => false,
                'api_access' => true,
                'webhooks' => false
            ],
            'limits' => [
                'max_dashboard_size' => 100, // widgets
                'max_custom_themes' => 5,
                'max_export_size' => 10485760, // 10MB
                'rate_limit_requests' => 1000,
                'rate_limit_period' => 3600 // 1 hour
            ]
        ];
    }
}
