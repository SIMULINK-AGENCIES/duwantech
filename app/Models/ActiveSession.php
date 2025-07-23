<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ActiveSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'location',
        'page_url',
        'last_activity',
    ];

    protected $casts = [
        'location' => 'array',
        'last_activity' => 'datetime',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only active sessions (within last 15 minutes).
     */
    public function scopeActive($query)
    {
        return $query->where('last_activity', '>=', Carbon::now()->subMinutes(15));
    }

    /**
     * Scope to get sessions from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope to get sessions by country.
     */
    public function scopeByCountry($query, $country)
    {
        return $query->whereJsonContains('location->country', $country);
    }

    /**
     * Scope to get authenticated user sessions only.
     */
    public function scopeAuthenticated($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope to get guest sessions only.
     */
    public function scopeGuest($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Get formatted location string.
     */
    public function getLocationStringAttribute(): string
    {
        if (!$this->location) {
            return 'Unknown';
        }

        $parts = [];
        if (isset($this->location['city'])) {
            $parts[] = $this->location['city'];
        }
        if (isset($this->location['country'])) {
            $parts[] = $this->location['country'];
        }

        return implode(', ', $parts) ?: 'Unknown';
    }

    /**
     * Check if session is currently active.
     */
    public function isActive(): bool
    {
        return $this->last_activity >= Carbon::now()->subMinutes(15);
    }

    /**
     * Get time since last activity in human readable format.
     */
    public function getTimeSinceLastActivityAttribute(): string
    {
        return Carbon::parse($this->last_activity)->diffForHumans();
    }
}
