<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Le workflow d'adhésion est simplifié à 3 statuts : en attente,
     * validé, rejeté (au lieu des 8 statuts détaillés précédents).
     */
    public function up(): void
    {
        DB::statement("UPDATE memberships SET status = 'pending' WHERE status NOT IN ('pending', 'approved', 'rejected')");

        DB::statement("ALTER TABLE memberships MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE memberships MODIFY status ENUM('draft', 'pending', 'under_review', 'to_complete', 'approved', 'rejected', 'suspended', 'cancelled') NOT NULL DEFAULT 'draft'");
    }
};
