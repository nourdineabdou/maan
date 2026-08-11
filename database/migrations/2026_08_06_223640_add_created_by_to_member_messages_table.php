<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingue qui a écrit le premier message d'un fil (le membre, comme
     * jusqu'ici, ou l'administration si elle prend l'initiative de
     * contacter un membre) — nécessaire pour afficher le bon auteur, `user_id`
     * ne désignant que le membre propriétaire du fil, pas forcément l'auteur.
     */
    public function up(): void
    {
        Schema::table('member_messages', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        DB::table('member_messages')->whereNull('created_by')->update(['created_by' => DB::raw('user_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
};
