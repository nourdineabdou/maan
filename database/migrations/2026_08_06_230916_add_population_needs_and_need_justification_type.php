<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Besoin de la population" (texte libre, colonne `memberships.population_needs`
     * déjà présente depuis la migration initiale mais jusqu'ici sans aucune
     * interface) a besoin de son propre type de justificatif, distinct de
     * `problematic_justification` : ce sont deux fonctionnalités séparées.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE member_documents MODIFY document_type ENUM(
            'identity_card_front',
            'identity_card_back',
            'diploma',
            'member_photo',
            'cv',
            'problematic_justification',
            'need_justification',
            'other'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE member_documents SET document_type = 'other' WHERE document_type = 'need_justification'");

        DB::statement("ALTER TABLE member_documents MODIFY document_type ENUM(
            'identity_card_front',
            'identity_card_back',
            'diploma',
            'member_photo',
            'cv',
            'problematic_justification',
            'other'
        )");
    }
};
