<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('restaurant');
            $table->unsignedTinyInteger('day_of_week');
            $table->boolean('is_active')->default(true);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->time('last_reservation_time')->nullable();
            $table->timestamps();
            $table->unique(['type', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_hours');
    }
};
