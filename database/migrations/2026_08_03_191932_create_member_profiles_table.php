<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('first_name', 100);
            $table->string('last_name', 100);

            $table->string('nni', 20)
                ->nullable()
                ->unique();

            $table->enum('gender', [
                'male',
                'female',
                'unspecified',
            ])->nullable();

            $table->date('birth_date')->nullable();

            $table->foreignId('region_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('moughataa')->nullable();
            $table->string('commune')->nullable();
            $table->string('locality')->nullable();
            $table->text('address')->nullable();

            $table->string('photo_path')->nullable();

            $table->enum('employment_status', [
                'employed',
                'unemployed',
                'student',
                'retired',
                'other',
                'unspecified',
            ])->default('unspecified');

            $table->enum('employment_sector', [
                'public',
                'private',
                'self_employed',
                'ngo',
                'other',
                'unspecified',
            ])->default('unspecified');

            $table->string('function')->nullable();
            $table->string('position')->nullable();
            $table->string('employer')->nullable();

            $table->text('other_employment_details')->nullable();

            $table->boolean('profile_completed')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};