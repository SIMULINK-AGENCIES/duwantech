<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\LayoutService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class DashboardLayoutController extends Controller
{
    protected LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
        // Note: Middleware and authorization should be handled via routes or policies
    }

    /**
     * Display the user's current dashboard layout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $layout = $this->layoutService->getUserLayout();
            
            return response()->json([
                'success' => true,
                'message' => 'Layout retrieved successfully',
                'data' => [
                    'layout' => $layout,
                    'user_id' => Auth::id(),
                    'template' => $layout['template'] ?? config('dashboard.layout.default_template'),
                    'last_updated' => $layout['updated_at'] ?? now(),
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error retrieving dashboard layout', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard layout',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a new dashboard layout configuration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'template' => 'required|string|in:' . implode(',', array_keys(config('dashboard.layout.available_templates'))),
                'widgets' => 'required|array',
                'widgets.*.id' => 'required|string',
                'widgets.*.position' => 'required|array',
                'widgets.*.position.x' => 'required|integer|min:0',
                'widgets.*.position.y' => 'required|integer|min:0',
                'widgets.*.size' => 'required|array',
                'widgets.*.size.width' => 'required|integer|min:1|max:' . config('dashboard.layout.grid_columns'),
                'widgets.*.size.height' => 'required|integer|min:1',
                'widgets.*.config' => 'sometimes|array',
                'theme' => 'sometimes|string|in:' . implode(',', array_keys(config('dashboard.themes.available'))),
                'auto_refresh' => 'sometimes|boolean',
                'refresh_interval' => 'sometimes|integer|min:5000|max:300000', // 5s to 5min
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $layoutData = $validator->validated();
            
            // Add metadata
            $layoutData['created_by'] = $user->id;
            $layoutData['created_at'] = now();
            $layoutData['updated_at'] = now();

            $saved = $this->layoutService->saveUserLayout($layoutData);

            if ($saved) {
                $layout = $this->layoutService->getUserLayout();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Dashboard layout saved successfully',
                    'data' => [
                        'layout' => $layout,
                        'template' => $layoutData['template'],
                        'widget_count' => count($layoutData['widgets']),
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save dashboard layout',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error saving dashboard layout', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save dashboard layout',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update an existing dashboard layout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'template' => 'sometimes|string|in:' . implode(',', array_keys(config('dashboard.layout.available_templates'))),
                'widgets' => 'sometimes|array',
                'widgets.*.id' => 'required_with:widgets|string',
                'widgets.*.position' => 'required_with:widgets|array',
                'widgets.*.position.x' => 'required_with:widgets.*.position|integer|min:0',
                'widgets.*.position.y' => 'required_with:widgets.*.position|integer|min:0',
                'widgets.*.size' => 'required_with:widgets|array',
                'widgets.*.size.width' => 'required_with:widgets.*.size|integer|min:1|max:' . config('dashboard.layout.grid_columns'),
                'widgets.*.size.height' => 'required_with:widgets.*.size|integer|min:1',
                'widgets.*.config' => 'sometimes|array',
                'theme' => 'sometimes|string|in:' . implode(',', array_keys(config('dashboard.themes.available'))),
                'auto_refresh' => 'sometimes|boolean',
                'refresh_interval' => 'sometimes|integer|min:5000|max:300000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $updateData = $validator->validated();
            $updateData['updated_at'] = now();

            // Get current layout and merge with updates
            $currentLayout = $this->layoutService->getUserLayout();
            $mergedLayout = array_merge($currentLayout, $updateData);
            
            $updated = $this->layoutService->saveUserLayout($mergedLayout);

            if ($updated) {
                $layout = $this->layoutService->getUserLayout();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Dashboard layout updated successfully',
                    'data' => [
                        'layout' => $layout,
                        'updated_fields' => array_keys($updateData),
                        'widget_count' => isset($updateData['widgets']) ? count($updateData['widgets']) : count($layout['widgets'] ?? []),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update dashboard layout',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error updating dashboard layout', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update dashboard layout',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete/remove a dashboard layout (reset to default)
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        try {
            $reset = $this->layoutService->resetToDefault();

            if ($reset) {
                $defaultLayout = $this->layoutService->getUserLayout();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Dashboard layout reset to default successfully',
                    'data' => [
                        'layout' => $defaultLayout,
                        'template' => $defaultLayout['template'] ?? config('dashboard.layout.default_template'),
                        'reset_at' => now(),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reset dashboard layout',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error resetting dashboard layout', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset dashboard layout',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available dashboard templates
     *
     * @return JsonResponse
     */
    public function templates(): JsonResponse
    {
        try {
            $templates = $this->layoutService->getTemplates();
            $defaultTemplate = config('dashboard.layout.default_template');

            return response()->json([
                'success' => true,
                'message' => 'Templates retrieved successfully',
                'data' => [
                    'templates' => $templates,
                    'default_template' => $defaultTemplate,
                    'total_templates' => count($templates),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving dashboard templates', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve templates',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Reset dashboard layout to default
     *
     * @return JsonResponse
     */
    public function reset(): JsonResponse
    {
        try {
            $user = Auth::user();
            $reset = $this->layoutService->resetToDefault();

            if ($reset) {
                $defaultLayout = $this->layoutService->getUserLayout();
                
                Log::info('Dashboard layout reset to default', [
                    'user_id' => $user->id,
                    'template' => $defaultLayout['template'] ?? config('dashboard.layout.default_template'),
                    'reset_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dashboard layout has been reset to default configuration',
                    'data' => [
                        'layout' => $defaultLayout,
                        'template' => $defaultLayout['template'] ?? config('dashboard.layout.default_template'),
                        'widget_count' => count($defaultLayout['widgets'] ?? []),
                        'reset_at' => now(),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reset dashboard layout to default',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error during dashboard layout reset', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset dashboard layout to default',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
