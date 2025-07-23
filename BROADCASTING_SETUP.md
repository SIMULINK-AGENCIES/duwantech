# Real-Time Broadcasting Setup Guide

## Overview
This document explains how to configure and use the real-time broadcasting system for the DuwanTech e-commerce admin monitoring.

## Broadcasting Events Created

### 1. User Activity Events
- **`UserOnlineEvent`** - Triggered when a user comes online
- **`UserOfflineEvent`** - Triggered when a user goes offline

### 2. E-commerce Events
- **`NewOrderEvent`** - Triggered when a new order is created
- **`PaymentProcessedEvent`** - Triggered when a payment is processed (success/failure)

### 3. Inventory Events
- **`StockAlertEvent`** - Triggered for low stock, out of stock, or restock alerts

### 4. System Events
- **`SystemAlertEvent`** - Triggered for system errors, security alerts, maintenance, etc.

## Broadcasting Channels

### Admin Channels (Private)
- `admin-monitoring` - General admin monitoring updates
- `admin-orders` - Order-specific updates
- `admin-payments` - Payment-specific updates
- `admin-inventory` - Stock and inventory updates
- `admin-system` - System alerts and maintenance
- `admin-notifications` - General admin notifications

### Presence Channels
- `admin-presence` - Track which admins are online
- `user-presence` - Track online users

## Configuration

### Environment Variables
The system is configured to use Pusher by default. Add these to your `.env` file:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### Local Development
For local development, you can use:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

## Usage Examples

### Triggering Events

```php
// User comes online
use App\Events\UserOnlineEvent;
$activeSession = ActiveSession::create([...]);
broadcast(new UserOnlineEvent($user, $activeSession, $totalActiveUsers));

// New order received
use App\Events\NewOrderEvent;
broadcast(new NewOrderEvent($order, $todayOrderCount));

// Stock alert
use App\Events\StockAlertEvent;
broadcast(new StockAlertEvent($product, 'low_stock', $currentStock, $threshold));

// System alert
use App\Events\SystemAlertEvent;
broadcast(SystemAlertEvent::systemError('Database Error', 'Connection timeout'));
```

### Frontend Integration
The events will be broadcasted to the configured channels and can be listened to using JavaScript:

```javascript
// Using Pusher client
const pusher = new Pusher('your-app-key', {
    cluster: 'your-cluster',
    encrypted: true
});

const channel = pusher.subscribe('private-admin-monitoring');
channel.bind('user.online', function(data) {
    // Handle user online event
    updateUserCounter(data.total_active_users);
});
```

## Security

### Channel Authorization
All admin channels require authentication and the user must have the 'admin' role:

```php
Broadcast::channel('admin-monitoring', function ($user) {
    return $user && $user->hasRole('admin');
});
```

### Data Privacy
- User data in broadcast events is limited to essential information only
- Sensitive information is never broadcasted
- All channels use proper authentication

## Production Setup

### Using Pusher (Recommended)
1. Create a Pusher account at https://pusher.com
2. Create a new app
3. Copy the credentials to your `.env` file
4. Configure your frontend to connect to Pusher

### Using Laravel WebSockets (Self-hosted)
For self-hosted solutions, you can use Laravel WebSockets (when compatible with your Laravel version):

1. Install: `composer require beyondcode/laravel-websockets`
2. Publish config: `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"`
3. Run: `php artisan websockets:serve`

## Testing

### Test Broadcasting
```bash
php artisan tinker
>>> broadcast(new App\Events\SystemAlertEvent('test', 'low', 'Test Alert', 'Testing broadcasting system'));
```

### Queue Workers
For production, make sure queue workers are running:
```bash
php artisan queue:work
```

## Troubleshooting

### Common Issues
1. **Events not broadcasting**: Check queue workers are running
2. **Channel authorization fails**: Verify user has 'admin' role
3. **Connection errors**: Check Pusher credentials and network connectivity

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Check logs in `storage/logs/laravel.log` for broadcasting issues.
