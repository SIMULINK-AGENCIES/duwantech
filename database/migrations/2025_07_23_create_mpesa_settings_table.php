<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mpesa_settings', function (Blueprint $table) {
            $table->id();
            $table->string('consumer_key')->nullable();
            $table->string('consumer_secret')->nullable();
            $table->string('passkey')->nullable();
            $table->string('shortcode')->nullable();
            $table->enum('environment', ['sandbox', 'live'])->default('sandbox');
            $table->boolean('is_enabled')->default(false);
            $table->string('callback_url')->nullable();
            $table->string('confirmation_url')->nullable();
            $table->string('validation_url')->nullable();
            $table->decimal('min_amount', 10, 2)->default(1.00);
            $table->decimal('max_amount', 10, 2)->default(70000.00);
            $table->string('account_reference')->nullable();
            $table->string('transaction_desc')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mpesa_settings');
    }
};
