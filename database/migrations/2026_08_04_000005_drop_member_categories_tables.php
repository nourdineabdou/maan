<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * La notion de "catégorie de membre" (acteur politique, parti
     * politique, association...) a été retirée du périmètre fonctionnel.
     */
    public function up(): void
    {
        Schema::dropIfExists('membership_categories');
        Schema::dropIfExists('member_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('member_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->boolean('requires_details')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('membership_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_category_id')->constrained()->cascadeOnDelete();
            $table->string('details')->nullable();
            $table->timestamps();
            $table->unique(['membership_id', 'member_category_id'], 'membership_category_unique');
        });
    }
};
