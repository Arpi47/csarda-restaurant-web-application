<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')
                ->constrained('menu')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            $table->unique(
                ['menu_id', 'user_id'],
                'menu_reviews_menu_user_unique'
            );
            $table->index(['menu_id', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_reviews');
    }
};
