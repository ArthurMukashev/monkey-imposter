<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'section_enum') THEN
        CREATE TYPE section_enum AS ENUM ('tourism', 'active', 'gastronomy');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'placement_enum') THEN
        CREATE TYPE placement_enum AS ENUM ('home', 'section', 'place-details', 'kiosk-home');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'target_type_enum') THEN
        CREATE TYPE target_type_enum AS ENUM ('place', 'section', 'external');
    END IF;
END
$$;
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TYPE IF EXISTS section_enum, placement_enum, target_type_enum CASCADE');
    }
};
