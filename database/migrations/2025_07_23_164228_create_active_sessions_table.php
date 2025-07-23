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
        Schema::create('active_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id', 255)->unique();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->json('location')->nullable(); // {country, city, lat, lng}
            $table->string('page_url', 500)->nullable();
            $table->timestamp('last_activity')->index();
            $table->timestamps();
            
            // Additional indexes for common queries
            $table->index(['user_id', 'last_activity']);
            $table->index(['last_activity', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_sessions');
    }
};
