<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the table and recreate with correct enum values
        Schema::dropIfExists('admin_notifications');
        
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['info', 'warning', 'success', 'error'])->default('info')->index();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->index();
            $table->string('title', 255);
            $table->text('message');
            $table->json('data')->nullable(); // Additional context data
            $table->string('action_url', 500)->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            // Indexes for common queries
            $table->index(['type', 'created_at']);
            $table->index(['priority', 'created_at']);
            $table->index(['is_read', 'created_at']);
            $table->index(['created_at', 'type']);
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
        
        // Recreate the original table structure
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['order', 'payment', 'inventory', 'user', 'system'])->index();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->index();
            $table->string('title', 255);
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('action_url', 500)->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index(['priority', 'created_at']);
            $table->index(['read_at', 'created_at']);
            $table->index(['created_at', 'type']);
        });
    }
};
