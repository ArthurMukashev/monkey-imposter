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
        Schema::create('places', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->enum('section', ['tourism', 'active', 'gastronomy']);
            $table->string('title');
            $table->string('short_description');
            $table->text('description_html')->nullable();

            // Внешние ключи на справочники
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignUuid('city_id')->constrained('cities')->cascadeOnDelete();

            // Координаты
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('address')->nullable();
            $table->string('working_hours')->nullable(); // человекочитаемая строка
            $table->string('average_bill')->nullable(); // для гастрономии
            $table->text('menu_html')->nullable(); // только для гастрономии

            // Расписание – JSON
            $table->json('schedule')->nullable(); // { date, time, timezone }

            // SEO
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_canonical_path')->nullable();

            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index('section');
            $table->index('category_id');
            $table->index('city_id');
            $table->index('is_published');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
