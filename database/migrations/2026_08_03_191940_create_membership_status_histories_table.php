<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30);

            $table->text('comment')->nullable();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'membership_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_status_histories');
    }
};