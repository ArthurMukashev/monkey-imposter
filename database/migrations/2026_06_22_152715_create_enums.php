<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE TYPE section_enum AS ENUM ('tourism', 'active', 'gastronomy')");
        DB::statement("CREATE TYPE placement_enum AS ENUM ('home', 'section', 'place-details', 'kiosk-home')");
        DB::statement("CREATE TYPE target_type_enum AS ENUM ('place', 'section', 'external')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TYPE section_enum");
        DB::statement("DROP TYPE placement_enum");
        DB::statement("DROP TYPE target_type_enum");
    }
};
