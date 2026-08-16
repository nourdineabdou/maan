<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jusqu'ici une problématique déclarée par un membre n'avait aucun
     * statut : impossible de savoir si l'administration l'a seulement lue,
     * est en train de la traiter, ou l'a résolue. `submitted` par défaut
     * pour que les lignes déjà en base restent cohérentes.
     */
    public function up(): void
    {
        Schema::table('membership_problematics', function ($table) {
            $table->enum('status', ['submitted', 'in_progress', 'resolved'])
                ->default('submitted')
                ->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('membership_problematics', function ($table) {
            $table->dropColumn('status');
        });
    }
};
