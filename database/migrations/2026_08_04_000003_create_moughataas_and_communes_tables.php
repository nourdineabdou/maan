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
        Schema::create('moughataas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('region_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('code', 20);
            $table->json('name');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['region_id', 'code']);
        });

        Schema::create('communes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('moughataa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('code', 20);
            $table->json('name');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['moughataa_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communes');
        Schema::dropIfExists('moughataas');
    }
};
