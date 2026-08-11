<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Compteur annuel utilisé pour générer les matricules (ER-{année}-{n})
     * de façon atomique, sans risque de doublon en cas de validations
     * concurrentes.
     */
    public function up(): void
    {
        Schema::create('matricule_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matricule_sequences');
    }
};
