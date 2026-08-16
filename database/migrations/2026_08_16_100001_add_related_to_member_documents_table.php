<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jusqu'ici un justificatif "besoin"/"problématique" était seulement
     * tagué par `document_type` (`need_justification`/`problematic_justification`),
     * partagé en vrac entre toutes les déclarations d'un membership — impossible
     * de savoir quel fichier appartient à quelle déclaration précise. Même
     * mécanisme polymorphique optionnel que `member_messages.related_*`
     * (ajouté dans une précédente migration) pour rester cohérent.
     */
    public function up(): void
    {
        Schema::table('member_documents', function (Blueprint $table) {
            $table->nullableMorphs('related');
        });
    }

    public function down(): void
    {
        Schema::table('member_documents', function (Blueprint $table) {
            $table->dropMorphs('related');
        });
    }
};
