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
        Schema::create('user_widget_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('widget_id', 100); // References dashboard_widgets.widget_id
            $table->json('position'); // Widget position: {x, y, width, height}
            $table->json('config')->nullable(); // User-specific widget configuration
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('last_accessed')->nullable();
            $table->json('metadata')->nullable(); // Additional user-specific metadata
            $table->timestamps();
            
            // Composite unique constraint - one config per user per widget
            $table->unique(['user_id', 'widget_id']);
            
            // Indexes
            $table->index(['user_id', 'is_enabled']);
            $table->index('widget_id');
            $table->index('sort_order');
            $table->index('last_accessed');
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('widget_id')->references('widget_id')->on('dashboard_widgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_widget_configs');
    }
};
