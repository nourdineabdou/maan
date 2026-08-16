<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Référence polymorphique optionnelle vers la déclaration (problématique
     * ou besoin) dont discute ce fil, pour que le membre et l'admin sachent
     * de quoi ils parlent sans avoir à le répéter dans le sujet/corps du
     * message. Nullable : la majorité des fils de support restent des
     * échanges génériques sans rapport avec une déclaration précise.
     */
    public function up(): void
    {
        Schema::table('member_messages', function (Blueprint $table) {
            $table->nullableMorphs('related');
        });
    }

    public function down(): void
    {
        Schema::table('member_messages', function (Blueprint $table) {
            $table->dropMorphs('related');
        });
    }
};
