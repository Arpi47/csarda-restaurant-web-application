<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 100);
            $table->string('name_hu', 100);
            $table->string('name_sr', 100);
            $table->string('name_sr_cyrl', 100);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_event_types');
    }
};