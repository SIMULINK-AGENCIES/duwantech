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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->boolean('email_orders')->default(true);
            $table->boolean('email_payments')->default(true);
            $table->boolean('email_inventory')->default(true);
            $table->boolean('email_users')->default(false);
            $table->boolean('email_system')->default(true);
            $table->boolean('push_orders')->default(true);
            $table->boolean('push_payments')->default(true);
            $table->boolean('push_inventory')->default(true);
            $table->boolean('push_users')->default(false);
            $table->boolean('push_system')->default(true);
            $table->boolean('sound_alerts')->default(false);
            $table->enum('email_frequency', ['instant', 'hourly', 'daily', 'weekly'])->default('instant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
