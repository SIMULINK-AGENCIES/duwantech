<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WidgetConfigurationSchema
{
    /**
     * Base widget configuration schema
     */
    private array $baseSchema = [
        'id' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'category' => 'required|string|in:analytics,ecommerce,users,system,content,communication,productivity,custom',
        'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
        'size' => 'required|array',
        'size.width' => 'required|integer|min:1|max:12',
        'size.height' => 'required|integer|min:1|max:12',
        'author' => 'sometimes|string|max:255',
        'icon' => 'sometimes|string|max:100',
        'color' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'tags' => 'sometimes|array',
        'tags.*' => 'string|max:50',
        'permissions' => 'sometimes|array',
        'permissions.*' => 'string|max:100',
        'dependencies' => 'sometimes|array',
        'dependencies.*' => 'string|max:100',
        'is_premium' => 'sometimes|boolean',
        'refresh_interval' => 'sometimes|integer|min:0|max:3600',
        'cache_enabled' => 'sometimes|boolean',
        'supports_resize' => 'sometimes|boolean',
        'supports_config' => 'sometimes|boolean',
        'min_size' => 'sometimes|array',
        'min_size.width' => 'required_with:min_size|integer|min:1|max:12',
        'min_size.height' => 'required_with:min_size|integer|min:1|max:12',
        'max_size' => 'sometimes|array',
        'max_size.width' => 'required_with:max_size|integer|min:1|max:12',
        'max_size.height' => 'required_with:max_size|integer|min:1|max:12',
        'default_size' => 'sometimes|array',
        'default_size.width' => 'required_with:default_size|integer|min:1|max:12',
        'default_size.height' => 'required_with:default_size|integer|min:1|max:12',
        'allowed_roles' => 'sometimes|array',
        'allowed_roles.*' => 'string|max:50',
        'feature_flag' => 'sometimes|string|max:100',
        'status' => 'sometimes|string|in:active,inactive,deprecated,beta',
        'config_schema' => 'sometimes|array',
        'data_sources' => 'sometimes|array',
        'api_endpoints' => 'sometimes|array',
        'template' => 'sometimes|string|max:255',
        'class' => 'sometimes|string|max:255',
        'view_path' => 'sometimes|string|max:255',
        'css_classes' => 'sometimes|array',
        'css_classes.*' => 'string|max:100',
        'js_dependencies' => 'sometimes|array',
        'js_dependencies.*' => 'string|max:255',
        'css_dependencies' => 'sometimes|array',
        'css_dependencies.*' => 'string|max:255',
    ];
    
    /**
     * Configuration field types and their schemas
     */
    private array $configFieldTypes = [
        'text' => [
            'type' => 'required|in:text',
            'label' => 'required|string|max:255',
            'placeholder' => 'sometimes|string|max:255',
            'default' => 'sometimes|string',
            'required' => 'sometimes|boolean',
            'min_length' => 'sometimes|integer|min:0',
            'max_length' => 'sometimes|integer|min:1',
            'pattern' => 'sometimes|string',
            'help_text' => 'sometimes|string|max:500',
        ],
        'number' => [
            'type' => 'required|in:number',
            'label' => 'required|string|max:255',
            'default' => 'sometimes|numeric',
            'required' => 'sometimes|boolean',
            'min' => 'sometimes|numeric',
            'max' => 'sometimes|numeric',
            'step' => 'sometimes|numeric|min:0.01',
            'help_text' => 'sometimes|string|max:500',
        ],
        'boolean' => [
            'type' => 'required|in:boolean',
            'label' => 'required|string|max:255',
            'default' => 'sometimes|boolean',
            'help_text' => 'sometimes|string|max:500',
        ],
        'select' => [
            'type' => 'required|in:select',
            'label' => 'required|string|max:255',
            'options' => 'required|array|min:1',
            'options.*' => 'required|array',
            'options.*.value' => 'required|string',
            'options.*.label' => 'required|string|max:255',
            'default' => 'sometimes|string',
            'required' => 'sometimes|boolean',
            'multiple' => 'sometimes|boolean',
            'help_text' => 'sometimes|string|max:500',
        ],
        'multiselect' => [
            'type' => 'required|in:multiselect',
            'label' => 'required|string|max:255',
            'options' => 'required|array|min:1',
            'options.*' => 'required|array',
            'options.*.value' => 'required|string',
            'options.*.label' => 'required|string|max:255',
            'default' => 'sometimes|array',
            'default.*' => 'string',
            'required' => 'sometimes|boolean',
            'min_selections' => 'sometimes|integer|min:0',
            'max_selections' => 'sometimes|integer|min:1',
            'help_text' => 'sometimes|string|max:500',
        ],
        'color' => [
            'type' => 'required|in:color',
            'label' => 'required|string|max:255',
            'default' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'required' => 'sometimes|boolean',
            'help_text' => 'sometimes|string|max:500',
        ],
        'date' => [
            'type' => 'required|in:date',
            'label' => 'required|string|max:255',
            'default' => 'sometimes|date',
            'required' => 'sometimes|boolean',
            'min_date' => 'sometimes|date',
            'max_date' => 'sometimes|date',
            'format' => 'sometimes|string|in:Y-m-d,Y-m-d H:i:s,d/m/Y,m/d/Y',
            'help_text' => 'sometimes|string|max:500',
        ],
        'time' => [
            'type' => 'required|in:time',
            'label' => 'required|string|max:255',
            'default' => 'sometimes|string|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
            'required' => 'sometimes|boolean',
            'format' => 'sometimes|string|in:H:i,h:i A',
            'help_text' => 'sometimes|string|max:500',
        ],
        'textarea' => [
            'type' => 'required|in:textarea',
            'label' => 'required|string|max:255',
            'placeholder' => 'sometimes|string|max:255',
            'default' => 'sometimes|string',
            'required' => 'sometimes|boolean',
            'min_length' => 'sometimes|integer|min:0',
            'max_length' => 'sometimes|integer|min:1',
            'rows' => 'sometimes|integer|min:2|max:20',
            'help_text' => 'sometimes|string|max:500',
        ],
        'json' => [
            'type' => 'required|in:json',
            'label' => 'required|string|max:255',
            'default' => 'sometimes|array',
            'required' => 'sometimes|boolean',
            'schema' => 'sometimes|array',
            'help_text' => 'sometimes|string|max:500',
        ],
        'file' => [
            'type' => 'required|in:file',
            'label' => 'required|string|max:255',
            'required' => 'sometimes|boolean',
            'accepted_types' => 'sometimes|array',
            'accepted_types.*' => 'string|max:10',
            'max_size' => 'sometimes|integer|min:1',
            'help_text' => 'sometimes|string|max:500',
        ],
        'range' => [
            'type' => 'required|in:range',
            'label' => 'required|string|max:255',
            'min' => 'required|numeric',
            'max' => 'required|numeric|gt:min',
            'step' => 'sometimes|numeric|min:0.01',
            'default' => 'sometimes|numeric',
            'required' => 'sometimes|boolean',
            'help_text' => 'sometimes|string|max:500',
        ]
    ];
    
    /**
     * Validate widget configuration against schema
     */
    public function validateWidgetConfiguration(array $config): array
    {
        $validator = Validator::make($config, $this->baseSchema);
        
        $errors = [];
        
        if ($validator->fails()) {
            $errors['base'] = $validator->errors()->toArray();
        }
        
        // Validate configuration schema if present
        if (isset($config['config_schema']) && is_array($config['config_schema'])) {
            $configErrors = $this->validateConfigurationSchema($config['config_schema']);
            if (!empty($configErrors)) {
                $errors['config_schema'] = $configErrors;
            }
        }
        
        // Validate size constraints
        $sizeErrors = $this->validateSizeConstraints($config);
        if (!empty($sizeErrors)) {
            $errors['size_constraints'] = $sizeErrors;
        }
        
        // Validate dependencies
        if (isset($config['dependencies'])) {
            $dependencyErrors = $this->validateDependencies($config['dependencies']);
            if (!empty($dependencyErrors)) {
                $errors['dependencies'] = $dependencyErrors;
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate widget configuration schema
     */
    public function validateConfigurationSchema(array $schema): array
    {
        $errors = [];
        
        foreach ($schema as $fieldName => $fieldConfig) {
            if (!is_array($fieldConfig)) {
                $errors[$fieldName] = ['Configuration must be an array'];
                continue;
            }
            
            if (!isset($fieldConfig['type'])) {
                $errors[$fieldName] = ['Field type is required'];
                continue;
            }
            
            $fieldType = $fieldConfig['type'];
            
            if (!isset($this->configFieldTypes[$fieldType])) {
                $errors[$fieldName] = ["Invalid field type: {$fieldType}"];
                continue;
            }
            
            // Validate field configuration against its type schema
            $fieldValidator = Validator::make($fieldConfig, $this->configFieldTypes[$fieldType]);
            
            if ($fieldValidator->fails()) {
                $errors[$fieldName] = $fieldValidator->errors()->toArray();
            }
            
            // Additional validation for specific field types
            $typeSpecificErrors = $this->validateFieldTypeSpecifics($fieldName, $fieldConfig);
            if (!empty($typeSpecificErrors)) {
                $errors[$fieldName] = array_merge($errors[$fieldName] ?? [], $typeSpecificErrors);
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate size constraints
     */
    private function validateSizeConstraints(array $config): array
    {
        $errors = [];
        
        if (isset($config['min_size'], $config['max_size'])) {
            $minWidth = $config['min_size']['width'] ?? 1;
            $minHeight = $config['min_size']['height'] ?? 1;
            $maxWidth = $config['max_size']['width'] ?? 12;
            $maxHeight = $config['max_size']['height'] ?? 12;
            
            if ($minWidth > $maxWidth) {
                $errors[] = 'Minimum width cannot be greater than maximum width';
            }
            
            if ($minHeight > $maxHeight) {
                $errors[] = 'Minimum height cannot be greater than maximum height';
            }
        }
        
        if (isset($config['default_size'])) {
            $defaultWidth = $config['default_size']['width'];
            $defaultHeight = $config['default_size']['height'];
            
            if (isset($config['min_size'])) {
                $minWidth = $config['min_size']['width'];
                $minHeight = $config['min_size']['height'];
                
                if ($defaultWidth < $minWidth || $defaultHeight < $minHeight) {
                    $errors[] = 'Default size cannot be smaller than minimum size';
                }
            }
            
            if (isset($config['max_size'])) {
                $maxWidth = $config['max_size']['width'];
                $maxHeight = $config['max_size']['height'];
                
                if ($defaultWidth > $maxWidth || $defaultHeight > $maxHeight) {
                    $errors[] = 'Default size cannot be larger than maximum size';
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate widget dependencies
     */
    private function validateDependencies(array $dependencies): array
    {
        $errors = [];
        
        foreach ($dependencies as $dependency) {
            if (!is_string($dependency)) {
                $errors[] = 'Dependency must be a string';
                continue;
            }
            
            // Check if dependency format is valid (widget_id or package/widget_id)
            if (!preg_match('/^[a-z0-9_]+(\\/[a-z0-9_]+)?$/', $dependency)) {
                $errors[] = "Invalid dependency format: {$dependency}";
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate field type specific rules
     */
    private function validateFieldTypeSpecifics(string $fieldName, array $fieldConfig): array
    {
        $errors = [];
        $type = $fieldConfig['type'];
        
        switch ($type) {
            case 'select':
                if (isset($fieldConfig['default'])) {
                    $validValues = array_column($fieldConfig['options'], 'value');
                    if (!in_array($fieldConfig['default'], $validValues)) {
                        $errors[] = 'Default value must be one of the available options';
                    }
                }
                break;
                
            case 'multiselect':
                if (isset($fieldConfig['default'])) {
                    $validValues = array_column($fieldConfig['options'], 'value');
                    $invalidDefaults = array_diff($fieldConfig['default'], $validValues);
                    if (!empty($invalidDefaults)) {
                        $errors[] = 'Default values must be from available options: ' . implode(', ', $invalidDefaults);
                    }
                }
                
                if (isset($fieldConfig['min_selections'], $fieldConfig['max_selections'])) {
                    if ($fieldConfig['min_selections'] > $fieldConfig['max_selections']) {
                        $errors[] = 'Minimum selections cannot be greater than maximum selections';
                    }
                }
                break;
                
            case 'range':
                if (isset($fieldConfig['default'])) {
                    $default = $fieldConfig['default'];
                    $min = $fieldConfig['min'];
                    $max = $fieldConfig['max'];
                    
                    if ($default < $min || $default > $max) {
                        $errors[] = "Default value must be between {$min} and {$max}";
                    }
                }
                break;
                
            case 'date':
                if (isset($fieldConfig['min_date'], $fieldConfig['max_date'])) {
                    if (strtotime($fieldConfig['min_date']) > strtotime($fieldConfig['max_date'])) {
                        $errors[] = 'Minimum date cannot be after maximum date';
                    }
                }
                break;
        }
        
        return $errors;
    }
    
    /**
     * Get schema for a specific field type
     */
    public function getFieldTypeSchema(string $type): ?array
    {
        return $this->configFieldTypes[$type] ?? null;
    }
    
    /**
     * Get all available field types
     */
    public function getAvailableFieldTypes(): array
    {
        return array_keys($this->configFieldTypes);
    }
    
    /**
     * Generate default configuration schema
     */
    public function generateDefaultConfigSchema(): array
    {
        return [
            'refresh_interval' => [
                'type' => 'number',
                'label' => 'Refresh Interval (seconds)',
                'default' => 300,
                'min' => 30,
                'max' => 3600,
                'help_text' => 'How often the widget should refresh its data (in seconds)'
            ],
            'show_header' => [
                'type' => 'boolean',
                'label' => 'Show Widget Header',
                'default' => true,
                'help_text' => 'Display the widget title and controls'
            ],
            'theme' => [
                'type' => 'select',
                'label' => 'Widget Theme',
                'options' => [
                    ['value' => 'light', 'label' => 'Light'],
                    ['value' => 'dark', 'label' => 'Dark'],
                    ['value' => 'auto', 'label' => 'Auto (System)']
                ],
                'default' => 'auto',
                'help_text' => 'Choose the widget color theme'
            ]
        ];
    }
    
    /**
     * Generate JSON schema for OpenAPI/validation
     */
    public function generateJsonSchema(): array
    {
        return [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'title' => 'Widget Configuration Schema',
            'description' => 'Schema for widget configuration validation',
            'type' => 'object',
            'properties' => $this->convertLaravelRulesToJsonSchema($this->baseSchema),
            'additionalProperties' => false
        ];
    }
    
    /**
     * Convert Laravel validation rules to JSON schema
     */
    private function convertLaravelRulesToJsonSchema(array $rules): array
    {
        $properties = [];
        
        foreach ($rules as $field => $rule) {
            $properties[$field] = $this->convertFieldRuleToJsonSchema($field, $rule);
        }
        
        return $properties;
    }
    
    /**
     * Convert single field rule to JSON schema property
     */
    private function convertFieldRuleToJsonSchema(string $field, string $rule): array
    {
        $property = ['type' => 'string']; // Default type
        $rules = explode('|', $rule);
        
        foreach ($rules as $singleRule) {
            if (str_contains($singleRule, 'integer')) {
                $property['type'] = 'integer';
            } elseif (str_contains($singleRule, 'boolean')) {
                $property['type'] = 'boolean';
            } elseif (str_contains($singleRule, 'array')) {
                $property['type'] = 'array';
            } elseif (str_contains($singleRule, 'numeric')) {
                $property['type'] = 'number';
            }
            
            if (str_contains($singleRule, 'required')) {
                $property['required'] = true;
            }
            
            if (preg_match('/max:(\d+)/', $singleRule, $matches)) {
                if ($property['type'] === 'string') {
                    $property['maxLength'] = (int) $matches[1];
                } else {
                    $property['maximum'] = (int) $matches[1];
                }
            }
            
            if (preg_match('/min:(\d+)/', $singleRule, $matches)) {
                if ($property['type'] === 'string') {
                    $property['minLength'] = (int) $matches[1];
                } else {
                    $property['minimum'] = (int) $matches[1];
                }
            }
            
            if (preg_match('/in:([^|]+)/', $singleRule, $matches)) {
                $property['enum'] = explode(',', $matches[1]);
            }
            
            if (preg_match('/regex:(.+)/', $singleRule, $matches)) {
                $property['pattern'] = $matches[1];
            }
        }
        
        return $property;
    }
    
    /**
     * Validate user widget configuration data
     */
    public function validateUserWidgetConfig(array $widgetSchema, array $userConfig): array
    {
        $errors = [];
        
        foreach ($widgetSchema as $fieldName => $fieldSchema) {
            $fieldType = $fieldSchema['type'];
            $isRequired = $fieldSchema['required'] ?? false;
            $userValue = $userConfig[$fieldName] ?? null;
            
            // Check required fields
            if ($isRequired && $userValue === null) {
                $errors[$fieldName] = ['This field is required'];
                continue;
            }
            
            // Skip validation if field is not required and not provided
            if (!$isRequired && $userValue === null) {
                continue;
            }
            
            // Validate based on field type
            $fieldErrors = $this->validateUserFieldValue($fieldName, $fieldType, $userValue, $fieldSchema);
            if (!empty($fieldErrors)) {
                $errors[$fieldName] = $fieldErrors;
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate individual user field value
     */
    private function validateUserFieldValue(string $fieldName, string $fieldType, $value, array $schema): array
    {
        $errors = [];
        
        switch ($fieldType) {
            case 'text':
            case 'textarea':
                if (!is_string($value)) {
                    $errors[] = 'Value must be a string';
                    break;
                }
                
                if (isset($schema['min_length']) && strlen($value) < $schema['min_length']) {
                    $errors[] = "Minimum length is {$schema['min_length']} characters";
                }
                
                if (isset($schema['max_length']) && strlen($value) > $schema['max_length']) {
                    $errors[] = "Maximum length is {$schema['max_length']} characters";
                }
                
                if (isset($schema['pattern']) && !preg_match($schema['pattern'], $value)) {
                    $errors[] = 'Value does not match required pattern';
                }
                break;
                
            case 'number':
            case 'range':
                if (!is_numeric($value)) {
                    $errors[] = 'Value must be a number';
                    break;
                }
                
                $numValue = (float) $value;
                
                if (isset($schema['min']) && $numValue < $schema['min']) {
                    $errors[] = "Minimum value is {$schema['min']}";
                }
                
                if (isset($schema['max']) && $numValue > $schema['max']) {
                    $errors[] = "Maximum value is {$schema['max']}";
                }
                break;
                
            case 'boolean':
                if (!is_bool($value)) {
                    $errors[] = 'Value must be boolean (true/false)';
                }
                break;
                
            case 'select':
                $validValues = array_column($schema['options'] ?? [], 'value');
                if (!in_array($value, $validValues)) {
                    $errors[] = 'Invalid selection';
                }
                break;
                
            case 'multiselect':
                if (!is_array($value)) {
                    $errors[] = 'Value must be an array';
                    break;
                }
                
                $validValues = array_column($schema['options'] ?? [], 'value');
                $invalidValues = array_diff($value, $validValues);
                if (!empty($invalidValues)) {
                    $errors[] = 'Invalid selections: ' . implode(', ', $invalidValues);
                }
                
                if (isset($schema['min_selections']) && count($value) < $schema['min_selections']) {
                    $errors[] = "Minimum {$schema['min_selections']} selections required";
                }
                
                if (isset($schema['max_selections']) && count($value) > $schema['max_selections']) {
                    $errors[] = "Maximum {$schema['max_selections']} selections allowed";
                }
                break;
                
            case 'color':
                if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
                    $errors[] = 'Value must be a valid hex color (e.g., #FF0000)';
                }
                break;
                
            case 'date':
                if (!strtotime($value)) {
                    $errors[] = 'Invalid date format';
                    break;
                }
                
                if (isset($schema['min_date']) && strtotime($value) < strtotime($schema['min_date'])) {
                    $errors[] = "Date must be after {$schema['min_date']}";
                }
                
                if (isset($schema['max_date']) && strtotime($value) > strtotime($schema['max_date'])) {
                    $errors[] = "Date must be before {$schema['max_date']}";
                }
                break;
                
            case 'time':
                if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
                    $errors[] = 'Invalid time format (use HH:MM)';
                }
                break;
                
            case 'json':
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $errors[] = 'Invalid JSON format';
                    }
                } elseif (!is_array($value)) {
                    $errors[] = 'Value must be valid JSON or array';
                }
                break;
        }
        
        return $errors;
    }
}
