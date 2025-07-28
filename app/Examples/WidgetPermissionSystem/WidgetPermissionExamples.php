<?php

namespace App\Examples\WidgetPermissionSystem;

use App\Services\Dashboard\WidgetService;
use App\Services\Dashboard\WidgetPermissionManager;
use App\Services\Dashboard\WidgetFeatureFlagService;
use App\Models\User;

/**
 * Example usage of the Widget Permission System
 * Demonstrates role-based access, feature flags, and dynamic permissions
 */
class WidgetPermissionExamples
{
    private WidgetService $widgetService;
    private WidgetPermissionManager $permissionManager;
    private WidgetFeatureFlagService $featureFlagService;
    
    public function __construct()
    {
        $this->widgetService = app(WidgetService::class);
        $this->permissionManager = new WidgetPermissionManager();
        $this->featureFlagService = new WidgetFeatureFlagService();
    }
    
    /**
     * Example 1: Basic Permission-Based Widget Registration
     */
    public function basicPermissionExample()
    {
        echo "=== Basic Permission Example ===\n";
        
        // Register widget with basic permissions
        $this->widgetService->register('user_management', [
            'title' => 'User Management Dashboard',
            'description' => 'Manage system users',
            'category' => 'admin',
            'version' => '1.0.0',
            'permissions' => ['manage_users'], // Simple permission requirement
            'dependencies' => []
        ]);
        
        // Register widget with action-specific permissions
        $this->widgetService->register('financial_reports', [
            'title' => 'Financial Reports',
            'description' => 'View and manage financial data',
            'category' => 'reports',
            'version' => '2.1.0',
            'permissions' => [
                'view' => ['view_reports'],
                'configure' => ['configure_reports'],
                'manage' => ['manage_reports', 'admin_access']
            ],
            'dependencies' => []
        ]);
        
        echo "✅ Widgets registered with permission requirements\n\n";
    }
    
    /**
     * Example 2: Role-Based Widget Access
     */
    public function roleBasedAccessExample()
    {
        echo "=== Role-Based Access Example ===\n";
        
        // Register widget with role-based permissions
        $this->widgetService->register('admin_dashboard', [
            'title' => 'Administrative Dashboard',
            'description' => 'Administrative control panel',
            'category' => 'admin',
            'version' => '3.0.0',
            'role_permissions' => [
                [
                    'roles' => ['admin', 'super-admin'],
                    'actions' => ['view', 'configure', 'manage'],
                    'denied_roles' => ['guest', 'banned']
                ],
                [
                    'roles' => ['manager'],
                    'actions' => ['view', 'configure'],
                    'denied_roles' => []
                ]
            ],
            'dependencies' => []
        ]);
        
        echo "✅ Widget registered with role-based access controls\n\n";
    }
    
    /**
     * Example 3: Feature Flag Integration
     */
    public function featureFlagExample()
    {
        echo "=== Feature Flag Integration Example ===\n";
        
        // Setup feature flags
        $this->featureFlagService->registerFlag('beta_analytics', [
            'enabled' => true,
            'strategy' => WidgetFeatureFlagService::STRATEGY_PERCENTAGE,
            'value' => 30, // 30% rollout
            'metadata' => ['description' => 'Beta analytics features']
        ]);
        
        $this->featureFlagService->registerFlag('premium_widgets', [
            'enabled' => true,
            'strategy' => WidgetFeatureFlagService::STRATEGY_WHITELIST,
            'conditions' => [
                'roles' => ['premium', 'admin']
            ]
        ]);
        
        // Register widget with feature flags
        $this->widgetService->register('advanced_analytics', [
            'title' => 'Advanced Analytics Widget',
            'description' => 'Advanced analytics with ML insights',
            'category' => 'analytics',
            'version' => '1.0.0-beta',
            'feature_flags' => [
                'beta_analytics', // Simple flag
                [
                    'name' => 'premium_widgets',
                    'context' => WidgetFeatureFlagService::CONTEXT_USER,
                    'required' => true
                ]
            ],
            'permissions' => ['view_analytics'],
            'dependencies' => []
        ]);
        
        echo "✅ Widget registered with feature flag requirements\n\n";
    }
    
    /**
     * Example 4: Time-Based and Custom Permissions
     */
    public function advancedPermissionExample()
    {
        echo "=== Advanced Permission Example ===\n";
        
        // Register widget with time-based permissions
        $this->widgetService->register('trading_dashboard', [
            'title' => 'Trading Dashboard',
            'description' => 'Real-time trading interface',
            'category' => 'trading',
            'version' => '2.0.0',
            'custom_permissions' => [
                [
                    'type' => 'time_based',
                    'timezone' => 'America/New_York',
                    'time_ranges' => [
                        ['start' => '09:30', 'end' => '16:00'] // Market hours
                    ],
                    'days_of_week' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']
                ],
                [
                    'type' => 'callback',
                    'callback' => function($user, $action, $rule) {
                        // Custom logic: check if user has sufficient account balance
                        return $user->account_balance >= 1000;
                    }
                ]
            ],
            'permissions' => ['access_trading'],
            'dependencies' => []
        ]);
        
        echo "✅ Widget registered with time-based and custom permissions\n\n";
    }
    
    /**
     * Example 5: Multi-Tenant Permissions
     */
    public function multiTenantExample()
    {
        echo "=== Multi-Tenant Permission Example ===\n";
        
        // Register widget with tenant-specific permissions
        $this->widgetService->register('tenant_analytics', [
            'title' => 'Tenant Analytics',
            'description' => 'Analytics scoped to tenant',
            'category' => 'analytics',
            'version' => '1.5.0',
            'tenant_permissions' => [
                'require_tenant' => true,
                'permissions' => ['view_tenant_data'],
                'roles' => ['tenant_admin', 'tenant_user']
            ],
            'permissions' => ['basic_access'],
            'dependencies' => []
        ]);
        
        echo "✅ Widget registered with multi-tenant permissions\n\n";
    }
    
    /**
     * Example 6: Testing Permission Scenarios
     */
    public function permissionTestingExample()
    {
        echo "=== Permission Testing Example ===\n";
        
        // Create test users (in real app, these would come from database)
        $adminUser = $this->createMockUser(1, 'admin@example.com', ['admin']);
        $managerUser = $this->createMockUser(2, 'manager@example.com', ['manager']);
        $regularUser = $this->createMockUser(3, 'user@example.com', ['user']);
        
        $widgetId = 'admin_dashboard';
        
        // Test different permission levels
        $testScenarios = [
            ['user' => $adminUser, 'action' => 'view'],
            ['user' => $adminUser, 'action' => 'manage'],
            ['user' => $managerUser, 'action' => 'view'],
            ['user' => $managerUser, 'action' => 'manage'],
            ['user' => $regularUser, 'action' => 'view'],
        ];
        
        foreach ($testScenarios as $scenario) {
            $canAccess = $this->permissionManager->canUserAccessWidget(
                $scenario['user'], 
                $widgetId, 
                $scenario['action']
            );
            
            $userRole = $scenario['user']->roles[0] ?? 'none';
            $status = $canAccess ? '✅ ALLOWED' : '❌ DENIED';
            
            echo "  {$userRole} user {$scenario['action']} access: {$status}\n";
        }
        
        echo "\n";
    }
    
    /**
     * Example 7: Permission Audit Trail
     */
    public function auditTrailExample()
    {
        echo "=== Permission Audit Trail Example ===\n";
        
        $user = $this->createMockUser(1, 'test@example.com', ['user']);
        $widgetId = 'financial_reports';
        
        $auditTrail = $this->permissionManager->getPermissionAuditTrail($user, $widgetId, 'manage');
        
        echo "Audit Trail for user {$user->id} accessing {$widgetId}:\n";
        echo "  Result: " . ($auditTrail['result'] ? 'ALLOWED' : 'DENIED') . "\n";
        echo "  Checks:\n";
        
        foreach ($auditTrail['checks'] as $checkName => $result) {
            $status = $result ? '✅' : '❌';
            echo "    {$checkName}: {$status}\n";
        }
        
        echo "\n";
    }
    
    /**
     * Example 8: Bulk Permission Checking
     */
    public function bulkPermissionExample()
    {
        echo "=== Bulk Permission Example ===\n";
        
        $user = $this->createMockUser(1, 'admin@example.com', ['admin']);
        $widgetIds = ['user_management', 'financial_reports', 'admin_dashboard', 'advanced_analytics'];
        
        $permissions = $this->permissionManager->checkBulkPermissions($user, $widgetIds, 'view');
        
        echo "Bulk permission check for admin user:\n";
        foreach ($permissions as $widgetId => $allowed) {
            $status = $allowed ? '✅ ALLOWED' : '❌ DENIED';
            echo "  {$widgetId}: {$status}\n";
        }
        
        echo "\n";
    }
    
    /**
     * Example 9: Accessible Widgets Filtering
     */
    public function accessibleWidgetsExample()
    {
        echo "=== Accessible Widgets Filtering Example ===\n";
        
        $managerUser = $this->createMockUser(2, 'manager@example.com', ['manager']);
        
        $accessibleWidgets = $this->permissionManager->getAccessibleWidgets($managerUser, 'view');
        
        echo "Widgets accessible to manager user:\n";
        foreach ($accessibleWidgets as $widgetId => $widget) {
            echo "  - {$widgetId}: {$widget['title']}\n";
        }
        
        echo "\n";
    }
    
    /**
     * Example 10: Feature Flag Testing
     */
    public function featureFlagTestingExample()
    {
        echo "=== Feature Flag Testing Example ===\n";
        
        $users = [
            $this->createMockUser(1, 'admin@example.com', ['admin']),
            $this->createMockUser(2, 'premium@example.com', ['premium']),
            $this->createMockUser(3, 'user@example.com', ['user'])
        ];
        
        foreach ($users as $user) {
            echo "Feature flags for {$user->email}:\n";
            
            $flags = [
                'beta_analytics' => $this->featureFlagService->isEnabled('beta_analytics', $user),
                'premium_widgets' => $this->featureFlagService->isEnabled('premium_widgets', $user),
                'widget_caching' => $this->featureFlagService->isEnabled('widget_caching', $user)
            ];
            
            foreach ($flags as $flagName => $enabled) {
                $status = $enabled ? '✅ ENABLED' : '❌ DISABLED';
                echo "  {$flagName}: {$status}\n";
            }
            echo "\n";
        }
    }
    
    /**
     * Run all examples
     */
    public function runAllExamples()
    {
        echo "🔐 Widget Permission System Examples\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        $this->basicPermissionExample();
        $this->roleBasedAccessExample();
        $this->featureFlagExample();
        $this->advancedPermissionExample();
        $this->multiTenantExample();
        $this->permissionTestingExample();
        $this->auditTrailExample();
        $this->bulkPermissionExample();
        $this->accessibleWidgetsExample();
        $this->featureFlagTestingExample();
        
        echo "✅ All permission system examples completed!\n";
    }
    
    /**
     * Helper method to create mock users for testing
     */
    private function createMockUser(int $id, string $email, array $roles): User
    {
        $user = new User();
        $user->id = $id;
        $user->email = $email;
        $user->roles = $roles; // Simplified for example
        $user->account_balance = rand(500, 5000); // For custom permission example
        $user->tenant_id = 'tenant_' . ($id % 3 + 1); // For multi-tenant example
        
        return $user;
    }
}

// Usage example:
// $examples = new WidgetPermissionExamples();
// $examples->runAllExamples();
