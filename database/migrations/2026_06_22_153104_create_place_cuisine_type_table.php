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
        Schema::create('place_cuisine_type', function (Blueprint $table) {
            $table->uuid('place_id');
            $table->uuid('cuisine_type_id');
            $table->primary(['place_id', 'cuisine_type_id']);

            $table->foreign('place_id')->references('id')->on('places')->cascadeOnDelete();
            $table->foreign('cuisine_type_id')->references('id')->on('cuisine_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_cuisine_type');
    }
};
