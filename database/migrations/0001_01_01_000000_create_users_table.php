<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')
                ->unique();
            $table->string('first_name', 50)
                ->nullable();
            $table->string('last_name', 50)
                ->nullable();
            $table->string('profile_image')
                ->nullable();
            $table->string('password')
                ->nullable();
            $table->timestamp('email_verified_at')
                ->nullable();
            $table->boolean('is_suspended')
                ->default(false);
            $table->boolean('deletion_requested')
                ->default(false);
            $table->timestamp('deletion_requested_at')
                ->nullable();
            $table->timestamp('deletion_will_be_final_at')
                ->nullable();
            $table->unsignedInteger('deletion_attempts_last_24h')
                ->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')
                ->primary();
            $table->foreignId('user_id')
                ->nullable()
                ->index()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('ip_address', 45)
                ->nullable();
            $table->text('user_agent')
                ->nullable();
            $table->longText('payload');
            $table->integer('last_activity')
                ->index();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};