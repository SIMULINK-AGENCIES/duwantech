<?php

namespace App\Providers;

use App\Models\GeneralSetting;
use App\Models\MpesaSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share public settings with all views
        View::composer('*', function ($view) {
            $publicSettings = Cache::remember('view_public_settings', 3600, function () {
                try {
                    return GeneralSetting::getPublicSettings();
                } catch (\Exception $e) {
                    // Return empty array if table doesn't exist yet (during migration)
                    return [];
                }
            });

            $view->with('publicSettings', $publicSettings);
        });

        // Share M-Pesa status with views that need it
        View::composer(['layouts.app', 'checkout.*', 'cart.*'], function ($view) {
            $mpesaEnabled = Cache::remember('mpesa_enabled_status', 1800, function () {
                try {
                    return MpesaSetting::isConfigured();
                } catch (\Exception $e) {
                    return false;
                }
            });

            $view->with('mpesaEnabled', $mpesaEnabled);
        });
    }
}
