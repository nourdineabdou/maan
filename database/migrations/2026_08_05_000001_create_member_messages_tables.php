<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Messagerie membre -> administration : un membre ouvre un fil de
     * discussion, l'administration y répond. Sens inverse de
     * `notifications` (qui est admin -> membres uniquement).
     */
    public function up(): void
    {
        Schema::create('member_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('subject');
            $table->text('body');

            $table->enum('status', ['open', 'answered', 'closed'])->default('open');

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('member_message_replies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_message_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('body');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_message_replies');
        Schema::dropIfExists('member_messages');
    }
};
