<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `memberships.population_needs` était un champ texte unique : un membre
     * ne pouvait déclarer qu'un seul besoin, sans suivi de statut possible.
     * On le remplace par une table `membership_needs` (même forme que
     * `membership_problematics`, sans catalogue de types car les besoins
     * restent en texte libre) qui autorise plusieurs déclarations, chacune
     * avec son propre statut.
     */
    public function up(): void
    {
        Schema::create('membership_needs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('description');

            $table->enum('status', ['submitted', 'in_progress', 'resolved'])
                ->default('submitted');

            $table->timestamps();
        });

        DB::table('memberships')
            ->whereNotNull('population_needs')
            ->where('population_needs', '!=', '')
            ->orderBy('id')
            ->get()
            ->each(function ($membership) {
                DB::table('membership_needs')->insert([
                    'membership_id' => $membership->id,
                    'description' => $membership->population_needs,
                    'status' => 'submitted',
                    'created_at' => $membership->updated_at,
                    'updated_at' => $membership->updated_at,
                ]);
            });

        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('population_needs');
        });
    }

    /**
     * Reverse best-effort : recrée la colonne et n'y remet que le besoin le
     * plus récent de chaque membership (le format à champ unique ne peut de
     * toute façon pas représenter plusieurs déclarations).
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->text('population_needs')->nullable()->after('member_message');
        });

        DB::table('membership_needs')
            ->orderByDesc('id')
            ->get()
            ->groupBy('membership_id')
            ->each(function ($needs, $membershipId) {
                DB::table('memberships')
                    ->where('id', $membershipId)
                    ->update(['population_needs' => $needs->first()->description]);
            });

        Schema::dropIfExists('membership_needs');
    }
};
