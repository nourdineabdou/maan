<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problematics', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();

            // {"fr":"Éducation","ar":"التعليم"}
            $table->json('name');

            $table->json('description')->nullable();

            $table->string('icon')->nullable();
            $table->boolean('requires_justification')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problematics');
    }
};