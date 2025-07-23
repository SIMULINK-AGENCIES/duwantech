# Settings System Documentation

## Overview

This e-commerce application now features a comprehensive database-driven settings system that replaces the old `.env` file approach. The system includes two main components:

1. **General Settings** (Frontend Control) - Controls all frontend appearance and behavior
2. **M-Pesa Settings** - Dedicated M-Pesa payment configuration

## Features

### General Settings (Frontend Control)
- **Database-driven**: All settings stored in `general_settings` table
- **Categorized**: Settings organized into logical categories (site, appearance, contact, social, features, analytics)
- **Type-safe**: Settings have types (string, boolean, integer, color, url, email) with automatic validation
- **Public/Private**: Settings can be marked as public (accessible to frontend) or private (admin-only)
- **Cached**: All settings are cached for performance
- **Real-time**: Changes are immediately available without server restart

### M-Pesa Settings
- **Dedicated configuration**: Separate table for M-Pesa-specific settings
- **Environment switching**: Easy sandbox/live environment switching
- **Amount limits**: Configurable min/max transaction amounts
- **Callback URLs**: Auto-generated and custom callback URL support
- **Connection testing**: Built-in M-Pesa connection validation
- **Export/Import**: Configuration backup and restore

## Admin Interface

### General Settings (/admin/frontend)
- **Tabbed interface**: Organized into logical sections
- **Real-time preview**: Color picker with live preview
- **Validation**: Form validation with helpful error messages
- **Responsive**: Mobile-friendly admin interface

### M-Pesa Settings (/admin/mpesa)
- **Step-by-step configuration**: Guided setup process
- **Status indicators**: Visual feedback on configuration status
- **Callback URL generator**: Automatic callback URL generation
- **Test functionality**: Built-in connection testing
- **Security**: Masked sensitive data display

## API Endpoints

### Public Settings API
```
GET /admin/api/frontend/public
```
Returns all public settings for frontend consumption.

### M-Pesa APIs
```
POST /admin/mpesa/test        - Test M-Pesa connection
POST /admin/mpesa/reset       - Reset to defaults
GET  /admin/mpesa/callbacks   - Get callback URLs
GET  /admin/mpesa/export      - Export configuration
```

## Helper Functions

The system includes several helper functions for easy access:

```php
// Get a general setting
$siteName = settings('site_name', 'Default Store');

// Get M-Pesa settings instance
$mpesa = mpesa_settings();

// Get specific M-Pesa setting
$isEnabled = mpesa_setting('is_enabled', false);

// Get all public settings
$publicSettings = public_settings();

// Check if M-Pesa is configured
if (is_mpesa_enabled()) {
    // M-Pesa payment logic
}
```

## Database Schema

### General Settings Table
```sql
- id: Primary key
- key: Unique setting identifier
- value: Setting value (stored as text)
- type: Data type (string, boolean, integer, color, url, email)
- category: Setting category (site, appearance, contact, etc.)
- label: Human-readable label
- description: Setting description
- is_public: Whether setting is accessible to frontend
- is_required: Whether setting is required
- sort_order: Display order within category
- timestamps: Created/updated timestamps
```

### M-Pesa Settings Table
```sql
- id: Primary key
- consumer_key: M-Pesa consumer key
- consumer_secret: M-Pesa consumer secret (encrypted)
- passkey: M-Pesa passkey (encrypted)
- shortcode: Business shortcode
- environment: sandbox/live
- is_enabled: Whether M-Pesa is active
- callback_url: Custom STK callback URL
- confirmation_url: Custom confirmation URL
- validation_url: Custom validation URL
- min_amount: Minimum transaction amount
- max_amount: Maximum transaction amount
- account_reference: Default account reference
- transaction_desc: Default transaction description
- timestamps: Created/updated timestamps
```

## Caching Strategy

- **Individual settings**: Cached for 1 hour with key `general_setting.{key}`
- **Public settings**: Cached for 1 hour with key `general_settings_public`
- **Category settings**: Cached for 1 hour with key `general_settings_category.{category}`
- **M-Pesa settings**: Cached for 30 minutes with key `mpesa_settings`
- **Auto-invalidation**: Cache automatically cleared when settings change

## Security Features

1. **Sensitive data masking**: Consumer secrets and passkeys are masked in display
2. **Type validation**: All inputs validated based on setting type
3. **CSRF protection**: All forms protected with CSRF tokens
4. **Access control**: Admin role required for settings management
5. **Encrypted storage**: Sensitive M-Pesa credentials can be encrypted

## Performance Optimizations

1. **Database indexing**: Proper indexes on frequently queried columns
2. **Eager loading**: Settings loaded efficiently with minimal queries
3. **Cache warming**: Settings pre-loaded into cache
4. **Lazy loading**: Settings loaded only when needed
5. **Batch operations**: Multiple settings updated in single transaction

## Frontend Integration

Settings are automatically available in all views via the `SettingsServiceProvider`:

```blade
<!-- Access public settings in any view -->
<h1>{{ $publicSettings['site_name'] ?? 'Default Store' }}</h1>
<meta name="description" content="{{ $publicSettings['site_description'] ?? '' }}">

<!-- Use settings in components -->
@if($publicSettings['enable_reviews'] ?? false)
    @include('components.reviews')
@endif
```

## Migration and Seeding

Run the following commands to set up the settings system:

```bash
# Run migrations
php artisan migrate

# Seed default settings
php artisan db:seed --class=GeneralSettingsSeeder
php artisan db:seed --class=MpesaSettingsSeeder

# Or seed everything
php artisan db:seed
```

## Customization

### Adding New Settings

1. Add setting to `GeneralSettingsSeeder`
2. Run the seeder to create the setting
3. Add form field to appropriate view tab
4. Setting will be automatically processed

### Adding New Setting Types

1. Update validation rules in `GeneralSettingsController`
2. Add type handling in `GeneralSetting` model
3. Update view with appropriate input field

### Custom Categories

Settings can be organized into any categories you need. Simply create settings with new category names and they'll be automatically grouped.

## Troubleshooting

### Settings Not Updating
- Check cache: `php artisan cache:clear`
- Verify permissions: Ensure user has admin role
- Check validation: Look for form validation errors

### M-Pesa Issues
- Verify all required fields are filled
- Test connection using built-in test feature
- Check callback URLs are accessible
- Verify environment setting matches M-Pesa dashboard

### Performance Issues
- Check cache status: Ensure Redis/Memcached is working
- Monitor database queries: Use Laravel Debugbar
- Optimize indexes: Ensure proper database indexing

## Future Enhancements

1. **Settings history**: Track changes to settings over time
2. **Environment-specific settings**: Different settings per environment
3. **Settings backup/restore**: Full backup and restore functionality
4. **Settings validation rules**: Custom validation rules per setting
5. **Settings scheduling**: Time-based setting changes
6. **Multi-tenant settings**: Different settings per tenant/store
