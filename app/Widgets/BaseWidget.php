<?php

namespace App\Widgets;

use App\Contracts\Dashboard\WidgetInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

/**
 * Base class for dashboard widgets
 */
abstract class BaseWidget implements WidgetInterface
{
    protected array $config = [];
    protected int $cacheTimeout = 300; // 5 minutes
    
    /**
     * Widget configuration - must be implemented by subclasses
     */
    abstract protected function getWidgetConfig(): array;
    
    /**
     * Get widget data - must be implemented by subclasses
     */
    abstract protected function getWidgetData(array $config = []): array;
    
    /**
     * Get widget configuration
     */
    public function getConfig(): array
    {
        return array_merge([
            'cache_enabled' => true,
            'refresh_interval' => 300,
            'supports_config' => false,
            'supports_resize' => true,
            'permissions' => [],
            'version' => '1.0.0',
            'author' => 'System',
        ], $this->getWidgetConfig());
    }
    
    /**
     * Get widget data with caching
     */
    public function getData(array $config = []): array
    {
        $this->config = $config;
        
        if ($this->getConfig()['cache_enabled']) {
            $cacheKey = $this->getCacheKey($config);
            return Cache::remember($cacheKey, $this->cacheTimeout, function() use ($config) {
                return $this->getWidgetData($config);
            });
        }
        
        return $this->getWidgetData($config);
    }
    
    /**
     * Render widget HTML
     */
    public function render(array $config = []): string
    {
        $data = $this->getData($config);
        $widgetConfig = $this->getConfig();
        
        $viewData = array_merge($data, [
            'config' => $config,
            'widget_config' => $widgetConfig,
            'widget_id' => $widgetConfig['id'] ?? 'unknown',
        ]);
        
        $template = $widgetConfig['template'] ?? 'admin.widgets.' . $widgetConfig['id'];
        
        if (View::exists($template)) {
            return View::make($template, $viewData)->render();
        }
        
        return $this->getDefaultTemplate($viewData);
    }
    
    /**
     * Validate widget configuration
     */
    public function validateConfig(array $config): array
    {
        $schema = $this->getConfigSchema();
        
        if (empty($schema)) {
            return []; // No validation rules defined
        }
        
        $validator = Validator::make($config, $schema);
        
        return $validator->errors()->toArray();
    }
    
    /**
     * Get widget configuration schema - can be overridden by subclasses
     */
    public function getConfigSchema(): array
    {
        return [];
    }
    
    /**
     * Handle widget refresh
     */
    public function refresh(array $config = []): array
    {
        // Clear cache if enabled
        if ($this->getConfig()['cache_enabled']) {
            $cacheKey = $this->getCacheKey($config);
            Cache::forget($cacheKey);
        }
        
        return $this->getData($config);
    }
    
    /**
     * Check if widget is available for current user
     */
    public function isAvailable(?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return false;
        }
        
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return false;
        }
        
        $permissions = $this->getConfig()['permissions'] ?? [];
        
        foreach ($permissions as $permission) {
            if (!$user->can($permission)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get cache key for widget data
     */
    protected function getCacheKey(array $config): string
    {
        $widgetId = $this->getConfig()['id'] ?? 'unknown';
        $userId = Auth::id() ?? 'guest';
        $configHash = md5(json_encode($config));
        
        return "widget_{$widgetId}_{$userId}_{$configHash}";
    }
    
    /**
     * Get default HTML template when no view exists
     */
    protected function getDefaultTemplate(array $data): string
    {
        $title = $data['widget_config']['title'] ?? 'Widget';
        $content = json_encode($data, JSON_PRETTY_PRINT);
        
        return "
        <div class='widget-container'>
            <div class='widget-header'>
                <h3>{$title}</h3>
            </div>
            <div class='widget-content'>
                <pre>{$content}</pre>
            </div>
        </div>
        ";
    }
    
    /**
     * Format number for display
     */
    protected function formatNumber($number): string
    {
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'M';
        }
        
        if ($number >= 1000) {
            return number_format($number / 1000, 1) . 'K';
        }
        
        return number_format($number);
    }
    
    /**
     * Get date range for analytics
     */
    protected function getDateRange(array $config, int $defaultDays = 30): array
    {
        $endDate = now();
        $startDate = $endDate->copy()->subDays($config['days'] ?? $defaultDays);
        
        return [$startDate, $endDate];
    }
    
    /**
     * Handle widget errors gracefully
     */
    protected function handleError(\Exception $e, string $context = ''): array
    {
        \Log::error("Widget error: {$context}", [
            'widget' => get_class($this),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return [
            'error' => true,
            'message' => 'Widget failed to load',
            'debug' => config('app.debug') ? $e->getMessage() : null
        ];
    }
}
