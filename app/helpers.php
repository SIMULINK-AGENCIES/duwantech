<?php

if (!function_exists('settings')) {
    /**
     * Get a general setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function settings(string $key, $default = null)
    {
        return \App\Models\GeneralSetting::get($key, $default);
    }
}

if (!function_exists('mpesa_settings')) {
    /**
     * Get M-Pesa settings instance
     *
     * @return \App\Models\MpesaSetting
     */
    function mpesa_settings(): \App\Models\MpesaSetting
    {
        return \App\Models\MpesaSetting::getInstance();
    }
}

if (!function_exists('mpesa_setting')) {
    /**
     * Get a specific M-Pesa setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function mpesa_setting(string $key, $default = null)
    {
        return \App\Models\MpesaSetting::getValue($key, $default);
    }
}

if (!function_exists('public_settings')) {
    /**
     * Get all public settings for frontend
     *
     * @return array
     */
    function public_settings(): array
    {
        return \App\Models\GeneralSetting::getPublicSettings();
    }
}

if (!function_exists('is_mpesa_enabled')) {
    /**
     * Check if M-Pesa is enabled and configured
     *
     * @return bool
     */
    function is_mpesa_enabled(): bool
    {
        return \App\Models\MpesaSetting::isConfigured();
    }
}
