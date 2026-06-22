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
        Schema::create('place_tag', function (Blueprint $table) {
            $table->uuid('place_id');
            $table->uuid('tag_id');
            $table->primary(['place_id', 'tag_id']);

            $table->foreign('place_id')->references('id')->on('places')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_tag');
    }
};
