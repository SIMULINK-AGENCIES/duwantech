<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Log;

class WidgetDependencyManager
{
    private array $registeredWidgets = [];
    private array $dependencyGraph = [];
    private array $resolvedOrder = [];
    
    /**
     * Set registered widgets for dependency resolution
     */
    public function setRegisteredWidgets(array $widgets): void
    {
        $this->registeredWidgets = $widgets;
        $this->buildDependencyGraph();
    }
    
    /**
     * Validate widget dependencies
     */
    public function validateDependencies(array $dependencies): array
    {
        $errors = [];
        
        foreach ($dependencies as $dependency) {
            if (is_string($dependency)) {
                // Simple dependency format: just widget ID
                $widgetId = $dependency;
                $constraints = [];
            } elseif (is_array($dependency)) {
                // Complex dependency format with constraints
                $widgetId = $dependency['id'] ?? null;
                $constraints = $dependency;
            } else {
                $errors[] = "Invalid dependency format: " . json_encode($dependency);
                continue;
            }
            
            if (!$widgetId) {
                $errors[] = "Dependency missing widget ID: " . json_encode($dependency);
                continue;
            }
            
            // Check if dependency widget exists
            if (!isset($this->registeredWidgets[$widgetId])) {
                $errors[] = "Dependency widget not found: {$widgetId}";
                continue;
            }
            
            // Validate version constraints
            if (isset($constraints['version'])) {
                $dependencyWidget = $this->registeredWidgets[$widgetId];
                $requiredVersion = $constraints['version'];
                $currentVersion = $dependencyWidget['version'] ?? '1.0.0';
                
                if (!$this->satisfiesVersionConstraint($currentVersion, $requiredVersion)) {
                    $errors[] = "Version constraint not satisfied for {$widgetId}: requires {$requiredVersion}, found {$currentVersion}";
                }
            }
            
            // Validate category constraints
            if (isset($constraints['category'])) {
                $dependencyWidget = $this->registeredWidgets[$widgetId];
                $requiredCategory = $constraints['category'];
                $currentCategory = $dependencyWidget['category'] ?? null;
                
                if ($currentCategory !== $requiredCategory) {
                    $errors[] = "Category constraint not satisfied for {$widgetId}: requires {$requiredCategory}, found {$currentCategory}";
                }
            }
            
            // Validate permission constraints
            if (isset($constraints['permissions'])) {
                $requiredPermissions = (array) $constraints['permissions'];
                $this->validatePermissionConstraints($widgetId, $requiredPermissions, $errors);
            }
        }
        
        return $errors;
    }
    
    /**
     * Check for circular dependencies
     */
    public function detectCircularDependencies(string $widgetId, array $dependencies): array
    {
        $visited = [];
        $recursionStack = [];
        $cycles = [];
        
        $this->detectCircularDependenciesRecursive($widgetId, $dependencies, $visited, $recursionStack, $cycles);
        
        return $cycles;
    }
    
    /**
     * Resolve widget loading order based on dependencies
     */
    public function resolveLoadingOrder(array $widgetIds): array
    {
        $resolved = [];
        $visiting = [];
        $visited = [];
        
        foreach ($widgetIds as $widgetId) {
            if (!isset($visited[$widgetId])) {
                $this->resolveLoadingOrderRecursive($widgetId, $resolved, $visiting, $visited);
            }
        }
        
        return array_reverse($resolved);
    }
    
    /**
     * Get widget dependencies (direct and transitive)
     */
    public function getWidgetDependencies(string $widgetId, bool $recursive = false): array
    {
        if (!isset($this->registeredWidgets[$widgetId])) {
            return [];
        }
        
        $widget = $this->registeredWidgets[$widgetId];
        $dependencies = $widget['dependencies'] ?? [];
        
        if (!$recursive) {
            return $this->normalizeDependencies($dependencies);
        }
        
        $allDependencies = [];
        $this->collectDependenciesRecursive($widgetId, $allDependencies, []);
        
        return array_unique($allDependencies);
    }
    
    /**
     * Get widgets that depend on a specific widget (reverse dependencies)
     */
    public function getReverseDependencies(string $widgetId): array
    {
        $reverseDependencies = [];
        
        foreach ($this->registeredWidgets as $id => $widget) {
            $dependencies = $this->getWidgetDependencies($id);
            
            if (in_array($widgetId, $dependencies)) {
                $reverseDependencies[] = $id;
            }
        }
        
        return $reverseDependencies;
    }
    
    /**
     * Check if widgets are compatible for co-existence
     */
    public function checkCompatibility(array $widgetIds): array
    {
        $incompatibilities = [];
        
        foreach ($widgetIds as $widgetId) {
            if (!isset($this->registeredWidgets[$widgetId])) {
                continue;
            }
            
            $widget = $this->registeredWidgets[$widgetId];
            $conflicts = $widget['conflicts'] ?? [];
            
            foreach ($conflicts as $conflictId) {
                if (in_array($conflictId, $widgetIds)) {
                    $incompatibilities[] = [
                        'widget1' => $widgetId,
                        'widget2' => $conflictId,
                        'reason' => 'explicit_conflict'
                    ];
                }
            }
        }
        
        return $incompatibilities;
    }
    
    /**
     * Get dependency graph visualization data
     */
    public function getDependencyGraph(): array
    {
        $nodes = [];
        $edges = [];
        
        foreach ($this->registeredWidgets as $widgetId => $widget) {
            $nodes[] = [
                'id' => $widgetId,
                'label' => $widget['title'] ?? $widgetId,
                'category' => $widget['category'] ?? 'general',
                'version' => $widget['version'] ?? '1.0.0'
            ];
            
            $dependencies = $this->getWidgetDependencies($widgetId);
            foreach ($dependencies as $depId) {
                $edges[] = [
                    'from' => $depId,
                    'to' => $widgetId,
                    'type' => 'dependency'
                ];
            }
        }
        
        return [
            'nodes' => $nodes,
            'edges' => $edges
        ];
    }
    
    /**
     * Validate that dependencies can be satisfied for a user's widget selection
     */
    public function validateUserWidgetSelection(array $selectedWidgets, int $userId = null): array
    {
        $errors = [];
        $warnings = [];
        
        // Check that all dependencies are included
        foreach ($selectedWidgets as $widgetId) {
            $dependencies = $this->getWidgetDependencies($widgetId, true);
            
            foreach ($dependencies as $depId) {
                if (!in_array($depId, $selectedWidgets)) {
                    $errors[] = "Widget '{$widgetId}' requires '{$depId}' but it's not selected";
                }
            }
        }
        
        // Check for conflicts
        $conflicts = $this->checkCompatibility($selectedWidgets);
        foreach ($conflicts as $conflict) {
            $warnings[] = "Widgets '{$conflict['widget1']}' and '{$conflict['widget2']}' may conflict";
        }
        
        // Check loading order
        try {
            $loadingOrder = $this->resolveLoadingOrder($selectedWidgets);
        } catch (\Exception $e) {
            $errors[] = "Cannot resolve loading order: " . $e->getMessage();
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'loading_order' => $loadingOrder ?? []
        ];
    }
    
    /**
     * Private helper methods
     */
    
    private function buildDependencyGraph(): void
    {
        $this->dependencyGraph = [];
        
        foreach ($this->registeredWidgets as $widgetId => $widget) {
            $dependencies = $this->normalizeDependencies($widget['dependencies'] ?? []);
            $this->dependencyGraph[$widgetId] = $dependencies;
        }
    }
    
    private function normalizeDependencies(array $dependencies): array
    {
        $normalized = [];
        
        foreach ($dependencies as $dependency) {
            if (is_string($dependency)) {
                $normalized[] = $dependency;
            } elseif (is_array($dependency) && isset($dependency['id'])) {
                $normalized[] = $dependency['id'];
            }
        }
        
        return $normalized;
    }
    
    private function satisfiesVersionConstraint(string $currentVersion, string $requiredVersion): bool
    {
        // Simple version comparison - can be enhanced with semantic versioning
        if (strpos($requiredVersion, '>=') === 0) {
            $minVersion = trim(substr($requiredVersion, 2));
            return version_compare($currentVersion, $minVersion, '>=');
        }
        
        if (strpos($requiredVersion, '>') === 0) {
            $minVersion = trim(substr($requiredVersion, 1));
            return version_compare($currentVersion, $minVersion, '>');
        }
        
        if (strpos($requiredVersion, '<=') === 0) {
            $maxVersion = trim(substr($requiredVersion, 2));
            return version_compare($currentVersion, $maxVersion, '<=');
        }
        
        if (strpos($requiredVersion, '<') === 0) {
            $maxVersion = trim(substr($requiredVersion, 1));
            return version_compare($currentVersion, $maxVersion, '<');
        }
        
        if (strpos($requiredVersion, '^') === 0) {
            // Caret constraint - compatible version
            $baseVersion = trim(substr($requiredVersion, 1));
            return $this->isCompatibleVersion($currentVersion, $baseVersion);
        }
        
        // Exact version match
        return version_compare($currentVersion, $requiredVersion, '=');
    }
    
    private function isCompatibleVersion(string $currentVersion, string $baseVersion): bool
    {
        $currentParts = explode('.', $currentVersion);
        $baseParts = explode('.', $baseVersion);
        
        // Major version must match
        if (($currentParts[0] ?? 0) !== ($baseParts[0] ?? 0)) {
            return false;
        }
        
        // Current version must be >= base version
        return version_compare($currentVersion, $baseVersion, '>=');
    }
    
    private function validatePermissionConstraints(string $widgetId, array $requiredPermissions, array &$errors): void
    {
        $dependencyWidget = $this->registeredWidgets[$widgetId];
        $widgetPermissions = $dependencyWidget['permissions'] ?? [];
        
        foreach ($requiredPermissions as $permission) {
            if (!in_array($permission, $widgetPermissions)) {
                $errors[] = "Widget {$widgetId} does not provide required permission: {$permission}";
            }
        }
    }
    
    private function detectCircularDependenciesRecursive(
        string $widgetId, 
        array $dependencies, 
        array &$visited, 
        array &$recursionStack, 
        array &$cycles
    ): void {
        $visited[$widgetId] = true;
        $recursionStack[$widgetId] = true;
        
        $deps = $this->normalizeDependencies($dependencies);
        
        foreach ($deps as $depId) {
            if (!isset($visited[$depId])) {
                $depWidget = $this->registeredWidgets[$depId] ?? null;
                if ($depWidget) {
                    $this->detectCircularDependenciesRecursive(
                        $depId, 
                        $depWidget['dependencies'] ?? [], 
                        $visited, 
                        $recursionStack, 
                        $cycles
                    );
                }
            } elseif (isset($recursionStack[$depId]) && $recursionStack[$depId]) {
                $cycles[] = "Circular dependency detected: {$widgetId} -> {$depId}";
            }
        }
        
        unset($recursionStack[$widgetId]);
    }
    
    private function resolveLoadingOrderRecursive(
        string $widgetId, 
        array &$resolved, 
        array &$visiting, 
        array &$visited
    ): void {
        if (isset($visiting[$widgetId])) {
            throw new \RuntimeException("Circular dependency detected involving: {$widgetId}");
        }
        
        if (isset($visited[$widgetId])) {
            return;
        }
        
        $visiting[$widgetId] = true;
        
        $dependencies = $this->getWidgetDependencies($widgetId);
        foreach ($dependencies as $depId) {
            $this->resolveLoadingOrderRecursive($depId, $resolved, $visiting, $visited);
        }
        
        unset($visiting[$widgetId]);
        $visited[$widgetId] = true;
        $resolved[] = $widgetId;
    }
    
    private function collectDependenciesRecursive(string $widgetId, array &$allDependencies, array $visited): void
    {
        if (in_array($widgetId, $visited)) {
            return; // Prevent infinite loops
        }
        
        $visited[] = $widgetId;
        $dependencies = $this->getWidgetDependencies($widgetId);
        
        foreach ($dependencies as $depId) {
            if (!in_array($depId, $allDependencies)) {
                $allDependencies[] = $depId;
                $this->collectDependenciesRecursive($depId, $allDependencies, $visited);
            }
        }
    }
}
