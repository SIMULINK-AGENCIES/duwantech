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
        // Orders table indexes for analytics performance
        Schema::table('orders', function (Blueprint $table) {
            // Check if indexes don't exist before creating them
            if (!$this->indexExists('orders', 'orders_created_status_idx')) {
                $table->index(['created_at', 'status'], 'orders_created_status_idx');
            }
            if (!$this->indexExists('orders', 'orders_user_created_idx')) {
                $table->index(['user_id', 'created_at'], 'orders_user_created_idx');
            }
            if (!$this->indexExists('orders', 'orders_status_amount_idx')) {
                $table->index(['status', 'amount'], 'orders_status_amount_idx');
            }
            if (!$this->indexExists('orders', 'orders_updated_idx')) {
                $table->index('updated_at', 'orders_updated_idx');
            }
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_created_idx')) {
                $table->index('created_at', 'users_created_idx');
            }
            if (!$this->indexExists('users', 'users_updated_idx')) {
                $table->index('updated_at', 'users_updated_idx');
            }
        });

        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            if (!$this->indexExists('products', 'products_created_idx')) {
                $table->index('created_at', 'products_created_idx');
            }
            if (!$this->indexExists('products', 'products_category_idx')) {
                $table->index('category_id', 'products_category_idx');
            }
            if (!$this->indexExists('products', 'products_price_idx')) {
                $table->index('price', 'products_price_idx');
            }
        });
    }

    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = ? 
            AND table_name = ? 
            AND index_name = ?
        ", [$databaseName, $table, $index]);
        
        return $result[0]->count > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_created_status_idx');
            $table->dropIndex('orders_user_created_idx');
            $table->dropIndex('orders_product_created_idx');
            $table->dropIndex('orders_status_amount_idx');
            $table->dropIndex('orders_updated_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_created_idx');
            $table->dropIndex('users_updated_idx');
            $table->dropIndex('users_verified_created_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_created_idx');
            $table->dropIndex('products_category_idx');
            $table->dropIndex('products_price_idx');
        });

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex('activity_logs_user_created_idx');
                $table->dropIndex('activity_logs_action_created_idx');
                $table->dropIndex('activity_logs_created_idx');
            });
        }

        if (Schema::hasTable('active_sessions')) {
            Schema::table('active_sessions', function (Blueprint $table) {
                $table->dropIndex('sessions_user_activity_idx');
                $table->dropIndex('sessions_activity_idx');
                $table->dropIndex('sessions_online_activity_idx');
            });
        }
    }
};
