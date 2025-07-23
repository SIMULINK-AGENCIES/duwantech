<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_notifications',
        'browser_notifications',
        'sms_notifications',
        'order_notifications',
        'payment_notifications',
        'inventory_notifications',
        'marketing_notifications',
        'email_frequency',
        'quiet_hours_start',
        'quiet_hours_end',
        'timezone',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'browser_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'order_notifications' => 'boolean',
        'payment_notifications' => 'boolean',
        'inventory_notifications' => 'boolean',
        'marketing_notifications' => 'boolean',
        'quiet_hours_start' => 'datetime:H:i',
        'quiet_hours_end' => 'datetime:H:i',
    ];

    /**
     * Email frequency options.
     */
    const FREQUENCY_IMMEDIATE = 'immediate';
    const FREQUENCY_HOURLY = 'hourly';
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_NEVER = 'never';

    /**
     * Get the user that owns the notification preferences.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get users who want email notifications.
     */
    public function scopeEmailEnabled($query)
    {
        return $query->where('email_notifications', true);
    }

    /**
     * Scope to get users who want browser notifications.
     */
    public function scopeBrowserEnabled($query)
    {
        return $query->where('browser_notifications', true);
    }

    /**
     * Scope to get users who want SMS notifications.
     */
    public function scopeSmsEnabled($query)
    {
        return $query->where('sms_notifications', true);
    }

    /**
     * Scope to filter by email frequency.
     */
    public function scopeByEmailFrequency($query, $frequency)
    {
        return $query->where('email_frequency', $frequency);
    }

    /**
     * Check if user wants order notifications.
     */
    public function wantsOrderNotifications(): bool
    {
        return $this->order_notifications;
    }

    /**
     * Check if user wants payment notifications.
     */
    public function wantsPaymentNotifications(): bool
    {
        return $this->payment_notifications;
    }

    /**
     * Check if user wants inventory notifications.
     */
    public function wantsInventoryNotifications(): bool
    {
        return $this->inventory_notifications;
    }

    /**
     * Check if user wants marketing notifications.
     */
    public function wantsMarketingNotifications(): bool
    {
        return $this->marketing_notifications;
    }

    /**
     * Check if current time is within quiet hours.
     */
    public function isQuietTime(): bool
    {
        if (!$this->quiet_hours_start || !$this->quiet_hours_end) {
            return false;
        }

        $now = now($this->timezone ?? config('app.timezone'));
        $start = $this->quiet_hours_start;
        $end = $this->quiet_hours_end;

        // Handle cases where quiet hours span midnight
        if ($start > $end) {
            return $now >= $start || $now <= $end;
        }

        return $now >= $start && $now <= $end;
    }

    /**
     * Get all available email frequencies.
     */
    public static function getEmailFrequencies(): array
    {
        return [
            self::FREQUENCY_IMMEDIATE,
            self::FREQUENCY_HOURLY,
            self::FREQUENCY_DAILY,
            self::FREQUENCY_WEEKLY,
            self::FREQUENCY_NEVER,
        ];
    }

    /**
     * Get default notification preferences.
     */
    public static function getDefaults(): array
    {
        return [
            'email_notifications' => true,
            'browser_notifications' => true,
            'sms_notifications' => false,
            'order_notifications' => true,
            'payment_notifications' => true,
            'inventory_notifications' => false,
            'marketing_notifications' => true,
            'email_frequency' => self::FREQUENCY_IMMEDIATE,
            'timezone' => config('app.timezone'),
        ];
    }
}
