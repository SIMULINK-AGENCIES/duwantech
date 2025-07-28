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
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('widget_id', 100)->unique(); // Unique identifier for the widget type
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('category', 50); // kpi, charts, data, system, notifications
            $table->enum('size', ['small', 'medium', 'large'])->default('medium');
            $table->string('component_path', 255); // Path to the Blade component
            $table->json('default_config')->nullable(); // Default configuration for the widget
            $table->json('config_schema')->nullable(); // JSON schema for widget configuration validation
            $table->string('data_endpoint', 255)->nullable(); // API endpoint for widget data
            $table->integer('refresh_interval')->default(300); // Refresh interval in seconds
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('cache_enabled')->default(true);
            $table->json('permissions')->nullable(); // Required permissions to use widget
            $table->string('icon', 100)->nullable(); // Widget icon class or path
            $table->string('preview_image', 255)->nullable(); // Preview image path
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable(); // Additional widget metadata
            $table->timestamps();
            
            // Indexes
            $table->index(['category', 'is_active']);
            $table->index(['is_premium', 'is_active']);
            $table->index('sort_order');
            $table->index('widget_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};
