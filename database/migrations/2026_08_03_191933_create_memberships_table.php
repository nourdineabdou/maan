<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('registration_number', 50)
                ->unique();

            $table->string('member_number', 50)
                ->nullable()
                ->unique();

            $table->enum('status', [
                'draft',
                'pending',
                'under_review',
                'to_complete',
                'approved',
                'rejected',
                'suspended',
                'cancelled',
            ])->default('draft');

            $table->text('member_message')->nullable();
            $table->text('population_needs')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('rejection_reason')->nullable();
            $table->text('completion_request')->nullable();
            $table->text('administrator_notes')->nullable();

            $table->string('qr_token', 100)
                ->nullable()
                ->unique();

            $table->timestamp('card_generated_at')->nullable();
            $table->boolean('card_is_active')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'submitted_at']);
            $table->index(['approved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};