<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->foreignId('moughataa_id')
                ->nullable()
                ->after('region_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('commune_id')
                ->nullable()
                ->after('moughataa_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moughataa_id');
            $table->dropConstrainedForeignId('commune_id');
        });
    }
};
