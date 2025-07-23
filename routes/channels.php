<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Admin Monitoring Channels
|--------------------------------------------------------------------------
|
| These channels are used for real-time admin monitoring functionality.
| Only authenticated admin users can access these channels.
|
*/

// Main admin monitoring channel for general real-time updates
Broadcast::channel('admin-monitoring', function ($user) {
    return $user && $user->hasRole('admin');
});

// Admin orders channel for order-specific updates
Broadcast::channel('admin-orders', function ($user) {
    return $user && $user->hasRole('admin');
});

// Admin payments channel for payment-specific updates
Broadcast::channel('admin-payments', function ($user) {
    return $user && $user->hasRole('admin');
});

// Admin inventory channel for stock and product updates
Broadcast::channel('admin-inventory', function ($user) {
    return $user && $user->hasRole('admin');
});

// Admin system channel for system alerts and maintenance
Broadcast::channel('admin-system', function ($user) {
    return $user && $user->hasRole('admin');
});

// Admin notifications channel for general admin notifications
Broadcast::channel('admin-notifications', function ($user) {
    return $user && $user->hasRole('admin');
});

/*
|--------------------------------------------------------------------------
| User-Specific Channels
|--------------------------------------------------------------------------
|
| These channels are for user-specific real-time updates.
|
*/

// User-specific notification channel
Broadcast::channel('user-notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// User-specific order updates channel
Broadcast::channel('user-orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

/*
|--------------------------------------------------------------------------
| Presence Channels
|--------------------------------------------------------------------------
|
| These channels are for tracking user presence and online status.
|
*/

// Admin presence channel to track which admins are online
Broadcast::channel('admin-presence', function ($user) {
    if ($user && $user->hasRole('admin')) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?? null,
            'role' => 'admin',
            'last_seen' => now()->toISOString(),
        ];
    }
    return false;
});

// General user presence channel for monitoring online users
Broadcast::channel('user-presence', function ($user) {
    if ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?? null,
            'online_since' => now()->toISOString(),
        ];
    }
    return false;
});
