<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * D'où le compte a été créé (ios / android / web). Sert au back-office à
 * repérer les comptes créés depuis l'app iOS, qui ne collecte ni NNI ni
 * carte d'identité (App Store 5.1.1(ix)), pour pouvoir demander ces pièces
 * manuellement au membre.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_platform', 10)->nullable()->after('preferred_locale');
        });

        // Rattrapage approximatif des comptes existants à partir du nom des
        // jetons API ("mobile:<user-agent>") encore présents : l'app iOS
        // s'annonce via CFNetwork/Darwin, l'app Android via okhttp.
        $tokens = DB::table('personal_access_tokens')
            ->where('tokenable_type', 'App\\Models\\User')
            ->where('name', 'like', 'mobile:%')
            ->orderBy('id')
            ->get(['tokenable_id', 'name']);

        foreach ($tokens->groupBy('tokenable_id') as $userId => $userTokens) {
            $name = $userTokens->first()->name;

            $platform = match (true) {
                str_contains($name, 'Darwin') || str_contains($name, 'CFNetwork') => 'ios',
                str_contains($name, 'okhttp') || str_contains($name, 'Android') => 'android',
                default => null,
            };

            if ($platform) {
                DB::table('users')->where('id', $userId)->whereNull('registration_platform')
                    ->update(['registration_platform' => $platform]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('registration_platform');
        });
    }
};
