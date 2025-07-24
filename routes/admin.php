<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\GeneralSettingsController;
use App\Http\Controllers\Admin\MpesaController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Products
    Route::resource('products', ProductController::class);
    
    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('transactions', [OrderController::class, 'transactions'])->name('orders.transactions');
    
    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    
    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // General Settings (Frontend Control)
    Route::get('frontend', [GeneralSettingsController::class, 'index'])->name('frontend.index');
    Route::put('frontend', [GeneralSettingsController::class, 'update'])->name('frontend.update');
    Route::get('api/frontend/public', [GeneralSettingsController::class, 'getPublicSettings'])->name('frontend.public');
    
    // M-Pesa Settings
    Route::get('mpesa', [MpesaController::class, 'index'])->name('mpesa.index');
    Route::put('mpesa', [MpesaController::class, 'update'])->name('mpesa.update');
    Route::post('mpesa/test', [MpesaController::class, 'testConnection'])->name('mpesa.test');
    Route::post('mpesa/reset', [MpesaController::class, 'reset'])->name('mpesa.reset');
    Route::get('mpesa/callbacks', [MpesaController::class, 'generateCallbacks'])->name('mpesa.callbacks');
    Route::get('mpesa/export', [MpesaController::class, 'export'])->name('mpesa.export');
    
    // Live User Monitoring API
    Route::get('api/live-stats', [AdminController::class, 'getLiveStats'])->name('api.live-stats');
    
    // Activity Feed
    Route::get('activity', [App\Http\Controllers\Admin\ActivityController::class, 'index'])->name('activity.index');
    Route::get('api/live-activities', [App\Http\Controllers\Admin\ActivityController::class, 'getLiveActivities'])->name('api.live-activities');
    
    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    
    // Notification API endpoints for real-time updates
    Route::prefix('api/notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications']);
        Route::get('/count', [NotificationController::class, 'getUnreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/{notification}/unread', [NotificationController::class, 'markAsUnread']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::post('/bulk-delete', [NotificationController::class, 'bulkDelete']);
        Route::post('/bulk-read', [NotificationController::class, 'bulkMarkAsRead']);
        Route::get('/stats', [NotificationController::class, 'getStats']);
        Route::post('/test', [NotificationController::class, 'createTestNotification']);
        Route::post('/clear-old', [NotificationController::class, 'clearOld']);
    });
    
    // Legacy notification routes (for backward compatibility)
    Route::get('notifications/dropdown', [NotificationController::class, 'dropdown'])->name('notifications.dropdown');
    Route::get('notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('notifications/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('notifications.bulk-delete');
    Route::post('notifications/test', [NotificationController::class, 'createTest'])->name('notifications.test');
    Route::post('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences');
    
    // Profile Management (using simple name for admin.profile route)
    Route::get('profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('profile/two-factor', [ProfileController::class, 'updateTwoFactor'])->name('profile.two-factor');
    Route::put('profile/api', [ProfileController::class, 'updateApiAccess'])->name('profile.api');
    Route::delete('profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::get('profile/export', [ProfileController::class, 'exportData'])->name('profile.export');
    Route::get('profile/activity', [ProfileController::class, 'getActivityLog'])->name('profile.activity');
});