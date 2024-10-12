<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->mediumText('body');
            $table->boolean('active')->default(false);
            $table->string('image')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->boolean('pinned')->default(false);

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('serie_id')
                ->nullable()
                ->default(null);
            $table->foreign('serie_id')
                ->references('id')
                ->on('series')
                ->nullable()
                ->default(null)
                ->onDelete('cascade');

            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->default(null);
            $table->foreign('parent_id')
                ->references('id')
                ->on('posts')
                ->nullable()
                ->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
