<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\WidgetService;
use App\Services\Dashboard\LayoutService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class WidgetController extends Controller
{
    protected WidgetService $widgetService;
    protected LayoutService $layoutService;

    public function __construct(WidgetService $widgetService, LayoutService $layoutService)
    {
        $this->widgetService = $widgetService;
        $this->layoutService = $layoutService;
        // Note: Middleware and authorization should be handled via routes or policies
    }

    /**
     * Get user's current dashboard widgets
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $layout = $this->layoutService->getUserLayout();
            $userWidgets = $layout['widgets'] ?? [];
            
            // Enrich widgets with configuration and metadata
            $enrichedWidgets = [];
            foreach ($userWidgets as $widget) {
                $widgetConfig = $this->widgetService->getWidget($widget['id']);
                if ($widgetConfig) {
                    $enrichedWidgets[] = array_merge($widget, [
                        'config' => $widgetConfig,
                        'category' => $widgetConfig['category'] ?? 'general',
                        'title' => $widgetConfig['title'] ?? $widget['id'],
                        'description' => $widgetConfig['description'] ?? '',
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User widgets retrieved successfully',
                'data' => [
                    'widgets' => $enrichedWidgets,
                    'total_widgets' => count($enrichedWidgets),
                    'user_id' => Auth::id(),
                    'last_updated' => $layout['updated_at'] ?? now(),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving user widgets', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user widgets',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get all available widgets for dashboard
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $category = $request->query('category');
            
            if ($category) {
                $widgets = $this->widgetService->getByCategory($category);
                $message = "Available widgets for category '{$category}' retrieved successfully";
            } else {
                $widgets = $this->widgetService->getAvailable();
                $message = 'All available widgets retrieved successfully';
            }

            // Group widgets by category for better organization
            $groupedWidgets = [];
            $categories = [];
            
            foreach ($widgets as $id => $widget) {
                $widgetCategory = $widget['category'] ?? 'general';
                $groupedWidgets[$widgetCategory][] = array_merge($widget, ['id' => $id]);
                
                if (!in_array($widgetCategory, $categories)) {
                    $categories[] = $widgetCategory;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'widgets' => $widgets,
                    'grouped_widgets' => $groupedWidgets,
                    'categories' => $categories,
                    'total_widgets' => count($widgets),
                    'filtered_category' => $category,
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving available widgets', [
                'user_id' => Auth::id(),
                'category' => $request->query('category'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available widgets',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Add a new widget to user's dashboard
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'widget_id' => 'required|string',
                'position' => 'required|array',
                'position.x' => 'required|integer|min:0',
                'position.y' => 'required|integer|min:0',
                'size' => 'required|array',
                'size.width' => 'required|integer|min:1|max:' . config('dashboard.layout.grid_columns'),
                'size.height' => 'required|integer|min:1',
                'config' => 'sometimes|array',
                'title' => 'sometimes|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $widgetId = $data['widget_id'];

            // Verify widget exists in available widgets
            $widgetConfig = $this->widgetService->getWidget($widgetId);
            if (!$widgetConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget not found or not available',
                    'error' => "Widget '{$widgetId}' does not exist"
                ], 404);
            }

            // Get current layout and add new widget
            $layout = $this->layoutService->getUserLayout();
            $currentWidgets = $layout['widgets'] ?? [];

            // Check if widget already exists
            foreach ($currentWidgets as $existingWidget) {
                if ($existingWidget['id'] === $widgetId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Widget already exists on dashboard',
                        'error' => "Widget '{$widgetId}' is already added to the dashboard"
                    ], 409);
                }
            }

            // Check maximum widgets limit
            $maxWidgets = config('dashboard.widgets.max_widgets_per_user', 20);
            if (count($currentWidgets) >= $maxWidgets) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum widgets limit reached',
                    'error' => "Cannot add more than {$maxWidgets} widgets"
                ], 409);
            }

            // Create new widget entry
            $newWidget = [
                'id' => $widgetId,
                'position' => $data['position'],
                'size' => $data['size'],
                'config' => $data['config'] ?? [],
                'title' => $data['title'] ?? $widgetConfig['title'] ?? $widgetId,
                'added_at' => now()->toISOString(),
            ];

            // Add widget to layout
            $currentWidgets[] = $newWidget;
            $layout['widgets'] = $currentWidgets;
            $layout['updated_at'] = now();

            // Save updated layout
            $saved = $this->layoutService->saveUserLayout($layout);

            if ($saved) {
                return response()->json([
                    'success' => true,
                    'message' => 'Widget added to dashboard successfully',
                    'data' => [
                        'widget' => array_merge($newWidget, ['config' => $widgetConfig]),
                        'total_widgets' => count($currentWidgets),
                        'position' => $data['position'],
                        'size' => $data['size'],
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add widget to dashboard',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error adding widget to dashboard', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add widget to dashboard',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update an existing widget on user's dashboard
     *
     * @param Request $request
     * @param string $widgetId
     * @return JsonResponse
     */
    public function update(Request $request, string $widgetId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'position' => 'sometimes|array',
                'position.x' => 'required_with:position|integer|min:0',
                'position.y' => 'required_with:position|integer|min:0',
                'size' => 'sometimes|array',
                'size.width' => 'required_with:size|integer|min:1|max:' . config('dashboard.layout.grid_columns'),
                'size.height' => 'required_with:size|integer|min:1',
                'config' => 'sometimes|array',
                'title' => 'sometimes|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $validator->validated();

            // Get current layout
            $layout = $this->layoutService->getUserLayout();
            $currentWidgets = $layout['widgets'] ?? [];

            // Find and update the widget
            $widgetFound = false;
            $updatedWidgets = [];

            foreach ($currentWidgets as $widget) {
                if ($widget['id'] === $widgetId) {
                    $widgetFound = true;
                    // Merge update data with existing widget
                    $updatedWidget = array_merge($widget, $updateData);
                    $updatedWidget['updated_at'] = now()->toISOString();
                    $updatedWidgets[] = $updatedWidget;
                } else {
                    $updatedWidgets[] = $widget;
                }
            }

            if (!$widgetFound) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget not found on dashboard',
                    'error' => "Widget '{$widgetId}' does not exist on the dashboard"
                ], 404);
            }

            // Update layout
            $layout['widgets'] = $updatedWidgets;
            $layout['updated_at'] = now();

            // Save updated layout
            $saved = $this->layoutService->saveUserLayout($layout);

            if ($saved) {
                // Get widget config for response
                $widgetConfig = $this->widgetService->getWidget($widgetId);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Widget updated successfully',
                    'data' => [
                        'widget' => array_merge($updatedWidget, ['config' => $widgetConfig]),
                        'updated_fields' => array_keys($updateData),
                        'widget_id' => $widgetId,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update widget',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error updating widget', [
                'user_id' => Auth::id(),
                'widget_id' => $widgetId,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update widget',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove a widget from user's dashboard
     *
     * @param string $widgetId
     * @return JsonResponse
     */
    public function destroy(string $widgetId): JsonResponse
    {
        try {
            // Get current layout
            $layout = $this->layoutService->getUserLayout();
            $currentWidgets = $layout['widgets'] ?? [];

            // Find and remove the widget
            $widgetFound = false;
            $filteredWidgets = [];

            foreach ($currentWidgets as $widget) {
                if ($widget['id'] === $widgetId) {
                    $widgetFound = true;
                    // Skip this widget (remove it)
                } else {
                    $filteredWidgets[] = $widget;
                }
            }

            if (!$widgetFound) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget not found on dashboard',
                    'error' => "Widget '{$widgetId}' does not exist on the dashboard"
                ], 404);
            }

            // Update layout
            $layout['widgets'] = $filteredWidgets;
            $layout['updated_at'] = now();

            // Save updated layout
            $saved = $this->layoutService->saveUserLayout($layout);

            if ($saved) {
                return response()->json([
                    'success' => true,
                    'message' => 'Widget removed from dashboard successfully',
                    'data' => [
                        'removed_widget_id' => $widgetId,
                        'remaining_widgets' => count($filteredWidgets),
                        'removed_at' => now(),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove widget from dashboard',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error removing widget from dashboard', [
                'user_id' => Auth::id(),
                'widget_id' => $widgetId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove widget from dashboard',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Reorder widgets on user's dashboard
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'widgets' => 'required|array',
                'widgets.*.id' => 'required|string',
                'widgets.*.position' => 'required|array',
                'widgets.*.position.x' => 'required|integer|min:0',
                'widgets.*.position.y' => 'required|integer|min:0',
                'widgets.*.size' => 'sometimes|array',
                'widgets.*.size.width' => 'required_with:widgets.*.size|integer|min:1|max:' . config('dashboard.layout.grid_columns'),
                'widgets.*.size.height' => 'required_with:widgets.*.size|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $newOrder = $validator->validated()['widgets'];

            // Get current layout
            $layout = $this->layoutService->getUserLayout();
            $currentWidgets = $layout['widgets'] ?? [];

            // Create a map of current widgets for easy lookup
            $currentWidgetMap = [];
            foreach ($currentWidgets as $widget) {
                $currentWidgetMap[$widget['id']] = $widget;
            }

            // Verify all widgets in new order exist in current dashboard
            $reorderedWidgets = [];
            foreach ($newOrder as $widgetOrder) {
                $widgetId = $widgetOrder['id'];
                
                if (!isset($currentWidgetMap[$widgetId])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Widget not found on dashboard',
                        'error' => "Widget '{$widgetId}' does not exist on the dashboard"
                    ], 404);
                }

                // Merge existing widget with new position/size
                $widget = $currentWidgetMap[$widgetId];
                $widget['position'] = $widgetOrder['position'];
                
                if (isset($widgetOrder['size'])) {
                    $widget['size'] = $widgetOrder['size'];
                }
                
                $widget['updated_at'] = now()->toISOString();
                $reorderedWidgets[] = $widget;
            }

            // Update layout with reordered widgets
            $layout['widgets'] = $reorderedWidgets;
            $layout['updated_at'] = now();

            // Save updated layout
            $saved = $this->layoutService->saveUserLayout($layout);

            if ($saved) {
                return response()->json([
                    'success' => true,
                    'message' => 'Widgets reordered successfully',
                    'data' => [
                        'widgets' => $reorderedWidgets,
                        'total_widgets' => count($reorderedWidgets),
                        'reordered_at' => now(),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reorder widgets',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error reordering widgets', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder widgets',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
