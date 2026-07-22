<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->index('date_time');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('fname', 100);
            $table->string('lname', 100);
            $table->string('email', 150);
            $table->dateTime('date_time');
            $table->unsignedInteger('guests');
            $table->string('status', 20)
                ->default('pending');
            $table->string('language', 10)
                ->default('en');
            $table->timestamp('status_changed_at')
                ->nullable();
            $table->unsignedBigInteger('status_changed_by')
                ->nullable();
            $table->timestamps();
            $table->unique(
                ['email', 'date_time'],
                'unique_booking'
            );
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};