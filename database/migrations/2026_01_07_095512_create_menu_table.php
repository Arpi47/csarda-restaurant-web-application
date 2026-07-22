<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();
            $table->text('name_hu');
            $table->text('name_en');
            $table->text('name_sr_lat');
            $table->text('name_sr_cyr');
            $table->text('description_hu');
            $table->text('description_en');
            $table->text('description_sr_lat');
            $table->text('description_sr_cyr');
            $table->decimal('price', 10, 2);
            $table->string('image');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};