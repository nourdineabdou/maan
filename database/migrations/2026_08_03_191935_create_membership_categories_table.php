<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('member_category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('details')->nullable();

            $table->timestamps();

            $table->unique([
                'membership_id',
                'member_category_id',
            ], 'membership_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_categories');
    }
};