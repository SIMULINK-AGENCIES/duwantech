<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MpesaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MpesaController extends Controller
{
    /**
     * Display the M-Pesa settings form
     */
    public function index()
    {
        $settings = MpesaSetting::getInstance();
        
        // Convert to array format expected by view
        $settingsArray = [
            'mpesa_consumer_key' => $settings->getAttribute('consumer_key'),
            'mpesa_consumer_secret' => $settings->getAttribute('consumer_secret'),
            'mpesa_passkey' => $settings->getAttribute('passkey'),
            'mpesa_shortcode' => $settings->getAttribute('shortcode'),
            'mpesa_environment' => $settings->getAttribute('environment'),
            'mpesa_enabled' => $settings->getAttribute('is_enabled'),
            'mpesa_min_amount' => $settings->getAttribute('min_amount'),
            'mpesa_max_amount' => $settings->getAttribute('max_amount'),
            'mpesa_account_reference' => $settings->getAttribute('account_reference'),
            'mpesa_transaction_desc' => $settings->getAttribute('transaction_desc'),
            'mpesa_callback_url' => $settings->getAttribute('callback_url'),
            'mpesa_confirmation_url' => $settings->getAttribute('confirmation_url'),
            'mpesa_validation_url' => $settings->getAttribute('validation_url'),
        ];

        return view('admin.settings.mpesa', [
            'settings' => $settingsArray,
            'mpesaSettings' => $settings,
            'isConfigured' => MpesaSetting::isConfigured()
        ]);
    }

    /**
     * Update M-Pesa settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'mpesa_consumer_key' => 'nullable|string|max:255',
            'mpesa_consumer_secret' => 'nullable|string|max:255',
            'mpesa_passkey' => 'nullable|string|max:255',
            'mpesa_shortcode' => 'nullable|string|max:20',
            'mpesa_environment' => 'required|in:sandbox,live',
            'mpesa_enabled' => 'boolean',
            'mpesa_min_amount' => 'required|numeric|min:1|max:70000',
            'mpesa_max_amount' => 'required|numeric|min:1|max:70000|gte:mpesa_min_amount',
            'mpesa_account_reference' => 'nullable|string|max:100',
            'mpesa_transaction_desc' => 'nullable|string|max:255',
            'mpesa_callback_url' => 'nullable|url',
            'mpesa_confirmation_url' => 'nullable|url',
            'mpesa_validation_url' => 'nullable|url',
        ], [
            'mpesa_max_amount.gte' => 'Maximum amount must be greater than or equal to minimum amount.',
            'mpesa_environment.in' => 'Environment must be either sandbox or live.',
        ]);

        $settings = MpesaSetting::getInstance();

        // Update the settings
        $settings->update([
            'consumer_key' => $request->input('mpesa_consumer_key'),
            'consumer_secret' => $request->input('mpesa_consumer_secret'),
            'passkey' => $request->input('mpesa_passkey'),
            'shortcode' => $request->input('mpesa_shortcode'),
            'environment' => $request->input('mpesa_environment'),
            'is_enabled' => $request->boolean('mpesa_enabled'),
            'min_amount' => $request->input('mpesa_min_amount'),
            'max_amount' => $request->input('mpesa_max_amount'),
            'account_reference' => $request->input('mpesa_account_reference') ?: config('app.name', 'Store'),
            'transaction_desc' => $request->input('mpesa_transaction_desc') ?: 'Payment for order',
            'callback_url' => $request->input('mpesa_callback_url'),
            'confirmation_url' => $request->input('mpesa_confirmation_url'),
            'validation_url' => $request->input('mpesa_validation_url'),
        ]);

        // Clear caches
        MpesaSetting::clearCache();
        Cache::flush();

        return back()->with('success', 'M-Pesa settings updated successfully.');
    }

    /**
     * Test M-Pesa connection
     */
    public function testConnection()
    {
        $settings = MpesaSetting::getInstance();

        if (!MpesaSetting::isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'M-Pesa is not fully configured. Please ensure all required fields are filled.'
            ]);
        }

        try {
            // Here you would implement actual M-Pesa API test
            // For now, we'll just validate the configuration
            
            $missingFields = [];
            
            if (empty($settings->getAttribute('consumer_key'))) {
                $missingFields[] = 'Consumer Key';
            }
            if (empty($settings->getAttribute('consumer_secret'))) {
                $missingFields[] = 'Consumer Secret';
            }
            if (empty($settings->getAttribute('passkey'))) {
                $missingFields[] = 'Passkey';
            }
            if (empty($settings->getAttribute('shortcode'))) {
                $missingFields[] = 'Shortcode';
            }

            if (!empty($missingFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields: ' . implode(', ', $missingFields)
                ]);
            }

            // Simulate API test (replace with actual M-Pesa OAuth test)
            return response()->json([
                'success' => true,
                'message' => 'M-Pesa configuration appears to be valid. Note: This is a basic validation, not a live API test.',
                'data' => [
                    'environment' => $settings->getAttribute('environment'),
                    'shortcode' => $settings->getAttribute('shortcode'),
                    'api_base_url' => $settings->getAttribute('api_base_url'),
                    'callback_urls' => $settings->getAttribute('callback_urls')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset M-Pesa settings to default
     */
    public function reset()
    {
        $settings = MpesaSetting::getInstance();

        $settings->update([
            'consumer_key' => null,
            'consumer_secret' => null,
            'passkey' => null,
            'shortcode' => null,
            'environment' => 'sandbox',
            'is_enabled' => false,
            'min_amount' => 1.00,
            'max_amount' => 70000.00,
            'account_reference' => config('app.name', 'Store'),
            'transaction_desc' => 'Payment for order',
            'callback_url' => null,
            'confirmation_url' => null,
            'validation_url' => null,
        ]);

        // Clear caches
        MpesaSetting::clearCache();
        Cache::flush();

        return back()->with('success', 'M-Pesa settings have been reset to default values.');
    }

    /**
     * Generate callback URLs
     */
    public function generateCallbacks()
    {
        $baseUrl = config('app.url');
        
        return response()->json([
            'success' => true,
            'urls' => [
                'stk_callback' => "{$baseUrl}/api/mpesa/stk-callback",
                'confirmation' => "{$baseUrl}/api/mpesa/c2b-confirmation",
                'validation' => "{$baseUrl}/api/mpesa/c2b-validation",
                'timeout' => "{$baseUrl}/api/mpesa/timeout",
            ]
        ]);
    }

    /**
     * Export M-Pesa configuration (for backup)
     */
    public function export()
    {
        $settings = MpesaSetting::getInstance();
        
        $exportData = [
            'environment' => $settings->getAttribute('environment'),
            'shortcode' => $settings->getAttribute('shortcode'),
            'min_amount' => $settings->getAttribute('min_amount'),
            'max_amount' => $settings->getAttribute('max_amount'),
            'account_reference' => $settings->getAttribute('account_reference'),
            'transaction_desc' => $settings->getAttribute('transaction_desc'),
            'callback_url' => $settings->getAttribute('callback_url'),
            'confirmation_url' => $settings->getAttribute('confirmation_url'),
            'validation_url' => $settings->getAttribute('validation_url'),
            'is_enabled' => $settings->getAttribute('is_enabled'),
            'exported_at' => now()->toDateTimeString(),
            // Note: Sensitive data like keys and secrets are intentionally excluded
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
            'filename' => 'mpesa-config-' . now()->format('Y-m-d-H-i-s') . '.json'
        ]);
    }
}
