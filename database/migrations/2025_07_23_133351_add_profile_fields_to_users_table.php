<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile fields
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio');
            $table->string('timezone', 50)->default('UTC')->after('avatar');
            $table->string('language', 10)->default('en')->after('timezone');
            
            // Authentication tracking
            $table->timestamp('last_login_at')->nullable()->after('language');
            $table->integer('login_count')->default(0)->after('last_login_at');
            
            // Notification preferences
            $table->boolean('notification_email')->default(true)->after('login_count');
            $table->boolean('notification_browser')->default(true)->after('notification_email');
            $table->boolean('notification_orders')->default(true)->after('notification_browser');
            $table->boolean('notification_products')->default(true)->after('notification_orders');
            $table->boolean('notification_users')->default(true)->after('notification_products');
            
            // Two-factor authentication
            $table->boolean('two_factor_enabled')->default(false)->after('notification_users');
            $table->enum('two_factor_method', ['email', 'sms', 'app'])->nullable()->after('two_factor_enabled');
            
            // API access
            $table->boolean('api_access_enabled')->default(false)->after('two_factor_method');
            $table->string('api_token')->nullable()->after('api_access_enabled');
            $table->integer('api_rate_limit')->default(1000)->after('api_token');
            
            // Indexes for performance
            $table->index('last_login_at');
            $table->index('api_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
