# E-Commerce Platform - Administrator Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Dashboard Overview](#dashboard-overview)
3. [Performance Monitoring](#performance-monitoring)
4. [User Management](#user-management)
5. [Product Management](#product-management)
6. [Order Management](#order-management)
7. [Real-Time Features](#real-time-features)
8. [System Configuration](#system-configuration)
9. [Troubleshooting](#troubleshooting)
10. [Maintenance Tasks](#maintenance-tasks)

## System Overview

The e-commerce platform is built on Laravel 12 with comprehensive real-time functionality, performance optimization, and advanced monitoring capabilities.

### Key Features
- **Real-time Notifications**: Instant updates for orders, user activities, and system events
- **Performance Monitoring**: Comprehensive system health and optimization tools
- **Advanced Analytics**: Real-time sales metrics and conversion tracking
- **User Activity Tracking**: Live session monitoring and activity logging
- **Queue Management**: Optimized background job processing
- **Caching System**: Redis-based intelligent caching for optimal performance

### System Requirements
- PHP 8.2+
- Laravel 12
- MySQL 8.0+
- Redis 6.0+
- Node.js 18+
- Composer 2.0+

## Dashboard Overview

### Accessing the Admin Dashboard
1. Navigate to `/admin` in your browser
2. Login with your administrator credentials
3. The dashboard provides an overview of:
   - Real-time system metrics
   - Recent orders and activities
   - User statistics
   - Performance indicators

### Dashboard Sections

#### Quick Stats Cards
- **Total Orders**: Current order count with trend indicators
- **Revenue**: Daily/monthly revenue with growth percentages
- **Active Users**: Real-time count of online users
- **System Health**: Overall system performance score

#### Real-Time Activity Feed
- Live updates of user activities
- Order notifications
- System alerts
- Performance warnings

#### Performance Metrics
- Database query performance
- Cache hit rates
- Queue processing status
- Memory and CPU usage

## Performance Monitoring

### Accessing Performance Dashboard
Navigate to `/admin/performance` to access the comprehensive performance monitoring system.

### Features Available

#### System Health Overview
- **Overall Health Score**: 0-100 rating of system performance
- **Status Indicators**: Color-coded health status (Green/Yellow/Red)
- **Critical Alerts**: Immediate notification of system issues
- **Last Check Timestamp**: When the system was last monitored

#### Database Performance
- **Connection Status**: Database connectivity and response times
- **Query Performance**: Slow query detection and optimization suggestions
- **Table Sizes**: Database storage usage by table
- **Index Usage**: Index efficiency statistics

#### Cache Performance
- **Hit Rate**: Percentage of cache hits vs misses
- **Memory Usage**: Current cache memory consumption
- **Key Count**: Total number of cached items
- **Optimization Status**: Cache efficiency recommendations

#### Queue Management
- **Job Statistics**: Waiting, processing, and failed job counts
- **Queue Health**: Overall queue system status
- **Processing Times**: Average job execution times
- **Failed Job Analysis**: Error patterns and retry recommendations

### Performance Optimization Tools

#### One-Click Optimizations
1. **Cache Optimization**
   - Click "Optimize Cache" button
   - Clears expired keys and optimizes memory usage
   - Provides immediate feedback on improvements

2. **Queue Optimization**
   - Analyzes queue performance
   - Provides recommendations for improvements
   - Cleans up old failed jobs

3. **Cache Warming**
   - Preloads critical data into cache
   - Improves response times for common requests
   - Best run during off-peak hours

4. **Full System Optimization**
   - Runs all optimization tools
   - Comprehensive performance improvement
   - Includes database query optimization

#### Automated Monitoring
- **Scheduled Health Checks**: Every 10 minutes
- **Automatic Optimization**: Hourly during low-traffic periods
- **Cache Warming**: Daily at 1:00 AM
- **Alert Notifications**: Immediate alerts for critical issues

### Command Line Tools

#### Performance Monitoring Command
```bash
# Check system status
php artisan performance:monitor

# View detailed statistics
php artisan performance:monitor --stats

# Optimize cache only
php artisan performance:monitor --cache

# Optimize queues only
php artisan performance:monitor --queue

# Warm up caches
php artisan performance:monitor --warmup

# Run full optimization
php artisan performance:monitor --optimize
```

#### Expected Output Examples
```
🚀 Performance Monitoring Tool
================================
📊 Performance Statistics

+-------------------+--------+
| Metric            | Value  |
+-------------------+--------+
| Cache Memory Used | 1.54M  |
| Cache Hit Rate    | 85.2%  |
| Total Keys        | 156    |
+-------------------+--------+

📋 Queue Statistics
+-----------------+---------+---------+----------+
| Queue           | Waiting | Delayed | Reserved |
+-----------------+---------+---------+----------+
| High priority   | 0       | 0       | 0        |
| Default         | 2       | 0       | 1        |
+-----------------+---------+---------+----------+

💾 Memory Usage
+---------------+-------+
| Metric        | Value |
+---------------+-------+
| Current Usage | 45 MB |
| Peak Usage    | 52 MB |
| Memory Limit  | 128M  |
+---------------+-------+
```

## User Management

### User Overview
Access user management at `/admin/users`

#### Features
- **User Listing**: Paginated list of all registered users
- **User Details**: Individual user profiles and activity history
- **Role Management**: Assign admin/user roles
- **Activity Monitoring**: Track user sessions and activities
- **Account Status**: Enable/disable user accounts

#### User Activity Tracking
- **Session Monitoring**: Real-time tracking of active sessions
- **Activity Logs**: Detailed history of user actions
- **Login History**: Track login patterns and locations
- **Performance Impact**: Monitor user activity impact on system performance

#### Session Management
```bash
# Clean up old sessions (older than 60 minutes)
php artisan sessions:cleanup --minutes=60

# View active sessions
php artisan sessions:cleanup --stats
```

## Product Management

### Product Catalog
Access product management at `/admin/products`

#### Features
- **Product CRUD**: Create, read, update, delete products
- **Inventory Management**: Stock level monitoring and alerts
- **Category Management**: Organize products into categories
- **Image Management**: Upload and manage product images
- **Pricing Control**: Set prices and manage discounts

#### Real-Time Inventory
- **Stock Alerts**: Automatic notifications when stock is low
- **Real-Time Updates**: Inventory changes reflected immediately
- **Stock History**: Track inventory changes over time
- **Performance Impact**: Monitor inventory updates on system performance

## Order Management

### Order Processing
Access order management at `/admin/orders`

#### Features
- **Order Listing**: View all orders with filtering and search
- **Order Details**: Complete order information and history
- **Status Management**: Update order status with real-time notifications
- **Transaction Tracking**: View payment and transaction details
- **Customer Communication**: Send updates to customers

#### Real-Time Order Updates
- **Live Notifications**: Instant alerts for new orders
- **Status Broadcasting**: Real-time updates to customers
- **Admin Notifications**: Immediate alerts for order issues
- **Performance Monitoring**: Track order processing performance

#### Order Status Flow
1. **Pending**: Order received, payment processing
2. **Confirmed**: Payment confirmed, preparing for shipping
3. **Processing**: Order being prepared
4. **Shipped**: Order dispatched to customer
5. **Delivered**: Order successfully delivered
6. **Cancelled**: Order cancelled by customer or admin

## Real-Time Features

### Broadcasting System
The platform uses Laravel Broadcasting with Redis and Pusher for real-time updates.

#### Configuration
```php
// config/broadcasting.php
'default' => env('BROADCAST_DRIVER', 'redis'),

'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
        ],
    ],
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
    ],
],
```

#### Real-Time Events
1. **New Order Events**: Broadcasted to admin dashboard
2. **User Activity Events**: Live user activity updates
3. **Notification Events**: Instant user notifications
4. **System Health Events**: Performance alerts
5. **Inventory Updates**: Stock level changes

#### Monitoring Real-Time Performance
- **Event Broadcasting**: Monitor event firing and delivery
- **WebSocket Connections**: Track active connections
- **Message Queue**: Monitor real-time message processing
- **Performance Impact**: Assess real-time features on system performance

### Session Tracking
```bash
# View active sessions
GET /admin/dashboard/live-stats

# Response example:
{
    "success": true,
    "data": {
        "active_users": 15,
        "total_sessions": 23,
        "average_session_duration": "00:15:30"
    }
}
```

## System Configuration

### Environment Configuration
Key environment variables for optimal performance:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=root
DB_PASSWORD=

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Broadcasting Configuration
BROADCAST_DRIVER=redis
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### Performance Settings
```env
# PHP Configuration (php.ini)
memory_limit=256M
max_execution_time=300
upload_max_filesize=10M
post_max_size=10M

# MySQL Configuration (my.cnf)
innodb_buffer_pool_size=1G
query_cache_size=256M
max_connections=200

# Redis Configuration (redis.conf)
maxmemory=512mb
maxmemory-policy=allkeys-lru
```

## Troubleshooting

### Common Issues and Solutions

#### Performance Issues
1. **Slow Page Load Times**
   - Check cache hit rate in performance dashboard
   - Run cache optimization: `php artisan performance:monitor --cache`
   - Verify database indexes are applied: `php artisan migrate:status`

2. **High Memory Usage**
   - Monitor memory usage in performance dashboard
   - Clear application cache: `php artisan cache:clear`
   - Restart PHP-FPM service

3. **Queue Delays**
   - Check queue status: `php artisan queue:work --timeout=60`
   - Monitor failed jobs: `php artisan queue:failed`
   - Restart queue workers: `php artisan queue:restart`

#### Database Issues
1. **Slow Queries**
   - Check slow query log in performance dashboard
   - Verify indexes: `SHOW INDEX FROM table_name`
   - Run database optimization: `php artisan db:optimize`

2. **Connection Errors**
   - Verify database credentials in `.env`
   - Check database server status
   - Test connection: `php artisan tinker` → `DB::connection()->getPdo()`

#### Real-Time Features
1. **Events Not Broadcasting**
   - Check broadcasting configuration
   - Verify Redis connection: `redis-cli ping`
   - Test event firing: `php artisan tinker` → `event(new TestEvent())`

2. **WebSocket Connection Issues**
   - Verify Pusher/WebSocket server status
   - Check browser console for connection errors
   - Test with browser developer tools

### Debugging Tools

#### Laravel Telescope (if installed)
- Access at `/telescope`
- Monitor queries, jobs, events, and exceptions
- Real-time debugging of performance issues

#### Log Files
```bash
# Application logs
tail -f storage/logs/laravel.log

# Performance monitoring logs
tail -f storage/logs/performance.log

# Queue processing logs
tail -f storage/logs/queue.log
```

#### Database Debugging
```sql
-- Check slow queries
SHOW PROCESSLIST;

-- Analyze table performance
ANALYZE TABLE orders;

-- Check index usage
EXPLAIN SELECT * FROM orders WHERE status = 'pending';
```

## Maintenance Tasks

### Daily Tasks
1. **System Health Check**
   ```bash
   php artisan performance:monitor --stats
   ```

2. **Cache Optimization**
   ```bash
   php artisan performance:monitor --cache
   ```

3. **Session Cleanup**
   ```bash
   php artisan sessions:cleanup
   ```

### Weekly Tasks
1. **Full System Optimization**
   ```bash
   php artisan performance:monitor --optimize
   ```

2. **Database Optimization**
   ```bash
   php artisan db:optimize
   php artisan queue:prune-failed --hours=168
   ```

3. **Log Cleanup**
   ```bash
   php artisan log:clear
   ```

### Monthly Tasks
1. **Performance Review**
   - Review performance trends in dashboard
   - Analyze user activity patterns
   - Plan capacity upgrades if needed

2. **Security Updates**
   ```bash
   composer update
   php artisan migrate
   npm update && npm run build
   ```

3. **Backup Verification**
   - Test database backup restoration
   - Verify file backup integrity
   - Update disaster recovery procedures

### Scheduled Tasks (Cron Jobs)
Add to server crontab:
```bash
# Laravel scheduler (handles all scheduled tasks)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1

# Additional monitoring (optional)
*/10 * * * * cd /path/to/project && php artisan performance:monitor --stats >> /var/log/performance.log 2>&1
```

### Emergency Procedures

#### System Overload
1. **Immediate Actions**
   ```bash
   # Clear all caches
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   
   # Restart services
   sudo systemctl restart php8.2-fpm
   sudo systemctl restart redis-server
   sudo systemctl restart mysql
   ```

2. **Monitor Recovery**
   - Check performance dashboard
   - Monitor error logs
   - Verify real-time features are working

#### Database Issues
1. **Connection Pool Exhaustion**
   ```bash
   # Check active connections
   mysql -e "SHOW PROCESSLIST;"
   
   # Kill long-running queries
   mysql -e "KILL [process_id];"
   ```

2. **Lock Issues**
   ```bash
   # Check for locked tables
   mysql -e "SHOW OPEN TABLES WHERE In_use > 0;"
   
   # Check for deadlocks
   mysql -e "SHOW ENGINE INNODB STATUS;"
   ```

### Contact Information
For technical support or emergency assistance:
- **System Administrator**: admin@yoursite.com
- **Development Team**: dev@yoursite.com
- **Emergency Hotline**: +1-xxx-xxx-xxxx

---

*This documentation is maintained by the development team and updated with each system release. Last updated: July 27, 2025*
