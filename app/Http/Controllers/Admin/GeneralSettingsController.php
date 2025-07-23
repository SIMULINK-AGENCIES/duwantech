<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class GeneralSettingsController extends Controller
{
    /**
     * Display the general settings form
     */
    public function index()
    {
        // Get all settings grouped by category
        $settingsGroups = GeneralSetting::with([])
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        // Convert to format expected by view
        $settings = [];
        foreach ($settingsGroups as $category => $categorySettings) {
            foreach ($categorySettings as $setting) {
                $settings[$setting->key] = $setting->typed_value;
            }
        }

        // If no settings exist, create default ones
        if (empty($settings)) {
            $this->createDefaultSettings();
            $settings = GeneralSetting::pluck('value', 'key')->toArray();
        }

        return view('admin.settings.general', compact('settings', 'settingsGroups'));
    }

    /**
     * Update the general settings
     */
    public function update(Request $request)
    {
        // Get all current settings for validation rules
        $currentSettings = GeneralSetting::all()->keyBy('key');
        
        // Build validation rules dynamically
        $rules = [];
        $messages = [];
        
        foreach ($currentSettings as $key => $setting) {
            $fieldRules = [];
            
            if ($setting->getAttribute('is_required')) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            // Add type-specific rules
            switch ($setting->getAttribute('type')) {
                case 'string':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:500';
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'integer':
                    $fieldRules[] = 'integer';
                    break;
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
                case 'color':
                    $fieldRules[] = 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';
                    $messages[$key . '.regex'] = 'The ' . $setting->getAttribute('label') . ' must be a valid hex color.';
                    break;
            }
            
            $rules[$key] = implode('|', $fieldRules);
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update each setting
        foreach ($currentSettings as $key => $setting) {
            if ($request->has($key)) {
                $value = $request->input($key);
                
                // Handle special cases
                if ($setting->getAttribute('type') === 'boolean') {
                    $value = $request->has($key) ? 1 : 0;
                }
                
                $setting->value = $value;
                $setting->save();
            }
        }

        // Clear all caches
        GeneralSetting::clearCache();
        Cache::flush();

        return back()->with('success', 'General settings updated successfully.');
    }

    /**
     * Create default settings
     */
    private function createDefaultSettings()
    {
        $defaultSettings = [
            // Site Information
            [
                'key' => 'site_name',
                'value' => config('app.name', 'E-Commerce Store'),
                'type' => 'string',
                'category' => 'site',
                'label' => 'Site Name',
                'description' => 'The name of your website',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'key' => 'site_description',
                'value' => 'Your online shopping destination',
                'type' => 'string',
                'category' => 'site',
                'label' => 'Site Description',
                'description' => 'A brief description of your website',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'site_keywords',
                'value' => 'ecommerce, online store, shopping',
                'type' => 'string',
                'category' => 'site',
                'label' => 'Site Keywords',
                'description' => 'SEO keywords for your site (comma separated)',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'key' => 'logo_url',
                'value' => '',
                'type' => 'url',
                'category' => 'appearance',
                'label' => 'Logo URL',
                'description' => 'URL to your site logo',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'favicon_url',
                'value' => '',
                'type' => 'url',
                'category' => 'appearance',
                'label' => 'Favicon URL',
                'description' => 'URL to your site favicon',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'primary_color',
                'value' => '#3B82F6',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Primary Color',
                'description' => 'Main brand color for your site',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'key' => 'secondary_color',
                'value' => '#64748B',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Secondary Color',
                'description' => 'Secondary brand color for your site',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4
            ],
            
            // Contact Information
            [
                'key' => 'contact_email',
                'value' => config('mail.from.address', ''),
                'type' => 'email',
                'category' => 'contact',
                'label' => 'Contact Email',
                'description' => 'Main contact email address',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'contact_phone',
                'value' => '',
                'type' => 'string',
                'category' => 'contact',
                'label' => 'Contact Phone',
                'description' => 'Main contact phone number',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'contact_address',
                'value' => '',
                'type' => 'string',
                'category' => 'contact',
                'label' => 'Contact Address',
                'description' => 'Physical business address',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            
            // Footer
            [
                'key' => 'footer_text',
                'value' => '© ' . date('Y') . ' ' . config('app.name', 'E-Commerce Store') . '. All rights reserved.',
                'type' => 'string',
                'category' => 'footer',
                'label' => 'Footer Text',
                'description' => 'Copyright text in footer',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1
            ],
            
            // Social Media
            [
                'key' => 'social_facebook',
                'value' => '',
                'type' => 'url',
                'category' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Your Facebook page URL',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'social_twitter',
                'value' => '',
                'type' => 'url',
                'category' => 'social',
                'label' => 'Twitter URL',
                'description' => 'Your Twitter profile URL',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'social_instagram',
                'value' => '',
                'type' => 'url',
                'category' => 'social',
                'label' => 'Instagram URL',
                'description' => 'Your Instagram profile URL',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'key' => 'social_linkedin',
                'value' => '',
                'type' => 'url',
                'category' => 'social',
                'label' => 'LinkedIn URL',
                'description' => 'Your LinkedIn profile URL',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4
            ],
            
            // Features
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'features',
                'label' => 'Maintenance Mode',
                'description' => 'Enable maintenance mode to show a maintenance page',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'allow_registration',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'features',
                'label' => 'Allow Registration',
                'description' => 'Allow new users to register',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'require_email_verification',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'features',
                'label' => 'Require Email Verification',
                'description' => 'Require users to verify their email before accessing the site',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 3
            ],
            
            // Analytics & Tracking
            [
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'string',
                'category' => 'analytics',
                'label' => 'Google Analytics ID',
                'description' => 'Your Google Analytics tracking ID (e.g., GA-XXXXXXXXX-X)',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'google_tag_manager_id',
                'value' => '',
                'type' => 'string',
                'category' => 'analytics',
                'label' => 'Google Tag Manager ID',
                'description' => 'Your Google Tag Manager container ID (e.g., GTM-XXXXXXX)',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'facebook_pixel_id',
                'value' => '',
                'type' => 'string',
                'category' => 'analytics',
                'label' => 'Facebook Pixel ID',
                'description' => 'Your Facebook Pixel ID for tracking',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            
            // Chat & Support
            [
                'key' => 'tawk_to_widget_id',
                'value' => '',
                'type' => 'string',
                'category' => 'support',
                'label' => 'Tawk.to Widget ID',
                'description' => 'Your Tawk.to chat widget ID',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1
            ],
        ];

        foreach ($defaultSettings as $setting) {
            GeneralSetting::create($setting);
        }
    }

    /**
     * Get public settings for frontend (API endpoint)
     */
    public function getPublicSettings()
    {
        return response()->json([
            'success' => true,
            'data' => GeneralSetting::getPublicSettings()
        ]);
    }
}
