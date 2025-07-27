<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\LayoutService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Exception;

class CustomizationController extends Controller
{
    protected LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
        // Note: Middleware and authorization should be handled via routes or policies
    }

    /**
     * Get user's current customization settings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $layout = $this->layoutService->getUserLayout();
            
            // Get user preferences from database or cache
            $preferences = $this->getUserPreferences($user->id);
            
            // Get current theme settings
            $currentTheme = $layout['theme'] ?? config('dashboard.themes.default');
            $themeConfig = $this->getThemeConfig($currentTheme);

            return response()->json([
                'success' => true,
                'message' => 'Customization settings retrieved successfully',
                'data' => [
                    'theme' => [
                        'current' => $currentTheme,
                        'config' => $themeConfig,
                    ],
                    'preferences' => $preferences,
                    'layout' => [
                        'template' => $layout['template'] ?? config('dashboard.layout.default_template'),
                        'auto_refresh' => $layout['auto_refresh'] ?? true,
                        'refresh_interval' => $layout['refresh_interval'] ?? config('dashboard.widgets.auto_refresh_interval'),
                    ],
                    'customization_options' => [
                        'allow_theme_customization' => config('dashboard.customization.allow_theme_customization'),
                        'allow_widget_creation' => config('dashboard.customization.allow_widget_creation'),
                        'allow_layout_sharing' => config('dashboard.customization.allow_layout_sharing'),
                    ],
                    'user_id' => $user->id,
                    'last_updated' => $layout['updated_at'] ?? now(),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving customization settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customization settings',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available themes
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function themes(Request $request): JsonResponse
    {
        try {
            $availableThemes = config('dashboard.themes.available', []);
            $defaultTheme = config('dashboard.themes.default');
            $allowCustomThemes = config('dashboard.customization.allow_theme_customization');

            // Get user's current theme
            $user = Auth::user();
            $layout = $this->layoutService->getUserLayout();
            $currentTheme = $layout['theme'] ?? $defaultTheme;

            // Enrich themes with additional metadata
            $enrichedThemes = [];
            foreach ($availableThemes as $themeId => $theme) {
                $enrichedThemes[$themeId] = array_merge($theme, [
                    'id' => $themeId,
                    'is_current' => $themeId === $currentTheme,
                    'is_default' => $themeId === $defaultTheme,
                    'preview_available' => true, // Could be dynamic based on theme assets
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Available themes retrieved successfully',
                'data' => [
                    'themes' => $enrichedThemes,
                    'current_theme' => $currentTheme,
                    'default_theme' => $defaultTheme,
                    'total_themes' => count($enrichedThemes),
                    'customization_allowed' => $allowCustomThemes,
                    'theme_categories' => $this->getThemeCategories($enrichedThemes),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving available themes', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available themes',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Save/apply a theme for the user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveTheme(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'theme' => 'required|string|in:' . implode(',', array_keys(config('dashboard.themes.available'))),
                'custom_colors' => 'sometimes|array',
                'custom_colors.primary' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'custom_colors.secondary' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'custom_colors.success' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'custom_colors.warning' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'custom_colors.danger' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'custom_colors.info' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'custom_colors.background' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'custom_colors.surface' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'custom_colors.text' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'apply_to_all_layouts' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $themeId = $data['theme'];

            // Check if theme customization is allowed
            if (!config('dashboard.customization.allow_theme_customization')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Theme customization is not allowed',
                    'error' => 'Theme customization has been disabled by administrator'
                ], 403);
            }

            // Get theme configuration
            $themeConfig = $this->getThemeConfig($themeId);
            if (!$themeConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'Theme not found',
                    'error' => "Theme '{$themeId}' does not exist"
                ], 404);
            }

            // Get current layout
            $layout = $this->layoutService->getUserLayout();
            
            // Update theme in layout
            $layout['theme'] = $themeId;
            
            // Apply custom colors if provided
            if (isset($data['custom_colors'])) {
                $layout['custom_theme_colors'] = array_merge(
                    $themeConfig['colors'] ?? [],
                    $data['custom_colors']
                );
            }
            
            $layout['updated_at'] = now();

            // Save updated layout
            $saved = $this->layoutService->saveUserLayout($layout);

            if ($saved) {
                // Cache theme preference for faster access
                $this->cacheUserTheme(Auth::id(), $themeId, $data['custom_colors'] ?? []);
                
                Log::info('User theme updated', [
                    'user_id' => Auth::id(),
                    'theme' => $themeId,
                    'custom_colors' => isset($data['custom_colors']),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Theme applied successfully',
                    'data' => [
                        'theme' => $themeId,
                        'theme_config' => $themeConfig,
                        'custom_colors' => $data['custom_colors'] ?? null,
                        'applied_at' => now(),
                        'css_variables' => $this->generateCssVariables($themeId, $data['custom_colors'] ?? []),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to apply theme',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Error saving theme', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save theme',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user preferences
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function preferences(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $preferences = $this->getUserPreferences($user->id);
            
            // Get layout preferences
            $layout = $this->layoutService->getUserLayout();
            $layoutPreferences = [
                'auto_refresh' => $layout['auto_refresh'] ?? true,
                'refresh_interval' => $layout['refresh_interval'] ?? config('dashboard.widgets.auto_refresh_interval'),
                'theme' => $layout['theme'] ?? config('dashboard.themes.default'),
                'template' => $layout['template'] ?? config('dashboard.layout.default_template'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'User preferences retrieved successfully',
                'data' => [
                    'general_preferences' => $preferences,
                    'layout_preferences' => $layoutPreferences,
                    'available_options' => [
                        'refresh_intervals' => [5000, 10000, 30000, 60000, 300000], // 5s to 5min
                        'themes' => array_keys(config('dashboard.themes.available')),
                        'templates' => array_keys(config('dashboard.layout.available_templates')),
                    ],
                    'user_id' => $user->id,
                    'last_updated' => max(
                        $preferences['updated_at'] ?? now()->subDay(),
                        $layout['updated_at'] ?? now()->subDay()
                    ),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving user preferences', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user preferences',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Save user preferences
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function savePreferences(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'auto_refresh' => 'sometimes|boolean',
                'refresh_interval' => 'sometimes|integer|min:5000|max:300000', // 5s to 5min
                'notifications_enabled' => 'sometimes|boolean',
                'sound_enabled' => 'sometimes|boolean',
                'compact_mode' => 'sometimes|boolean',
                'sidebar_collapsed' => 'sometimes|boolean',
                'show_breadcrumbs' => 'sometimes|boolean',
                'timezone' => 'sometimes|string|max:50',
                'date_format' => 'sometimes|string|in:Y-m-d,d/m/Y,m/d/Y,d-m-Y',
                'time_format' => 'sometimes|string|in:H:i,h:i A',
                'language' => 'sometimes|string|size:2',
                'items_per_page' => 'sometimes|integer|min:10|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $preferences = $validator->validated();
            $user = Auth::user();

            // Separate layout preferences from general preferences
            $layoutPreferences = [];
            $generalPreferences = [];

            foreach ($preferences as $key => $value) {
                if (in_array($key, ['auto_refresh', 'refresh_interval'])) {
                    $layoutPreferences[$key] = $value;
                } else {
                    $generalPreferences[$key] = $value;
                }
            }

            // Update layout preferences if any
            if (!empty($layoutPreferences)) {
                $layout = $this->layoutService->getUserLayout();
                $layout = array_merge($layout, $layoutPreferences);
                $layout['updated_at'] = now();
                $this->layoutService->saveUserLayout($layout);
            }

            // Update general preferences
            if (!empty($generalPreferences)) {
                $this->saveUserPreferences($user->id, $generalPreferences);
            }

            Log::info('User preferences updated', [
                'user_id' => $user->id,
                'layout_preferences' => $layoutPreferences,
                'general_preferences' => array_keys($generalPreferences),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Preferences saved successfully',
                'data' => [
                    'updated_preferences' => $preferences,
                    'layout_preferences' => $layoutPreferences,
                    'general_preferences' => $generalPreferences,
                    'total_updated' => count($preferences),
                    'saved_at' => now(),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error saving user preferences', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save preferences',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user preferences from cache or database
     *
     * @param int $userId
     * @return array
     */
    protected function getUserPreferences(int $userId): array
    {
        return Cache::remember("user_preferences_{$userId}", 3600, function () use ($userId) {
            $user = User::find($userId);
            
            return [
                'notifications_enabled' => $user->dashboard_preferences['notifications_enabled'] ?? true,
                'sound_enabled' => $user->dashboard_preferences['sound_enabled'] ?? false,
                'compact_mode' => $user->dashboard_preferences['compact_mode'] ?? false,
                'sidebar_collapsed' => $user->dashboard_preferences['sidebar_collapsed'] ?? false,
                'show_breadcrumbs' => $user->dashboard_preferences['show_breadcrumbs'] ?? true,
                'timezone' => $user->dashboard_preferences['timezone'] ?? config('app.timezone'),
                'date_format' => $user->dashboard_preferences['date_format'] ?? 'Y-m-d',
                'time_format' => $user->dashboard_preferences['time_format'] ?? 'H:i',
                'language' => $user->dashboard_preferences['language'] ?? 'en',
                'items_per_page' => $user->dashboard_preferences['items_per_page'] ?? 25,
                'updated_at' => $user->dashboard_preferences['updated_at'] ?? now()->subDay(),
            ];
        });
    }

    /**
     * Save user preferences to database and cache
     *
     * @param int $userId
     * @param array $preferences
     * @return bool
     */
    protected function saveUserPreferences(int $userId, array $preferences): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $currentPreferences = $user->dashboard_preferences ?? [];
        $updatedPreferences = array_merge($currentPreferences, $preferences);
        $updatedPreferences['updated_at'] = now();

        $user->dashboard_preferences = $updatedPreferences;
        $saved = $user->save();

        if ($saved) {
            // Clear cache to force refresh
            Cache::forget("user_preferences_{$userId}");
        }

        return $saved;
    }

    /**
     * Cache user theme for faster access
     *
     * @param int $userId
     * @param string $themeId
     * @param array $customColors
     * @return void
     */
    protected function cacheUserTheme(int $userId, string $themeId, array $customColors = []): void
    {
        $cacheKey = "user_theme_{$userId}";
        $themeData = [
            'theme' => $themeId,
            'custom_colors' => $customColors,
            'cached_at' => now(),
        ];

        Cache::put($cacheKey, $themeData, 3600); // Cache for 1 hour
    }

    /**
     * Generate CSS variables for theme
     *
     * @param string $themeId
     * @param array $customColors
     * @return array
     */
    protected function generateCssVariables(string $themeId, array $customColors = []): array
    {
        $themeConfig = $this->getThemeConfig($themeId);
        $colors = array_merge($themeConfig['colors'] ?? [], $customColors);

        $cssVariables = [];
        foreach ($colors as $colorKey => $colorValue) {
            $cssVariables["--dashboard-{$colorKey}"] = $colorValue;
        }

        return $cssVariables;
    }

    /**
     * Get theme categories for organization
     *
     * @param array $themes
     * @return array
     */
    protected function getThemeCategories(array $themes): array
    {
        $categories = [];
        foreach ($themes as $theme) {
            $category = $theme['category'] ?? 'general';
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }
        return $categories;
    }

    /**
     * Get theme configuration safely
     *
     * @param string $themeId
     * @return array|null
     */
    protected function getThemeConfig(string $themeId): ?array
    {
        $availableThemes = config('dashboard.themes.available', []);
        return $availableThemes[$themeId] ?? null;
    }
}
