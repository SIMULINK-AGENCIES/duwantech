<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MpesaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_key',
        'consumer_secret',
        'passkey',
        'shortcode',
        'environment',
        'is_enabled',
        'callback_url',
        'confirmation_url',
        'validation_url',
        'min_amount',
        'max_amount',
        'account_reference',
        'transaction_desc'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2'
    ];

    /**
     * Get the singleton M-Pesa settings instance
     */
    public static function getInstance(): self
    {
        return Cache::remember('mpesa_settings', 3600, function () {
            $settings = static::first();
            
            if (!$settings) {
                $settings = static::create([
                    'environment' => 'sandbox',
                    'is_enabled' => false,
                    'min_amount' => 1.00,
                    'max_amount' => 70000.00,
                    'account_reference' => config('app.name', 'Store'),
                    'transaction_desc' => 'Payment for order'
                ]);
            }
            
            return $settings;
        });
    }

    /**
     * Get a specific setting value
     */
    public static function getValue(string $key, $default = null)
    {
        $settings = static::getInstance();
        return $settings->getAttribute($key) ?? $default;
    }

    /**
     * Check if M-Pesa is enabled and configured
     */
    public static function isConfigured(): bool
    {
        $settings = static::getInstance();
        
        return $settings->is_enabled &&
               !empty($settings->consumer_key) &&
               !empty($settings->consumer_secret) &&
               !empty($settings->passkey) &&
               !empty($settings->shortcode);
    }

    /**
     * Get the base URL for M-Pesa API
     */
    public function getApiBaseUrlAttribute(): string
    {
        return $this->getAttribute('environment') === 'live'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    /**
     * Get formatted callback URLs
     */
    public function getCallbackUrlsAttribute(): array
    {
        $baseUrl = config('app.url');
        
        return [
            'stk_callback' => $this->getAttribute('callback_url') ?: "{$baseUrl}/api/mpesa/stk-callback",
            'confirmation' => $this->getAttribute('confirmation_url') ?: "{$baseUrl}/api/mpesa/c2b-confirmation",
            'validation' => $this->getAttribute('validation_url') ?: "{$baseUrl}/api/mpesa/c2b-validation"
        ];
    }

    /**
     * Validate amount against limits
     */
    public function isValidAmount(float $amount): bool
    {
        return $amount >= $this->getAttribute('min_amount') && $amount <= $this->getAttribute('max_amount');
    }

    /**
     * Get validation errors for amount
     */
    public function getAmountValidationError(float $amount): ?string
    {
        $minAmount = $this->getAttribute('min_amount');
        $maxAmount = $this->getAttribute('max_amount');
        
        if ($amount < $minAmount) {
            return "Amount must be at least KES " . number_format($minAmount, 2);
        }
        
        if ($amount > $maxAmount) {
            return "Amount cannot exceed KES " . number_format($maxAmount, 2);
        }
        
        return null;
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('mpesa_settings');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Get masked consumer secret (for display purposes)
     */
    public function getMaskedConsumerSecretAttribute(): string
    {
        $secret = $this->getAttribute('consumer_secret');
        if (empty($secret)) {
            return '';
        }
        
        return str_repeat('*', strlen($secret) - 4) . substr($secret, -4);
    }

    /**
     * Get masked passkey (for display purposes)
     */
    public function getMaskedPasskeyAttribute(): string
    {
        $passkey = $this->getAttribute('passkey');
        if (empty($passkey)) {
            return '';
        }
        
        return str_repeat('*', strlen($passkey) - 4) . substr($passkey, -4);
    }
}
