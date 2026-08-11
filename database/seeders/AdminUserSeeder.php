<?php

namespace Database\Seeders;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@ensemble-republique.test');
        $password = env('ADMIN_PASSWORD', 'password');

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrateur',
                'phone' => '22200000000',
                'password' => Hash::make($password),
                'preferred_locale' => 'fr',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('administrateur')) {
            $admin->assignRole('administrateur');
        }

        MemberProfile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'first_name' => 'Administrateur',
                'last_name' => 'Plateforme',
                'gender' => 'unspecified',
                'profile_completed' => true,
            ]
        );
    }
}
