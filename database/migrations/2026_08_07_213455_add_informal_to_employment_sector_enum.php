<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE member_profiles MODIFY employment_sector ENUM(
            'public',
            'private',
            'informal',
            'self_employed',
            'ngo',
            'other',
            'unspecified'
        ) NOT NULL DEFAULT 'unspecified'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE member_profiles SET employment_sector = 'other' WHERE employment_sector = 'informal'");

        DB::statement("ALTER TABLE member_profiles MODIFY employment_sector ENUM(
            'public',
            'private',
            'self_employed',
            'ngo',
            'other',
            'unspecified'
        ) NOT NULL DEFAULT 'unspecified'");
    }
};
