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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)
                ->nullable()
                ->unique()
                ->after('email');

            $table->string('preferred_locale', 5)
                ->default('fr')
                ->after('password');

            $table->boolean('is_active')
                ->default(true)
                ->after('preferred_locale');

            $table->timestamp('phone_verified_at')
                ->nullable()
                ->after('email_verified_at');

            $table->timestamp('last_login_at')
                ->nullable()
                ->after('phone_verified_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'preferred_locale',
                'is_active',
                'phone_verified_at',
                'last_login_at',
            ]);
        });
    }
};
