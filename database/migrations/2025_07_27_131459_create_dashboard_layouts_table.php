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
        Schema::create('dashboard_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->json('layout_config'); // Contains grid configuration, responsive breakpoints
            $table->json('widget_positions'); // Default widget positions for this layout
            $table->enum('type', ['system', 'custom', 'shared'])->default('custom');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('usage_count')->default(0);
            $table->json('metadata')->nullable(); // Additional layout information
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['is_public', 'is_active']);
            $table->index('created_by');
            $table->index('slug');
            
            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_layouts');
    }
};
