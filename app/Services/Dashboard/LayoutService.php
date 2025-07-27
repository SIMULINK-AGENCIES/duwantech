<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LayoutService
{
    /**
     * Get user's dashboard layout configuration
     */
    public function getUserLayout(): array
    {
        $userId = Auth::id();
        
        return Cache::remember("user_layout_{$userId}", 3600, function () use ($userId) {
            $user = User::find($userId);
            
            return $user->dashboard_layout ?? $this->getDefaultLayout();
        });
    }
    
    /**
     * Save user's dashboard layout
     */
    public function saveUserLayout(array $layout): bool
    {
        $userId = Auth::id();
        $user = User::find($userId);
        
        $user->dashboard_layout = $layout;
        $result = $user->save();
        
        // Clear cache
        Cache::forget("user_layout_{$userId}");
        
        return $result;
    }
    
    /**
     * Get default dashboard layout
     */
    public function getDefaultLayout(): array
    {
        return [
            'theme' => 'light',
            'sidebar_collapsed' => false,
            'widgets' => [
                ['id' => 'metrics', 'position' => 1, 'size' => 'large'],
                ['id' => 'charts', 'position' => 2, 'size' => 'large'],
                ['id' => 'tables', 'position' => 3, 'size' => 'medium'],
                ['id' => 'activity', 'position' => 4, 'size' => 'small'],
            ],
            'grid_columns' => 4,
            'auto_refresh' => true,
            'refresh_interval' => 60000,
        ];
    }
    
    /**
     * Reset user layout to default
     */
    public function resetToDefault(): bool
    {
        return $this->saveUserLayout($this->getDefaultLayout());
    }
    
    /**
     * Get available layout templates
     */
    public function getTemplates(): array
    {
        return [
            'executive' => [
                'name' => 'Executive Dashboard',
                'description' => 'High-level KPIs and metrics',
                'widgets' => ['kpi-cards', 'revenue-chart', 'top-products', 'alerts']
            ],
            'operational' => [
                'name' => 'Operations Dashboard', 
                'description' => 'Detailed operational metrics',
                'widgets' => ['order-status', 'inventory-alerts', 'performance', 'activity-feed']
            ],
            'analytics' => [
                'name' => 'Analytics Dashboard',
                'description' => 'Comprehensive data analysis',
                'widgets' => ['multi-charts', 'trends', 'forecasting', 'custom-reports']
            ]
        ];
    }
}
