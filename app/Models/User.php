<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'phone_verified_at',
        'bio',
        'avatar',
        'timezone',
        'language',
        'last_login_at',
        'login_count',
        'notification_email',
        'notification_browser',
        'notification_orders',
        'notification_products',
        'notification_users',
        'two_factor_enabled',
        'two_factor_method',
        'api_access_enabled',
        'api_token',
        'api_rate_limit',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'notification_email' => 'boolean',
            'notification_browser' => 'boolean',
            'notification_orders' => 'boolean',
            'notification_products' => 'boolean',
            'notification_users' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'api_access_enabled' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's active sessions.
     */
    public function activeSessions(): HasMany
    {
        return $this->hasMany(ActiveSession::class);
    }

    /**
     * Get the user's current active sessions.
     */
    public function currentActiveSessions(): HasMany
    {
        return $this->activeSessions()->active();
    }

    /**
     * Get the user's notification preferences.
     */
    public function notificationPreferences()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    /**
     * Get the user's activity logs.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get recent activity logs for the user.
     */
    public function recentActivityLogs(): HasMany
    {
        return $this->activityLogs()->recent()->orderBy('created_at', 'desc')->limit(10);
    }

    /**
     * Check if user is currently online.
     */
    public function isOnline(): bool
    {
        return $this->activeSessions()->active()->exists();
    }

    /**
     * Get user's notification preferences or create default ones.
     */
    public function getNotificationPreferences(): NotificationPreference
    {
        return $this->notificationPreferences()->firstOrCreate(
            ['user_id' => $this->id],
            NotificationPreference::getDefaults()
        );
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
}
