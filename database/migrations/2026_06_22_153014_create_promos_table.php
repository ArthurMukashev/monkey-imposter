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
        Schema::create('promos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('placement', ['home', 'section', 'place-details', 'kiosk-home']);
            $table->integer('priority')->default(0);
            $table->enum('section', ['tourism', 'active', 'gastronomy'])->nullable(); // добавлено для фильтрации при placement=section
            $table->timestamp('active_from')->nullable();
            $table->timestamp('active_until')->nullable();
            $table->string('title');
            $table->string('teaser');
            $table->enum('target_type', ['place', 'section', 'external']);
            $table->string('target_slug')->nullable(); // для place или section
            $table->string('target_url')->nullable(); // для external
            $table->timestamps();

            $table->index('placement');
            $table->index('priority');
            $table->index('active_from');
            $table->index('active_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
