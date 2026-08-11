<?php

namespace Database\Seeders;

use App\Models\MemberDocument;
use App\Models\MemberProfile;
use App\Models\Membership;
use App\Models\Region;
use App\Models\User;
use App\Services\MembershipApprovalService;
use App\Services\RegistrationNumberGeneratorService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            RegionSeeder::class,
            MoughataaSeeder::class,
            ProblematicSeeder::class,
            AdminUserSeeder::class,
        ]);

        // Compte membre de démonstration (dev uniquement) pour tester la
        // connexion par e-mail ET par téléphone.
        $member = User::firstOrCreate(
            ['email' => 'membre@ensemble-republique.test'],
            [
                'name' => 'Membre Test',
                'phone' => '22212345678',
                'password' => Hash::make('password'),
                'preferred_locale' => 'fr',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (! $member->hasRole('membre')) {
            $member->assignRole('membre');
        }

        MemberProfile::firstOrCreate(
            ['user_id' => $member->id],
            [
                'first_name' => 'Membre',
                'last_name' => 'Test',
                'gender' => 'unspecified',
            ]
        );

        // Adhésion approuvée de démonstration, pour pouvoir afficher/tester
        // la carte de membre sans avoir encore construit le parcours
        // d'adhésion ni l'écran de validation admin.
        $membership = Membership::firstOrCreate(
            ['user_id' => $member->id],
            [
                'registration_number' => app(RegistrationNumberGeneratorService::class)->generate(),
                'status' => 'pending',
                'submitted_at' => now(),
            ]
        );

        if ($membership->status !== 'approved') {
            app(MembershipApprovalService::class)->approve($membership, $member);
        }

        // Deux demandes en attente de démonstration, pour pouvoir tester
        // l'écran de validation admin (valider / rejeter) de bout en bout.
        $this->seedPendingMembership(
            email: 'aissata.ba@ensemble-republique.test',
            phone: '22233333333',
            name: 'Aissata Ba',
            firstName: 'Aissata',
            lastName: 'Ba',
            gender: 'female',
            regionCode: 'TRZ',
            nni: '1122334455',
            employmentStatus: 'employed',
            employmentSector: 'public',
            function: 'Enseignante',
        );

        $this->seedPendingMembership(
            email: 'mohamed.salem@ensemble-republique.test',
            phone: '22244444444',
            name: 'Mohamed Salem',
            firstName: 'Mohamed',
            lastName: 'Salem',
            gender: 'male',
            regionCode: 'NKO',
            nni: '2233445566',
            employmentStatus: 'unemployed',
            employmentSector: 'unspecified',
            function: null,
        );

        // Document de démonstration, pour tester l'écran admin de
        // vérification des documents.
        $logoPath = public_path('logo_fr.png');

        if (is_file($logoPath) && ! Storage::disk('public')->exists('documents/demo-cni.png')) {
            Storage::disk('public')->put('documents/demo-cni.png', file_get_contents($logoPath));
        }

        MemberDocument::firstOrCreate(
            ['membership_id' => $membership->id, 'document_type' => 'identity_card_front'],
            [
                'title' => 'Carte nationale d\'identité (recto)',
                'file_path' => 'documents/demo-cni.png',
                'original_name' => 'cni-recto.png',
                'mime_type' => 'image/png',
                'file_size' => Storage::disk('public')->size('documents/demo-cni.png'),
                'is_required' => true,
                'is_verified' => false,
            ]
        );
    }

    private function seedPendingMembership(
        string $email,
        string $phone,
        string $name,
        string $firstName,
        string $lastName,
        string $gender,
        string $regionCode,
        string $nni,
        string $employmentStatus,
        string $employmentSector,
        ?string $function,
    ): void {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'phone' => $phone,
                'password' => Hash::make('password'),
                'preferred_locale' => 'fr',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole('membre')) {
            $user->assignRole('membre');
        }

        MemberProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'nni' => $nni,
                'gender' => $gender,
                'region_id' => Region::where('code', $regionCode)->value('id'),
                'employment_status' => $employmentStatus,
                'employment_sector' => $employmentSector,
                'function' => $function,
            ]
        );

        Membership::firstOrCreate(
            ['user_id' => $user->id],
            [
                'registration_number' => app(RegistrationNumberGeneratorService::class)->generate(),
                'status' => 'pending',
                'submitted_at' => now(),
            ]
        );
    }
}
