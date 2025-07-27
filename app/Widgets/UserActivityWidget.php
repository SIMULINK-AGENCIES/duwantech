<?php

namespace App\Widgets;

use App\Models\User;
use App\Models\Order;
use App\Widgets\BaseWidget;

/**
 * User Activity Overview Widget
 */
class UserActivityWidget extends BaseWidget
{
    protected function getWidgetConfig(): array
    {
        return [
            'id' => 'user_activity',
            'title' => 'User Activity Overview',
            'description' => 'Display user registration and activity statistics',
            'category' => 'users',
            'size' => ['width' => 6, 'height' => 4],
            'permissions' => ['admin.users.view'],
            'supports_config' => true,
            'refresh_interval' => 300, // 5 minutes
            'tags' => ['users', 'activity', 'statistics'],
            'version' => '1.0.0',
            'author' => 'DuwanTech',
            'template' => 'admin.widgets.user-activity'
        ];
    }
    
    protected function getWidgetData(array $config = []): array
    {
        try {
            [$startDate, $endDate] = $this->getDateRange($config, 30);
            
            // Get user statistics
            $totalUsers = User::count();
            $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
            $activeUsers = User::where('last_login_at', '>=', now()->subDays(7))->count();
            
            // Get user growth data for chart
            $userGrowth = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $count = User::whereDate('created_at', $date)->count();
                $userGrowth[] = [
                    'date' => $date->format('Y-m-d'),
                    'count' => $count
                ];
            }
            
            // Calculate growth percentage
            $previousPeriodUsers = User::whereBetween('created_at', [
                $startDate->copy()->subDays(30),
                $startDate
            ])->count();
            
            $growthPercentage = $previousPeriodUsers > 0 
                ? (($newUsers - $previousPeriodUsers) / $previousPeriodUsers) * 100 
                : 0;
            
            return [
                'total_users' => $totalUsers,
                'new_users' => $newUsers,
                'active_users' => $activeUsers,
                'growth_percentage' => round($growthPercentage, 1),
                'user_growth' => $userGrowth,
                'period_days' => $config['days'] ?? 30,
                'last_updated' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            return $this->handleError($e, 'fetching user activity data');
        }
    }
    
    public function getConfigSchema(): array
    {
        return [
            'days' => 'integer|min:1|max:365',
            'chart_type' => 'string|in:line,bar,area',
            'show_percentage' => 'boolean',
            'include_inactive' => 'boolean'
        ];
    }
}
