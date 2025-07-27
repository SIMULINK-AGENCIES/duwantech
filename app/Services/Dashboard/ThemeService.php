<?php

namespace App\Services\Dashboard;

use App\Contracts\Dashboard\ThemeServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ThemeService implements ThemeServiceInterface
{
    private array $availableThemes = [];
    private int $cacheTimeout = 7200; // 2 hours
    
    public function __construct()
    {
        $this->initializeThemes();
    }
    
    /**
     * Get user's current theme with caching
     */
    public function getUserTheme(int $userId): array
    {
        $cacheKey = "user_theme_{$userId}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($userId) {
            $user = User::find($userId);
            if (!$user || !$user->dashboard_preferences) {
                Log::info("Loading default theme for user", ['user_id' => $userId]);
                return $this->getDefaultTheme();
            }
            
            $preferences = $user->dashboard_preferences;
            $themeData = $preferences['theme'] ?? $this->getDefaultTheme();
            
            // Validate theme still exists
            if (!isset($this->availableThemes[$themeData['name']])) {
                Log::warning("User theme no longer available, reverting to default", [
                    'user_id' => $userId,
                    'invalid_theme' => $themeData['name']
                ]);
                return $this->getDefaultTheme();
            }
            
            Log::info("Loaded user theme from cache", [
                'user_id' => $userId,
                'theme' => $themeData['name']
            ]);
            
            return $themeData;
        });
    }
    
    /**
     * Save user's theme preferences with validation
     */
    public function saveUserTheme(int $userId, string $theme, array $customColors = []): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                Log::warning("User not found when saving theme", ['user_id' => $userId]);
                return false;
            }
            
            // Validate theme
            $validationResult = $this->validateTheme($theme, $customColors);
            if (!empty($validationResult)) {
                Log::warning("Theme validation failed", [
                    'user_id' => $userId,
                    'theme' => $theme,
                    'errors' => $validationResult
                ]);
                return false;
            }
            
            $preferences = $user->dashboard_preferences ?? [];
            $preferences['theme'] = [
                'name' => $theme,
                'custom_colors' => $customColors,
                'updated_at' => now(),
                'css_variables' => $this->generateCSSVariables($theme, $customColors)
            ];
            
            $success = $user->update(['dashboard_preferences' => $preferences]);
            
            if ($success) {
                // Clear user cache
                $this->clearUserThemeCache($userId);
                
                Log::info("Successfully saved user theme", [
                    'user_id' => $userId,
                    'theme' => $theme,
                    'has_custom_colors' => !empty($customColors)
                ]);
            }
            
            return $success;
        } catch (\Exception $e) {
            Log::error("Error saving user theme", [
                'user_id' => $userId,
                'theme' => $theme,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get all available themes with caching
     */
    public function getAvailableThemes(): array
    {
        return Cache::remember('available_themes', $this->cacheTimeout, function() {
            return $this->availableThemes;
        });
    }
    
    /**
     * Get theme configuration by name
     */
    public function getTheme(string $name): ?array
    {
        return $this->availableThemes[$name] ?? null;
    }
    
    /**
     * Generate CSS variables for theme
     */
    public function generateThemeCSS(string $theme, array $customColors = []): string
    {
        $themeConfig = $this->getTheme($theme);
        if (!$themeConfig) {
            return '';
        }
        
        $variables = $this->generateCSSVariables($theme, $customColors);
        
        $css = ":root {\n";
        foreach ($variables as $property => $value) {
            $css .= "  --{$property}: {$value};\n";
        }
        $css .= "}\n";
        
        // Add theme-specific styles
        if (isset($themeConfig['additional_css'])) {
            $css .= "\n" . $themeConfig['additional_css'];
        }
        
        return $css;
    }
    
    /**
     * Validate theme configuration
     */
    public function validateTheme(string $theme, array $customColors = []): array
    {
        $errors = [];
        
        // Check if theme exists
        if (!isset($this->availableThemes[$theme])) {
            $errors['theme'] = ["Theme '{$theme}' does not exist"];
            return $errors;
        }
        
        // Validate custom colors if provided
        if (!empty($customColors)) {
            $colorValidator = Validator::make(['colors' => $customColors], [
                'colors' => 'array',
                'colors.*' => 'string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ]);
            
            if ($colorValidator->fails()) {
                $errors['custom_colors'] = $colorValidator->errors()->toArray()['colors'] ?? [];
            }
            
            // Check if custom colors are allowed for this theme
            $themeConfig = $this->availableThemes[$theme];
            if (!($themeConfig['supports_custom_colors'] ?? false)) {
                $errors['custom_colors'] = ["Theme '{$theme}' does not support custom colors"];
            }
        }
        
        return $errors;
    }
    
    /**
     * Get default theme for new users
     */
    public function getDefaultTheme(): array
    {
        return [
            'name' => 'professional',
            'custom_colors' => [],
            'css_variables' => $this->generateCSSVariables('professional', [])
        ];
    }
    
    /**
     * Clear theme cache for user
     */
    public function clearUserThemeCache(int $userId): void
    {
        $cacheKeys = [
            "user_theme_{$userId}",
            "theme_css_{$userId}"
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        Log::info("Cleared theme cache for user", ['user_id' => $userId]);
    }
    
    /**
     * Get theme usage statistics
     */
    public function getThemeStatistics(): array
    {
        return Cache::remember('theme_statistics', 3600, function() {
            $stats = [
                'total_themes' => count($this->availableThemes),
                'themes' => [],
                'most_popular' => null,
                'custom_color_usage' => 0
            ];
            
            foreach ($this->availableThemes as $themeName => $config) {
                $stats['themes'][$themeName] = [
                    'name' => $config['name'],
                    'description' => $config['description'],
                    'supports_custom_colors' => $config['supports_custom_colors'] ?? false,
                    'usage_count' => 0 // Would be populated from actual user data
                ];
            }
            
            // This would typically query the database for actual usage statistics
            // For now, providing placeholder data
            $stats['most_popular'] = 'professional';
            
            return $stats;
        });
    }
    
    /**
     * Export user theme as JSON
     */
    public function exportUserTheme(int $userId): array
    {
        $userTheme = $this->getUserTheme($userId);
        
        return [
            'version' => '1.0',
            'exported_at' => now()->toISOString(),
            'user_id' => $userId,
            'theme' => $userTheme,
            'theme_config' => $this->getTheme($userTheme['name'])
        ];
    }
    
    /**
     * Import theme configuration for user
     */
    public function importUserTheme(int $userId, array $themeData): bool
    {
        try {
            // Validate import data structure
            $validator = Validator::make($themeData, [
                'version' => 'required|string',
                'theme' => 'required|array',
                'theme.name' => 'required|string',
                'theme.custom_colors' => 'sometimes|array'
            ]);
            
            if ($validator->fails()) {
                Log::warning("Invalid theme import data", [
                    'user_id' => $userId,
                    'errors' => $validator->errors()->toArray()
                ]);
                return false;
            }
            
            $theme = $themeData['theme'];
            
            return $this->saveUserTheme(
                $userId,
                $theme['name'],
                $theme['custom_colors'] ?? []
            );
        } catch (\Exception $e) {
            Log::error("Error importing user theme", [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Generate CSS variables for theme and custom colors
     */
    private function generateCSSVariables(string $theme, array $customColors = []): array
    {
        $themeConfig = $this->getTheme($theme);
        if (!$themeConfig) {
            return [];
        }
        
        $variables = $themeConfig['css_variables'] ?? [];
        
        // Override with custom colors if provided and supported
        if (!empty($customColors) && ($themeConfig['supports_custom_colors'] ?? false)) {
            foreach ($customColors as $property => $color) {
                if (isset($variables[$property])) {
                    $variables[$property] = $color;
                }
            }
        }
        
        return $variables;
    }
    
    /**
     * Initialize available themes
     */
    private function initializeThemes(): void
    {
        $this->availableThemes = [
            'light' => [
                'name' => 'Light Theme',
                'description' => 'Clean and bright interface',
                'category' => 'standard',
                'supports_custom_colors' => true,
                'preview_image' => '/images/themes/light-preview.png',
                'css_variables' => [
                    'primary-color' => '#3b82f6',
                    'secondary-color' => '#64748b',
                    'background-color' => '#ffffff',
                    'surface-color' => '#f8fafc',
                    'text-primary' => '#1e293b',
                    'text-secondary' => '#64748b',
                    'border-color' => '#e2e8f0',
                    'success-color' => '#10b981',
                    'warning-color' => '#f59e0b',
                    'error-color' => '#ef4444',
                    'info-color' => '#3b82f6'
                ]
            ],
            
            'dark' => [
                'name' => 'Dark Theme',
                'description' => 'Easy on the eyes dark interface',
                'category' => 'standard',
                'supports_custom_colors' => true,
                'preview_image' => '/images/themes/dark-preview.png',
                'css_variables' => [
                    'primary-color' => '#60a5fa',
                    'secondary-color' => '#94a3b8',
                    'background-color' => '#0f172a',
                    'surface-color' => '#1e293b',
                    'text-primary' => '#f1f5f9',
                    'text-secondary' => '#94a3b8',
                    'border-color' => '#334155',
                    'success-color' => '#34d399',
                    'warning-color' => '#fbbf24',
                    'error-color' => '#f87171',
                    'info-color' => '#60a5fa'
                ]
            ],
            
            'professional' => [
                'name' => 'Professional Theme',
                'description' => 'Corporate-focused design with refined colors',
                'category' => 'business',
                'supports_custom_colors' => true,
                'preview_image' => '/images/themes/professional-preview.png',
                'css_variables' => [
                    'primary-color' => '#1e40af',
                    'secondary-color' => '#6b7280',
                    'background-color' => '#f9fafb',
                    'surface-color' => '#ffffff',
                    'text-primary' => '#111827',
                    'text-secondary' => '#6b7280',
                    'border-color' => '#d1d5db',
                    'success-color' => '#059669',
                    'warning-color' => '#d97706',
                    'error-color' => '#dc2626',
                    'info-color' => '#1e40af'
                ],
                'additional_css' => '
                    .card { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
                    .btn-primary { font-weight: 600; }
                    .nav-link { font-weight: 500; }
                '
            ],
            
            'minimal' => [
                'name' => 'Minimal Theme',
                'description' => 'Clean and distraction-free interface',
                'category' => 'modern',
                'supports_custom_colors' => false,
                'preview_image' => '/images/themes/minimal-preview.png',
                'css_variables' => [
                    'primary-color' => '#000000',
                    'secondary-color' => '#6b7280',
                    'background-color' => '#ffffff',
                    'surface-color' => '#ffffff',
                    'text-primary' => '#000000',
                    'text-secondary' => '#6b7280',
                    'border-color' => '#f3f4f6',
                    'success-color' => '#000000',
                    'warning-color' => '#000000',
                    'error-color' => '#000000',
                    'info-color' => '#000000'
                ],
                'additional_css' => '
                    .card { border: 1px solid var(--border-color); box-shadow: none; }
                    .btn { border-radius: 0; }
                    input, select, textarea { border-radius: 0; }
                '
            ],
            
            'blue-corporate' => [
                'name' => 'Blue Corporate',
                'description' => 'Professional blue-themed design',
                'category' => 'business',
                'supports_custom_colors' => true,
                'preview_image' => '/images/themes/blue-corporate-preview.png',
                'css_variables' => [
                    'primary-color' => '#1e3a8a',
                    'secondary-color' => '#475569',
                    'background-color' => '#f8fafc',
                    'surface-color' => '#ffffff',
                    'text-primary' => '#1e293b',
                    'text-secondary' => '#475569',
                    'border-color' => '#cbd5e1',
                    'success-color' => '#0f766e',
                    'warning-color' => '#ca8a04',
                    'error-color' => '#b91c1c',
                    'info-color' => '#1e3a8a'
                ]
            ],
            
            'green-nature' => [
                'name' => 'Green Nature',
                'description' => 'Eco-friendly green color scheme',
                'category' => 'colorful',
                'supports_custom_colors' => true,
                'preview_image' => '/images/themes/green-nature-preview.png',
                'css_variables' => [
                    'primary-color' => '#166534',
                    'secondary-color' => '#6b7280',
                    'background-color' => '#f0fdf4',
                    'surface-color' => '#ffffff',
                    'text-primary' => '#14532d',
                    'text-secondary' => '#6b7280',
                    'border-color' => '#dcfce7',
                    'success-color' => '#15803d',
                    'warning-color' => '#ca8a04',
                    'error-color' => '#dc2626',
                    'info-color' => '#166534'
                ]
            ]
        ];
    }
}
