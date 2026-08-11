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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type', 60)->after('id');

            // Titre et message traduits {"fr": "...", "ar": "..."}
            $table->json('title');
            $table->json('message');

            $table->string('action_url')->nullable();
            $table->json('data')->nullable();

            $table->nullableMorphs('notifiable');

            $table->foreignId('message_campaign_id')
                ->nullable()
                ->constrained('message_campaigns')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('message_campaign_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn([
                'type',
                'title',
                'message',
                'action_url',
                'data',
                'notifiable_type',
                'notifiable_id',
            ]);
        });
    }
};
