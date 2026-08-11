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
        Schema::table('notification_recipients', function (Blueprint $table) {
            $table->foreignId('notification_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('channel', [
                'internal',
                'email',
                'sms',
                'whatsapp',
            ])->default('internal');

            $table->enum('status', [
                'pending',
                'sent',
                'delivered',
                'failed',
            ])->default('pending');

            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();

            $table->unique(['notification_id', 'user_id', 'channel'], 'notif_recipient_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_recipients', function (Blueprint $table) {
            $table->dropUnique('notif_recipient_unique');
            $table->dropConstrainedForeignId('notification_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['channel', 'status', 'delivered_at', 'read_at']);
        });
    }
};
