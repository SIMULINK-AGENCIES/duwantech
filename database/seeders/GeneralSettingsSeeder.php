<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
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
                'value' => 'Your online shopping destination for quality products',
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
                'value' => 'ecommerce, online store, shopping, retail, products',
                'type' => 'string',
                'category' => 'site',
                'label' => 'Site Keywords',
                'description' => 'SEO keywords for your site (comma separated)',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'key' => 'site_author',
                'value' => config('app.name', 'E-Commerce Store'),
                'type' => 'string',
                'category' => 'site',
                'label' => 'Site Author',
                'description' => 'The author/owner of the website',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4
            ],
            
            // Appearance
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
                'value' => '/favicon.ico',
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
            [
                'key' => 'theme_mode',
                'value' => 'light',
                'type' => 'string',
                'category' => 'appearance',
                'label' => 'Default Theme Mode',
                'description' => 'Default theme mode (light/dark/auto)',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 5
            ],
            
            // Contact Information
            [
                'key' => 'contact_email',
                'value' => config('mail.from.address', 'info@example.com'),
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
                'value' => '+254700000000',
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
                'value' => 'Nairobi, Kenya',
                'type' => 'string',
                'category' => 'contact',
                'label' => 'Contact Address',
                'description' => 'Physical business address',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'key' => 'business_hours',
                'value' => 'Monday - Friday: 9:00 AM - 6:00 PM',
                'type' => 'string',
                'category' => 'contact',
                'label' => 'Business Hours',
                'description' => 'Your business operating hours',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4
            ],
            
            // Footer
            [
                'key' => 'footer_text',
                'value' => '© ' . date('Y') . ' ' . config('app.name', 'E-Commerce Store') . '. All rights reserved.',
                'type' => 'string',
                'category' => 'footer',
                'label' => 'Footer Copyright Text',
                'description' => 'Copyright text displayed in footer',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'footer_description',
                'value' => 'Your trusted online shopping destination for quality products at competitive prices.',
                'type' => 'string',
                'category' => 'footer',
                'label' => 'Footer Description',
                'description' => 'Brief description in footer',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2
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
            [
                'key' => 'social_youtube',
                'value' => '',
                'type' => 'url',
                'category' => 'social',
                'label' => 'YouTube URL',
                'description' => 'Your YouTube channel URL',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 5
            ],
            [
                'key' => 'social_tiktok',
                'value' => '',
                'type' => 'url',
                'category' => 'social',
                'label' => 'TikTok URL',
                'description' => 'Your TikTok profile URL',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 6
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
                'description' => 'Allow new users to register on the site',
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
            [
                'key' => 'enable_reviews',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'features',
                'label' => 'Enable Product Reviews',
                'description' => 'Allow customers to leave product reviews',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'key' => 'enable_wishlist',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'features',
                'label' => 'Enable Wishlist',
                'description' => 'Allow customers to create wishlists',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 5
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
            [
                'key' => 'hotjar_site_id',
                'value' => '',
                'type' => 'string',
                'category' => 'analytics',
                'label' => 'Hotjar Site ID',
                'description' => 'Your Hotjar site ID for heatmaps and recordings',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4
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
            [
                'key' => 'intercom_app_id',
                'value' => '',
                'type' => 'string',
                'category' => 'support',
                'label' => 'Intercom App ID',
                'description' => 'Your Intercom application ID',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'crisp_website_id',
                'value' => '',
                'type' => 'string',
                'category' => 'support',
                'label' => 'Crisp Website ID',
                'description' => 'Your Crisp chat widget website ID',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 3
            ],
            
            // E-commerce Settings
            [
                'key' => 'currency',
                'value' => 'KES',
                'type' => 'string',
                'category' => 'ecommerce',
                'label' => 'Default Currency',
                'description' => 'Default currency for the store',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 1
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'KSh',
                'type' => 'string',
                'category' => 'ecommerce',
                'label' => 'Currency Symbol',
                'description' => 'Symbol for the default currency',
                'is_public' => true,
                'is_required' => true,
                'sort_order' => 2
            ],
            [
                'key' => 'tax_rate',
                'value' => '16',
                'type' => 'float',
                'category' => 'ecommerce',
                'label' => 'Tax Rate (%)',
                'description' => 'Default tax rate percentage',
                'is_public' => false,
                'is_required' => false,
                'sort_order' => 3
            ],
            [
                'key' => 'shipping_cost',
                'value' => '200',
                'type' => 'float',
                'category' => 'ecommerce',
                'label' => 'Default Shipping Cost',
                'description' => 'Default shipping cost for orders',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 4
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '2000',
                'type' => 'float',
                'category' => 'ecommerce',
                'label' => 'Free Shipping Threshold',
                'description' => 'Minimum order amount for free shipping',
                'is_public' => true,
                'is_required' => false,
                'sort_order' => 5
            ],
        ];

        foreach ($settings as $setting) {
            GeneralSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
