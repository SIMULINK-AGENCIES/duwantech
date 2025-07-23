<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => config('app.name'),
            'site_description' => config('app.description', ''),
            'contact_email' => config('mail.from.address'),
            'contact_phone' => config('app.contact_phone', ''),
            'mpesa_consumer_key' => config('services.mpesa.consumer_key', ''),
            'mpesa_consumer_secret' => config('services.mpesa.consumer_secret', ''),
            'mpesa_passkey' => config('services.mpesa.passkey', ''),
            'mpesa_shortcode' => config('services.mpesa.shortcode', ''),
            'tawk_to_widget_id' => config('services.tawkto.widget_id', ''),
            'google_analytics_id' => config('services.google.analytics_id', ''),
            'social_facebook' => config('app.social_facebook', ''),
            'social_instagram' => config('app.social_instagram', ''),
            'social_twitter' => config('app.social_twitter', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'mpesa_consumer_key' => 'nullable|string',
            'mpesa_consumer_secret' => 'nullable|string',
            'mpesa_passkey' => 'nullable|string',
            'mpesa_shortcode' => 'nullable|string',
            'tawk_to_widget_id' => 'nullable|string',
            'google_analytics_id' => 'nullable|string',
            'social_facebook' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
        ]);

        // Update .env file or database settings
        $this->updateEnvironmentFile([
            'APP_NAME' => $request->site_name,
            'APP_DESCRIPTION' => $request->site_description,
            'MAIL_FROM_ADDRESS' => $request->contact_email,
            'APP_CONTACT_PHONE' => $request->contact_phone,
            'MPESA_CONSUMER_KEY' => $request->mpesa_consumer_key,
            'MPESA_CONSUMER_SECRET' => $request->mpesa_consumer_secret,
            'MPESA_PASSKEY' => $request->mpesa_passkey,
            'MPESA_SHORTCODE' => $request->mpesa_shortcode,
            'TAWKTO_WIDGET_ID' => $request->tawk_to_widget_id,
            'GOOGLE_ANALYTICS_ID' => $request->google_analytics_id,
            'SOCIAL_FACEBOOK' => $request->social_facebook,
            'SOCIAL_INSTAGRAM' => $request->social_instagram,
            'SOCIAL_TWITTER' => $request->social_twitter,
        ]);

        // Clear cache
        Cache::flush();

        return back()->with('success', 'Settings updated successfully.');
    }

    private function updateEnvironmentFile($data)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $content = file_get_contents($path);

            foreach ($data as $key => $value) {
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}=" . (is_bool($value) ? ($value ? 'true' : 'false') : $value),
                    $content
                );
            }

            file_put_contents($path, $content);
        }
    }
} 