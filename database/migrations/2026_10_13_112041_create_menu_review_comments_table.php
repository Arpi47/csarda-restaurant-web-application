<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_review_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')
                ->constrained('menu')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('menu_review_comments')
                ->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
            $table->index(['menu_id', 'created_at']);
            $table->index('parent_id');
        });

        $reviews = DB::table('menu_reviews')
            ->orderBy('id')
            ->get();

        foreach ($reviews as $review) {
            $commentId = null;

            if (
                $review->comment !== null
                && trim($review->comment) !== ''
            ) {
                $commentId = DB::table('menu_review_comments')
                    ->insertGetId([
                        'menu_id' => $review->menu_id,
                        'user_id' => $review->user_id,
                        'parent_id' => null,
                        'body' => trim($review->comment),
                        'is_admin' => false,
                        'created_at' => $review->created_at,
                        'updated_at' => $review->updated_at,
                    ]);
            }

            if (
                $commentId !== null
                && isset($review->admin_reply)
                && trim($review->admin_reply) !== ''
            ) {
                DB::table('menu_review_comments')->insert([
                    'menu_id' => $review->menu_id,
                    'user_id' => null,
                    'parent_id' => $commentId,
                    'body' => trim($review->admin_reply),
                    'is_admin' => true,
                    'created_at' => $review->updated_at
                        ?? $review->created_at,
                    'updated_at' => $review->updated_at
                        ?? $review->created_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_review_comments');
    }
};