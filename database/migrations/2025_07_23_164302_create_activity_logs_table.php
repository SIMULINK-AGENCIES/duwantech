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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('action', [
                'login', 'logout', 'register', 'profile_update',
                'order_created', 'order_updated', 'order_cancelled',
                'payment_success', 'payment_failed', 'payment_pending',
                'product_viewed', 'product_added_to_cart', 'product_purchased',
                'admin_login', 'admin_action', 'system_error'
            ])->index();
            $table->text('details')->nullable(); // JSON or text description
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamps();
            
            // Composite indexes for efficient queries
            $table->index(['user_id', 'action', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['created_at', 'action']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
