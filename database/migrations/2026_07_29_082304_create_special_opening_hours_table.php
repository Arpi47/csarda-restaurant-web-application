<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('restaurant');
            $table->date('date');
            $table->boolean('is_active')->default(true);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->time('last_reservation_time')->nullable();
            $table->boolean('is_google_calendar')->default(false);
            $table->string('google_calendar_event_id')->nullable()->unique();
            $table->boolean('is_manually_overridden')->default(false);
            $table->boolean('is_manually_deleted')->default(false);
            $table->timestamps();
            $table->unique(['type', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_opening_hours');
    }
};
