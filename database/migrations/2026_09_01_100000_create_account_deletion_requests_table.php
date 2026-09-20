<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_deletion_requests', function (Blueprint $table) {
            $table->id();

            // Formulaire public, non authentifié (l'utilisateur peut avoir
            // désinstallé l'app ou perdu l'accès à son compte) : pas de
            // user_id, juste les informations qu'il renseigne lui-même.
            $table->string('full_name');
            $table->string('contact');
            $table->string('member_number')->nullable();
            $table->text('reason')->nullable();

            $table->enum('status', ['pending', 'processed'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_deletion_requests');
    }
};
