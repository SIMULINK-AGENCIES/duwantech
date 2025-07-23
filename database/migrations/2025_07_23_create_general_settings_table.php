<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, array, json
            $table->string('category')->default('general'); // general, appearance, contact, seo, social
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // can be accessed by frontend
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category', 'sort_order']);
            $table->index('is_public');
        });
    }

    public function down()
    {
        Schema::dropIfExists('general_settings');
    }
};
