# 🔐 Admin Dashboard Setup Guide

## 📋 Overview

The admin dashboard provides complete control over the e-commerce website with role-based access control using Spatie Permissions.

## 🚀 Access Information

### Admin Login Credentials
- **URL**: `http://127.0.0.1:8000/admin`
- **Email**: `admin@duwantech.co.ke`
- **Password**: `password`

### First Time Setup
1. Run the admin seeder to create the admin user:
   ```bash
   php artisan db:seed --class=AdminSeeder
   ```

2. Login with the credentials above

## 🛠️ Admin Features

### 📊 Dashboard
- **Statistics Overview**: Total orders, products, users, revenue
- **Recent Orders**: Latest 10 orders with status
- **Top Products**: Best selling products
- **Quick Actions**: Direct links to add products, categories, settings

### 📦 Product Management
- **View All Products**: List with images, prices, status
- **Add New Product**: Name, description, price, category, images, JSON attributes
- **Edit Products**: Update all product details
- **Delete Products**: Safe deletion (prevents deletion if orders exist)
- **Product Status**: Active/Inactive toggle
- **Featured Products**: Mark products as featured

### 📂 Category Management
- **View Categories**: Hierarchical display with subcategories
- **Add Categories**: Create main categories and subcategories
- **Edit Categories**: Update names, descriptions, parent relationships
- **Delete Categories**: Safe deletion (prevents deletion if products/subcategories exist)

### 📋 Order Management
- **View All Orders**: Complete order list with customer details
- **Order Details**: Full order information with payment status
- **Update Order Status**: Pending → Processing → Completed → Cancelled
- **M-PESA Transactions**: Monitor payment transactions and revenue

### ⚙️ Settings Management
- **General Settings**: Site name, description, contact info
- **M-PESA Configuration**: API credentials, shortcode, passkey
- **Third Party Integrations**: Tawk.to live chat, Google Analytics
- **Social Media Links**: Facebook, Instagram, Twitter URLs

## 🔐 Security Features

### Role-Based Access Control
- **Admin Role**: Full access to all features
- **Permission System**: Granular permissions for different actions
- **Middleware Protection**: All admin routes protected by auth and role middleware

### Admin Permissions
- `view_dashboard` - Access admin dashboard
- `manage_products` - Create, edit, delete products
- `manage_categories` - Create, edit, delete categories
- `manage_orders` - View and update orders
- `manage_users` - User management (future)
- `manage_settings` - Update site settings
- `view_transactions` - View M-PESA transactions

## 🎨 UI Features

### Modern Design
- **Responsive Layout**: Works on desktop, tablet, and mobile
- **Tailwind CSS**: Clean, modern styling
- **Sidebar Navigation**: Easy access to all sections
- **Status Indicators**: Color-coded order and product status
- **Quick Actions**: Fast access to common tasks

### User Experience
- **Success/Error Messages**: Clear feedback for all actions
- **Confirmation Dialogs**: Safe deletion with confirmations
- **Loading States**: Visual feedback during operations
- **Search & Filter**: Easy product and order management

## 🔧 Technical Implementation

### Controllers
- `AdminController` - Dashboard and general admin functions
- `ProductController` - Product CRUD operations
- `CategoryController` - Category management
- `OrderController` - Order and transaction management
- `SettingsController` - Site configuration

### Models & Relationships
- **User** - Has roles and permissions via Spatie
- **Product** - Belongs to category, has orders
- **Category** - Has products, can have parent/children
- **Order** - Belongs to user and product, has payment
- **Payment** - Belongs to order

### Routes
All admin routes are prefixed with `/admin` and protected by:
- `auth` middleware - Requires authentication
- `role:admin` middleware - Requires admin role

## 🚀 Quick Start

1. **Login**: Visit `/admin` and login with admin credentials
2. **Add Categories**: Create product categories first
3. **Add Products**: Create products with images and attributes
4. **Configure M-PESA**: Set up payment credentials in settings
5. **Monitor Orders**: Track orders and payments in real-time

## 🔄 Future Enhancements

- **User Management**: Add/remove users, assign roles
- **Analytics Dashboard**: Sales reports, charts, insights
- **Bulk Operations**: Import/export products, bulk status updates
- **Notification System**: Email/SMS alerts for new orders
- **Advanced Filtering**: Search and filter products/orders
- **API Management**: Manage API keys and webhooks

## 🛡️ Security Best Practices

1. **Change Default Password**: Update admin password after first login
2. **Use HTTPS**: Always use HTTPS in production
3. **Regular Backups**: Backup database and files regularly
4. **Monitor Logs**: Check Laravel logs for suspicious activity
5. **Update Dependencies**: Keep Laravel and packages updated
6. **Environment Variables**: Store sensitive data in `.env` file

## 📞 Support

For technical support or questions about the admin dashboard:
- Check Laravel logs: `storage/logs/laravel.log`
- Review documentation in `README.md`
- Contact development team for assistance 