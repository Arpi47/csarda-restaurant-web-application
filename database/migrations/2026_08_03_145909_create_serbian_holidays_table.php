<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serbian_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('google_event_id')->unique();
            $table->string('name');
            $table->date('date');
            $table->boolean('restaurant_is_active')->default(false);
            $table->time('restaurant_open_time')->nullable();
            $table->time('restaurant_close_time')->nullable();
            $table->time('restaurant_last_reservation_time')->nullable();
            $table->boolean('kitchen_is_active')->default(false);
            $table->time('kitchen_open_time')->nullable();
            $table->time('kitchen_close_time')->nullable();
            $table->time('kitchen_last_order_time')->nullable();
            $table->timestamps();
            $table->unique(['date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serbian_holidays');
    }
};
