<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Contracts\Dashboard\LayoutServiceInterface;
use Exception;

class LayoutService implements LayoutServiceInterface
{
    protected int $cacheTimeout = 3600; // 1 hour
    protected string $cachePrefix = 'dashboard_layout_';

    /**
     * Get user's dashboard layout configuration
     */
    public function getUserLayout(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return $this->getDefaultLayout();
        }
        
        return Cache::remember($this->getCacheKey($userId), $this->cacheTimeout, function () use ($userId) {
            try {
                $user = User::find($userId);
                
                if (!$user || !$user->dashboard_layout) {
                    return $this->getDefaultLayout();
                }
                
                // Validate layout structure
                $layout = $user->dashboard_layout;
                if ($this->validateLayout($layout)) {
                    return $layout;
                }
                
                // If invalid, return default and log warning
                Log::warning('Invalid layout structure found for user', [
                    'user_id' => $userId,
                    'layout' => $layout
                ]);
                
                return $this->getDefaultLayout();
                
            } catch (Exception $e) {
                Log::error('Error retrieving user layout', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
                
                return $this->getDefaultLayout();
            }
        });
    }
    
    /**
     * Save user's dashboard layout
     */
    public function saveUserLayout(array $layout, ?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return false;
        }
        
        try {
            // Validate layout before saving
            if (!$this->validateLayout($layout)) {
                Log::warning('Attempted to save invalid layout', [
                    'user_id' => $userId,
                    'layout' => $layout
                ]);
                return false;
            }
            
            $user = User::find($userId);
            if (!$user) {
                return false;
            }
            
            // Add metadata
            $layout['updated_at'] = now()->toISOString();
            $layout['updated_by'] = $userId;
            
            $user->dashboard_layout = $layout;
            $result = $user->save();
            
            if ($result) {
                // Clear cache
                $this->clearUserCache($userId);
                
                Log::info('User layout saved successfully', [
                    'user_id' => $userId,
                    'template' => $layout['template'] ?? 'custom',
                    'widget_count' => count($layout['widgets'] ?? [])
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Log::error('Error saving user layout', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'layout' => $layout
            ]);
            
            return false;
        }
    }
    
    /**
     * Get default dashboard layout
     */
    public function getDefaultLayout(): array
    {
        $defaultTemplate = config('dashboard.layout.default_template', 'professional');
        $templates = $this->getTemplates();
        
        return [
            'template' => $defaultTemplate,
            'theme' => config('dashboard.themes.default', 'light'),
            'sidebar_collapsed' => false,
            'auto_refresh' => true,
            'refresh_interval' => config('dashboard.widgets.auto_refresh_interval', 30000),
            'grid_columns' => config('dashboard.layout.grid_columns', 12),
            'widgets' => $templates[$defaultTemplate]['widgets'] ?? $this->getDefaultWidgets(),
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'version' => '1.0',
        ];
    }
    
    /**
     * Reset user layout to default
     */
    public function resetToDefault(?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return false;
        }
        
        try {
            $defaultLayout = $this->getDefaultLayout();
            $result = $this->saveUserLayout($defaultLayout, $userId);
            
            if ($result) {
                Log::info('User layout reset to default', [
                    'user_id' => $userId,
                    'template' => $defaultLayout['template']
                ]);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Log::error('Error resetting user layout', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get available layout templates
     */
    public function getTemplates(): array
    {
        return Cache::remember('dashboard_templates', 3600, function () {
            $configTemplates = config('dashboard.layout.available_templates', []);
            
            // Enrich templates with default widgets
            $enrichedTemplates = [];
            foreach ($configTemplates as $templateId => $template) {
                $enrichedTemplates[$templateId] = array_merge($template, [
                    'id' => $templateId,
                    'widgets' => $template['widgets'] ?? $this->getTemplateWidgets($templateId),
                    'preview_available' => true,
                    'customizable' => $template['customizable'] ?? true,
                ]);
            }
            
            return $enrichedTemplates;
        });
    }
    
    /**
     * Get layout template by ID
     */
    public function getTemplate(string $templateId): ?array
    {
        $templates = $this->getTemplates();
        return $templates[$templateId] ?? null;
    }
    
    /**
     * Apply template to user layout
     */
    public function applyTemplate(string $templateId, ?int $userId = null): bool
    {
        $template = $this->getTemplate($templateId);
        if (!$template) {
            return false;
        }
        
        $userId = $userId ?? Auth::id();
        $currentLayout = $this->getUserLayout($userId);
        
        // Merge template with current user preferences
        $newLayout = array_merge($currentLayout, [
            'template' => $templateId,
            'widgets' => $template['widgets'],
            'grid_columns' => $template['columns'] ?? config('dashboard.layout.grid_columns', 12),
            'applied_template_at' => now()->toISOString(),
        ]);
        
        return $this->saveUserLayout($newLayout, $userId);
    }
    
    /**
     * Validate layout structure
     */
    public function validateLayout(array $layout): bool
    {
        $validator = Validator::make($layout, [
            'template' => 'sometimes|string',
            'theme' => 'sometimes|string',
            'widgets' => 'sometimes|array',
            'widgets.*.id' => 'required_with:widgets|string',
            'widgets.*.position' => 'sometimes|array',
            'widgets.*.size' => 'sometimes|array',
            'grid_columns' => 'sometimes|integer|min:1|max:24',
            'auto_refresh' => 'sometimes|boolean',
            'refresh_interval' => 'sometimes|integer|min:5000|max:300000',
        ]);
        
        return !$validator->fails();
    }
    
    /**
     * Get user layout sharing permissions
     */
    public function canShareLayout(?int $userId = null): bool
    {
        return config('dashboard.customization.allow_layout_sharing', true);
    }
    
    /**
     * Share layout with other users
     */
    public function shareLayout(string $layoutName, array $userIds, ?int $ownerId = null): bool
    {
        if (!$this->canShareLayout()) {
            return false;
        }
        
        $ownerId = $ownerId ?? Auth::id();
        $ownerLayout = $this->getUserLayout($ownerId);
        
        try {
            foreach ($userIds as $userId) {
                $sharedLayout = array_merge($ownerLayout, [
                    'shared_from' => $ownerId,
                    'shared_at' => now()->toISOString(),
                    'shared_name' => $layoutName,
                ]);
                
                $this->saveUserLayout($sharedLayout, $userId);
            }
            
            Log::info('Layout shared successfully', [
                'owner_id' => $ownerId,
                'shared_with' => $userIds,
                'layout_name' => $layoutName
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Error sharing layout', [
                'owner_id' => $ownerId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get layout performance metrics
     */
    public function getLayoutMetrics(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $layout = $this->getUserLayout($userId);
        
        return [
            'widget_count' => count($layout['widgets'] ?? []),
            'template' => $layout['template'] ?? 'custom',
            'theme' => $layout['theme'] ?? 'light',
            'auto_refresh_enabled' => $layout['auto_refresh'] ?? false,
            'refresh_interval' => $layout['refresh_interval'] ?? 30000,
            'last_updated' => $layout['updated_at'] ?? null,
            'cache_hit' => Cache::has($this->getCacheKey($userId)),
        ];
    }
    
    /**
     * Clear user layout cache
     */
    public function clearUserCache(?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        Cache::forget($this->getCacheKey($userId));
    }
    
    /**
     * Clear all layout caches
     */
    public function clearAllCache(): void
    {
        Cache::forget('dashboard_templates');
        // Clear individual user caches would require iterating through all users
        // This is typically handled by cache tagging in production
    }
    
    /**
     * Get default widgets for layout
     */
    protected function getDefaultWidgets(): array
    {
        return [
            [
                'id' => 'revenue_overview',
                'position' => ['x' => 0, 'y' => 0],
                'size' => ['width' => 6, 'height' => 4],  
                'config' => []
            ],
            [
                'id' => 'sales_chart',
                'position' => ['x' => 6, 'y' => 0],
                'size' => ['width' => 6, 'height' => 4],
                'config' => []
            ],
            [
                'id' => 'user_activity',
                'position' => ['x' => 0, 'y' => 4],
                'size' => ['width' => 4, 'height' => 3],
                'config' => []
            ],
            [
                'id' => 'system_status',
                'position' => ['x' => 4, 'y' => 4],
                'size' => ['width' => 4, 'height' => 3],
                'config' => []
            ],
            [
                'id' => 'recent_orders',
                'position' => ['x' => 8, 'y' => 4],
                'size' => ['width' => 4, 'height' => 3],
                'config' => []
            ],
        ];
    }
    
    /**
     * Get template-specific widgets
     */
    protected function getTemplateWidgets(string $templateId): array
    {
        $widgets = [
            'professional' => $this->getDefaultWidgets(),
            'minimal' => [
                [
                    'id' => 'kpi_summary',
                    'position' => ['x' => 0, 'y' => 0],
                    'size' => ['width' => 12, 'height' => 3],
                    'config' => []
                ],
                [
                    'id' => 'quick_stats',
                    'position' => ['x' => 0, 'y' => 3],
                    'size' => ['width' => 6, 'height' => 4],
                    'config' => []
                ],
                [
                    'id' => 'activity_feed',
                    'position' => ['x' => 6, 'y' => 3],
                    'size' => ['width' => 6, 'height' => 4],
                    'config' => []
                ],
            ],
            'executive' => [
                [
                    'id' => 'executive_summary',
                    'position' => ['x' => 0, 'y' => 0],
                    'size' => ['width' => 12, 'height' => 2],
                    'config' => []
                ],
                [
                    'id' => 'revenue_chart',
                    'position' => ['x' => 0, 'y' => 2],
                    'size' => ['width' => 8, 'height' => 5],
                    'config' => []
                ],
                [
                    'id' => 'key_metrics',  
                    'position' => ['x' => 8, 'y' => 2],
                    'size' => ['width' => 4, 'height' => 5],
                    'config' => []
                ],
            ],
        ];
        
        return $widgets[$templateId] ?? $this->getDefaultWidgets();
    }
    
    /**
     * Get cache key for user layout
     */
    protected function getCacheKey(int $userId): string
    {
        return $this->cachePrefix . $userId;
    }
}
