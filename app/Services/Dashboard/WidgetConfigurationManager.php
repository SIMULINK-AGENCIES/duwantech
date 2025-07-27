<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Contracts\Dashboard\WidgetServiceInterface;

class WidgetConfigurationManager
{
    private WidgetConfigurationSchema $schema;
    private int $cacheTimeout = 3600;
    
    public function __construct(WidgetConfigurationSchema $schema)
    {
        $this->schema = $schema;
    }
    
    /**
     * Get widget configuration schema
     */
    public function getWidgetConfigSchema(string $widgetId): ?array
    {
        $cacheKey = "widget_config_schema_{$widgetId}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($widgetId) {
            // Try to get from widget service first
            $widgetService = app(WidgetServiceInterface::class);
            $widget = $widgetService->getWidget($widgetId);
            
            if (!$widget) {
                return null;
            }
            
            // Return custom schema if defined, otherwise generate default
            return $widget['config_schema'] ?? $this->schema->generateDefaultConfigSchema();
        });
    }
    
    /**
     * Validate widget configuration
     */
    public function validateConfiguration(string $widgetId, array $config): array
    {
        $schema = $this->getWidgetConfigSchema($widgetId);
        
        if (!$schema) {
            return ['widget' => ['Widget not found or does not support configuration']];
        }
        
        return $this->schema->validateUserWidgetConfig($schema, $config);
    }
    
    /**
     * Get user's widget configuration
     */
    public function getUserWidgetConfig(int $userId, string $widgetId): array
    {
        $cacheKey = "user_widget_config_{$userId}_{$widgetId}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($userId, $widgetId) {
            $user = User::find($userId);
            
            if (!$user || !$user->dashboard_preferences) {
                return $this->getDefaultConfiguration($widgetId);
            }
            
            $preferences = $user->dashboard_preferences;
            $widgets = $preferences['widgets'] ?? [];
            
            // Find the widget configuration
            foreach ($widgets as $widget) {
                if ($widget['id'] === $widgetId) {
                    return array_merge(
                        $this->getDefaultConfiguration($widgetId),
                        $widget['config'] ?? []
                    );
                }
            }
            
            return $this->getDefaultConfiguration($widgetId);
        });
    }
    
    /**
     * Save user's widget configuration
     */
    public function saveUserWidgetConfig(int $userId, string $widgetId, array $config): bool
    {
        try {
            // Validate configuration first
            $errors = $this->validateConfiguration($widgetId, $config);
            if (!empty($errors)) {
                Log::warning('Widget configuration validation failed', [
                    'user_id' => $userId,
                    'widget_id' => $widgetId,
                    'errors' => $errors
                ]);
                return false;
            }
            
            $user = User::find($userId);
            if (!$user) {
                return false;
            }
            
            $preferences = $user->dashboard_preferences ?? [];
            $widgets = $preferences['widgets'] ?? [];
            
            // Find and update the widget configuration
            $widgetFound = false;
            foreach ($widgets as &$widget) {
                if ($widget['id'] === $widgetId) {
                    $widget['config'] = array_merge($widget['config'] ?? [], $config);
                    $widget['config_updated_at'] = now();
                    $widgetFound = true;
                    break;
                }
            }
            
            // If widget not found in user's widgets, add it
            if (!$widgetFound) {
                $widgetService = app(WidgetServiceInterface::class);
                $widgetInfo = $widgetService->getWidget($widgetId);
                
                if (!$widgetInfo) {
                    return false;
                }
                
                $widgets[] = [
                    'id' => $widgetId,
                    'position' => [
                        'x' => 0,
                        'y' => 0,
                        'width' => $widgetInfo['size']['width'],
                        'height' => $widgetInfo['size']['height']
                    ],
                    'config' => $config,
                    'enabled' => true,
                    'config_updated_at' => now()
                ];
            }
            
            $preferences['widgets'] = $widgets;
            $preferences['updated_at'] = now();
            
            $success = $user->update(['dashboard_preferences' => $preferences]);
            
            if ($success) {
                // Clear related caches
                Cache::forget("user_widget_config_{$userId}_{$widgetId}");
                Cache::forget("user_widgets_{$userId}");
                
                Log::info('Widget configuration saved successfully', [
                    'user_id' => $userId,
                    'widget_id' => $widgetId
                ]);
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error('Error saving widget configuration', [
                'user_id' => $userId,
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get default configuration for a widget
     */
    public function getDefaultConfiguration(string $widgetId): array
    {
        $schema = $this->getWidgetConfigSchema($widgetId);
        
        if (!$schema) {
            return [];
        }
        
        $defaultConfig = [];
        
        foreach ($schema as $fieldName => $fieldSchema) {
            if (isset($fieldSchema['default'])) {
                $defaultConfig[$fieldName] = $fieldSchema['default'];
            }
        }
        
        return $defaultConfig;
    }
    
    /**
     * Reset widget configuration to defaults
     */
    public function resetWidgetConfig(int $userId, string $widgetId): bool
    {
        $defaultConfig = $this->getDefaultConfiguration($widgetId);
        return $this->saveUserWidgetConfig($userId, $widgetId, $defaultConfig);
    }
    
    /**
     * Get configuration form fields for frontend
     */
    public function getConfigurationForm(string $widgetId): array
    {
        $schema = $this->getWidgetConfigSchema($widgetId);
        
        if (!$schema) {
            return [];
        }
        
        $formFields = [];
        
        foreach ($schema as $fieldName => $fieldSchema) {
            $formFields[] = [
                'name' => $fieldName,
                'type' => $fieldSchema['type'],
                'label' => $fieldSchema['label'],
                'required' => $fieldSchema['required'] ?? false,
                'default' => $fieldSchema['default'] ?? null,
                'options' => $fieldSchema['options'] ?? null,
                'help_text' => $fieldSchema['help_text'] ?? null,
                'placeholder' => $fieldSchema['placeholder'] ?? null,
                'validation' => $this->getFieldValidationRules($fieldSchema),
                'attributes' => $this->getFieldAttributes($fieldSchema)
            ];
        }
        
        return $formFields;
    }
    
    /**
     * Get validation rules for a field
     */
    private function getFieldValidationRules(array $fieldSchema): array
    {
        $rules = [];
        $type = $fieldSchema['type'];
        
        if ($fieldSchema['required'] ?? false) {
            $rules[] = 'required';
        }
        
        switch ($type) {
            case 'text':
            case 'textarea':
                if (isset($fieldSchema['min_length'])) {
                    $rules[] = "min:{$fieldSchema['min_length']}";
                }
                if (isset($fieldSchema['max_length'])) {
                    $rules[] = "max:{$fieldSchema['max_length']}";
                }
                if (isset($fieldSchema['pattern'])) {
                    $rules[] = "regex:{$fieldSchema['pattern']}";
                }
                break;
                
            case 'number':
            case 'range':
                $rules[] = 'numeric';
                if (isset($fieldSchema['min'])) {
                    $rules[] = "min:{$fieldSchema['min']}";
                }
                if (isset($fieldSchema['max'])) {
                    $rules[] = "max:{$fieldSchema['max']}";
                }
                break;
                
            case 'boolean':
                $rules[] = 'boolean';
                break;
                
            case 'select':
                if (isset($fieldSchema['options'])) {
                    $validValues = array_column($fieldSchema['options'], 'value');
                    $rules[] = 'in:' . implode(',', $validValues);
                }
                break;
                
            case 'multiselect':
                $rules[] = 'array';
                if (isset($fieldSchema['options'])) {
                    $validValues = array_column($fieldSchema['options'], 'value');
                    $rules[] = 'array';
                    foreach ($validValues as $value) {
                        $rules[] = "in:{$value}";
                    }
                }
                break;
                
            case 'color':
                $rules[] = 'regex:/^#[0-9A-Fa-f]{6}$/';
                break;
                
            case 'date':
                $rules[] = 'date';
                if (isset($fieldSchema['min_date'])) {
                    $rules[] = "after:{$fieldSchema['min_date']}";
                }
                if (isset($fieldSchema['max_date'])) {
                    $rules[] = "before:{$fieldSchema['max_date']}";
                }
                break;
                
            case 'time':
                $rules[] = 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/';
                break;
                
            case 'json':
                $rules[] = 'json';
                break;
                
            case 'file':
                $rules[] = 'file';
                if (isset($fieldSchema['accepted_types'])) {
                    $rules[] = 'mimes:' . implode(',', $fieldSchema['accepted_types']);
                }
                if (isset($fieldSchema['max_size'])) {
                    $rules[] = "max:{$fieldSchema['max_size']}";
                }
                break;
        }
        
        return $rules;
    }
    
    /**
     * Get HTML attributes for field
     */
    private function getFieldAttributes(array $fieldSchema): array
    {
        $attributes = [];
        $type = $fieldSchema['type'];
        
        switch ($type) {
            case 'text':
            case 'textarea':
                if (isset($fieldSchema['placeholder'])) {
                    $attributes['placeholder'] = $fieldSchema['placeholder'];
                }
                if (isset($fieldSchema['min_length'])) {
                    $attributes['minlength'] = $fieldSchema['min_length'];
                }
                if (isset($fieldSchema['max_length'])) {
                    $attributes['maxlength'] = $fieldSchema['max_length'];
                }
                if ($type === 'textarea' && isset($fieldSchema['rows'])) {
                    $attributes['rows'] = $fieldSchema['rows'];
                }
                break;
                
            case 'number':
            case 'range':
                if (isset($fieldSchema['min'])) {
                    $attributes['min'] = $fieldSchema['min'];
                }
                if (isset($fieldSchema['max'])) {
                    $attributes['max'] = $fieldSchema['max'];
                }
                if (isset($fieldSchema['step'])) {
                    $attributes['step'] = $fieldSchema['step'];
                }
                break;
                
            case 'multiselect':
                if (isset($fieldSchema['min_selections'])) {
                    $attributes['data-min-selections'] = $fieldSchema['min_selections'];
                }
                if (isset($fieldSchema['max_selections'])) {
                    $attributes['data-max-selections'] = $fieldSchema['max_selections'];
                }
                break;
                
            case 'file':
                if (isset($fieldSchema['accepted_types'])) {
                    $attributes['accept'] = '.' . implode(',.', $fieldSchema['accepted_types']);
                }
                break;
        }
        
        return $attributes;
    }
    
    /**
     * Export widget configuration as JSON
     */
    public function exportWidgetConfig(int $userId, string $widgetId): ?string
    {
        $config = $this->getUserWidgetConfig($userId, $widgetId);
        
        if (empty($config)) {
            return null;
        }
        
        $exportData = [
            'widget_id' => $widgetId,
            'configuration' => $config,
            'exported_at' => now()->toISOString(),
            'exported_by' => $userId,
            'version' => '1.0'
        ];
        
        return json_encode($exportData, JSON_PRETTY_PRINT);
    }
    
    /**
     * Import widget configuration from JSON
     */
    public function importWidgetConfig(int $userId, string $jsonData): bool
    {
        try {
            $data = json_decode($jsonData, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON in widget configuration import');
                return false;
            }
            
            if (!isset($data['widget_id'], $data['configuration'])) {
                Log::error('Invalid widget configuration format');
                return false;
            }
            
            return $this->saveUserWidgetConfig(
                $userId,
                $data['widget_id'],
                $data['configuration']
            );
        } catch (\Exception $e) {
            Log::error('Error importing widget configuration', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get configuration history for a widget
     */
    public function getConfigurationHistory(int $userId, string $widgetId, int $limit = 10): array
    {
        // This would typically query a widget_configuration_history table
        // For now, return empty array as placeholder
        return [
            // [
            //     'id' => 1,
            //     'widget_id' => $widgetId,
            //     'user_id' => $userId,
            //     'configuration' => [...],
            //     'changed_fields' => [...],
            //     'created_at' => '2024-01-01 12:00:00'
            // ]
        ];
    }
    
    /**
     * Clear configuration cache
     */
    public function clearConfigurationCache(int $userId, string $widgetId): void
    {
        Cache::forget("user_widget_config_{$userId}_{$widgetId}");
        Cache::forget("widget_config_schema_{$widgetId}");
    }
}
