<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_problematics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('problematic_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('description')->nullable();
            $table->text('requested_solution')->nullable();
            $table->string('locality')->nullable();

            $table->enum('priority', [
                'low',
                'normal',
                'high',
                'urgent',
            ])->default('normal');

            $table->timestamps();

            $table->unique([
                'membership_id',
                'problematic_id',
            ], 'membership_problematic_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_problematics');
    }
};