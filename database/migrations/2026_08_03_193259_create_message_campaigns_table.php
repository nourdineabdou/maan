<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_campaigns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Titres traduits
            $table->json('title');

            // Messages traduits
            $table->json('message');

            $table->enum('target_type', [
                'all',
                'selected_members',
                'region',
                'category',
                'membership_status',
            ])->default('all');

            $table->json('target_filters')->nullable();

            $table->enum('channel', [
                'internal',
                'email',
                'sms',
                'whatsapp',
            ])->default('internal');

            $table->enum('status', [
                'draft',
                'scheduled',
                'sending',
                'sent',
                'failed',
            ])->default('draft');

            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_campaigns');
    }
};