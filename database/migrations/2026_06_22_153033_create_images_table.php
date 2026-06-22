<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('url'); // абсолютный HTTPS URL
            $table->string('alt');
            $table->string('title');
            $table->boolean('is_cover')->default(false);
            $table->integer('sort_order')->default(0);

            // Полиморфная связь
            $table->uuid('imageable_id');
            $table->string('imageable_type');
            $table->index(['imageable_id', 'imageable_type']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
